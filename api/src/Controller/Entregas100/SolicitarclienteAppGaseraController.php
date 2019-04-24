<?php

namespace App\Controller\Entregas100;

use Exception;

class SolicitarclienteAppGaseraController extends AppGaseraController {

    public function solicitarcliente_fn() {
        $nParametrosFn = 1;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene la conexion a la bd de la plaza
        $oConexion = $this->getConexion();
        $sQuery = "SELECT plaza.*, " .
                "conexion.ip_te, " .
                "conexion.usuario_te, " .
                "conexion.password_te, " .
                "conexion.base_te " .
            "FROM plaza " .
            "INNER JOIN conexion ON plaza.id = conexion.plaza_id " .
            "WHERE plaza.id = ?";
        $aQueryParams = array($aUnidad['plaza_id']);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        // si no hay info de la plaza se termina el proceso
        if (count($aResultado) <= 0) {
            throw new Exception("Error al obtener los datos de conexion a la plaza");
        }
        $aPlaza = $aResultado[0];
        $oConexionPlaza = $this->getConexion(
            $aPlaza["plaza"],
            array(
                "host" => $aPlaza["ip_te"],
                "user" => $aPlaza["usuario_te"],
                "password" => $aPlaza["password_te"],
                "database" => $aPlaza["base_te"]
            )
        );

        $dFechaPadron = date("Y-m-d");
        $nNumeroControl = $aDatos["numero_control"];

        // si existe la tabla padron_app_<unidad> y es la pagina 0, se elimina la tabla para crearla de nuevo
        // esto para refrescar la tabla que por casualidad no se haya eliminado al terminar de descargar
        // el padron
        $sQuery = "DROP TABLE IF EXISTS padron_app_" . $aUnidad["unidad"];
        $oConexionPlaza->query($sQuery);

        // si no existe la tabla padron_app_<unidad> se crea, es una tabla real
        // la cual debera eliminarse al terminar de descargar el padron
        // genera la lista de trabajo en una tabla temporal
        $sQuery = "CREATE TEMPORARY TABLE lista_app AS (" .
            SolicitarlistaAppGaseraController::getListaQueryString() .
            ")";
        $aQueryParams = array($aPlaza["otorga_puntos"], $aUnidad["plaza_id"], $dFechaPadron, $dFechaPadron);
        $oConexionPlaza->query($sQuery, $aQueryParams);

        // genera el padron combinado con la lista en una tabla
        $sQuery = "CREATE TABLE padron_app_" . $aUnidad["unidad"] . " AS (" .
            self::getPadronQueryString() .
            ")";
        $aQueryParams = array($aPlaza["otorga_puntos"], $aUnidad["plaza_id"], $nNumeroControl);
        $oConexionPlaza->query($sQuery, $aQueryParams);

        // obtiene la pagina del padron
        $sQuery = "SELECT * " .
            "FROM padron_app_" . $aUnidad["unidad"] . " " .
            "WHERE numero_control = ?";
        $aQueryParams = array($nNumeroControl);
        $aResultado = $oConexionPlaza->query($sQuery, $aQueryParams);

        // elimina la tabla generada para descargar el padron para no tener basura en la bd
        $sQuery = "DROP TABLE IF EXISTS padron_app_" . $aUnidad["unidad"];
        $oConexionPlaza->query($sQuery);

        if (count($aResultado) <= 0) {
            return $this->asJson(array(
                "success" => false,
                "message" => "No se encontro el cliente",
                "metadata" => array(
                    "Registros" => 0,
                ),
                "data" => array()
            ));
        }

        $aCliente = $this->procesarClientesPadron($aResultado);

        return $this->asJson(array(
            "success" => true,
            "message" => "Cliente encontrado",
            "data" => $aCliente,
        ));
    }

    /**
     * Arma y retorna la query para obtener el padron
     * @return string
     */
    public static function getPadronQueryString() {
        return "SELECT DISTINCT " .
            "listas_padron.ncontrol AS numero_control, " .
            "listas_padron.nombre AS nombre_cliente, " .
            "listas_padron.capacid AS capacidad_tanque, " .
            "listas_padron.descuento_centavos AS descuento_centavos, " .
            "listas_padron.surtido_multiple AS surtido_multiple, " .
            "listas_padron.plazo_credito AS plazo_credito, " .
            "listas_padron.status_credito AS status_credito, " .
            "listas_padron.credito_disponible AS credito_disponible, " .
            "listas_padron.tipo_carburacion AS tipo_carburacion, " .
            "listas_padron.puntos_acumulados AS puntos_acumulados, " .
            "listas_padron.latitud AS latitud, " .
            "listas_padron.longitud AS longitud, " .
            "listas_padron.fecha_fabricacion AS fecha_fabricacion, " .
            "padron.operacion AS factor_calibracion, " .
            "listas_padron.calle as calle_id, " .
            "listas_padron.colonia as colonia_id, " .
            "listas_padron.ncasa AS numero_exterior, " .
            "50 AS distancia_permitir_surtir, " .
            "listas_padron.clave_surtido AS clave_credito, " .
            "padron.cuenta_cxc AS cuenta_credito, " .
            "listas_padron.fecvta AS fecha_clave, " .
            "listas_padron.zona AS zona, " .
            "\"\" AS numero_interior, " .
            "0 AS tipo_compromiso_id, " .
            "? AS plaza_otorga_puntos, " .
            "? AS plaza_id, " .
            // identifica si el servicio es programado
            "IF(" .
                "lista_app.es_programado IS NULL, " .
                "0, " .
                "lista_app.es_programado" .
            ") AS es_programado, " .
            // identifica el tipo de compromiso programado
            "IF(" .
                "lista_app.tipo_programado IS NULL, " .
                "\"EMERGENCIA\", " .
                "lista_app.tipo_programado" .
            ") AS tipo_programado, " .
            // identifica el tipo de compromiso de emergencia
            "IF(" .
                "lista_app.tipo_emergencia IS NULL, " .
                "\"EMERGENCIA\", " .
                "lista_app.tipo_emergencia" .
            ") AS tipo_emergencia, " .
            // si no hay hora en lista la hora de preferencia sera la del padron
            "IF(" .
                "lista_app.hora_pref1 IS NULL, " .
                "padron.hora_pref1, " .
                "lista_app.hora_pref1" .
            ") AS hora_pref1, " .
            // si no hay hora en lista la hora de preferencia sera la del padron
            "IF(" .
                "lista_app.hora_pref2 IS NULL, " .
                "padron.hora_pref2, " .
                "lista_app.hora_pref2" .
            ") AS hora_pref2, " .
            // se asigna el perfil de pago
            "CASE " .
                "WHEN padron.tresm = 1 " .
                    "THEN 3 " . // llenafacil
                "WHEN padron.filial = 1 " .
                    "THEN 7 " . // filial
                "WHEN listas_padron.status_credito = \"A\" AND listas_padron.credito_disponible > 0 OR listas_padron.status_credito = \"S\" " .
                    "THEN 2 " . // credito
                "WHEN listas_padron.tipo_cte = 14 " .
                    "THEN 4 " . // consignacion
                "WHEN listas_padron.tipo_cte = 15 " .
                    "THEN 6 " . // donativo
                "WHEN listas_padron.tipo_cte = 16 " .
                    "THEN 5 " . // cortesia
                "WHEN listas_padron.tipo_cte IN (1, 7, 17) " .
                    "THEN 1 " . // general domestico
                "ELSE 10 " . // general comercial
            "END " .
            "AS tipo_cv_id, " .
            // se verifica si el cliente tiene nombre para facturacion
            "IF (" .
                "dat_fact.nombre IS NULL OR TRIM(dat_fact.nombre) = \"\", " .
                "\"\", " .
                "dat_fact.nombre" .
            ") AS nombre_facturacion, " .
            // se verifica si el cliente tiene domicilio para facturacion
            "IF (" .
                "dat_fact.domicilio IS NULL OR TRIM(dat_fact.domicilio) = \"\", " .
                "\"\", " .
                "dat_fact.domicilio" .
            ") AS domicilio_facturacion, " .
            // se verifica si el tipo de cliente es comercial
            "IF(" .
                "listas_padron.tipo_cte IN (1, 7, 17), " .
                "0, " .
                "1" .
            ") AS tipo_cliente, " .
            "IF(" .
                "tarifas.cvetar IS NULL, " .
                "0, " .
                "tarifas.cvetar" .
            ") AS tarifa_id " .
        "FROM listas_padron " .
        "LEFT JOIN lista_app ON listas_padron.ncontrol = lista_app.numero_control " .
        "LEFT JOIN padron ON listas_padron.ncontrol = padron.ncontrol " .
        "LEFT JOIN tarifas ON tarifas.cvetar = padron.tarifa " .
        "LEFT JOIN dat_fact ON listas_padron.ncontrol = dat_fact.ncontrol " .
        "WHERE listas_padron.ncontrol = ? " .
        "GROUP BY listas_padron.ncontrol " .
        "ORDER BY numero_control";
    }

    /**
     * Procesa la informacion del padron para que pueda ser usada por la appgasera
     *
     * @param  array $aListaTrabajo
     * @return array Padron procesado
     */
    public function procesarClientesPadron($aPadron) {
        $aPadronProcesado = array();

        foreach ($aPadron as &$aServicio) {
            // asigna el compromiso de acuerdo a los criterios
            $aServicio["compromiso"] = $aServicio["es_programado"] ?
                $aServicio["tipo_programado"] :
                $aServicio["tipo_emergencia"];

            // asigna tipo_compromiso_id de acuerdo a los criterios
            if ($aServicio["es_programado"]) {
                switch ($aServicio["tipo_programado"]) {
                    case "OPERADORA":
                        $aServicio["tipo_compromiso_id"] = 4;
                        break;
                    case "CHOFER":
                        $aServicio["tipo_compromiso_id"] = 1;
                        break;
                    case "TELEMARKETING":
                        $aServicio["tipo_compromiso_id"] = 6;
                        break;
                    default: // EMERGENCIA
                        $aServicio["tipo_compromiso_id"] = 2;
                        break;
                }
            } else {
                // EMERGENCIA
                $aServicio["tipo_compromiso_id"] = 2;
            }

            // asigna otorga_puntos de acuerdo a los criterios
            if (
                $aServicio["plaza_otorga_puntos"] &&    // la plaza otorga puntos
                $aServicio["es_programado"] &&          // es servicios programado
                $aServicio["tipo_cliente"] == 0 &&      // es cliente domestico
                (
                    !in_array($aServicio["plaza_id"], [16, 17]) ||    // si las plaza no es san jose o san lucas
                    $aServicio['capacidad_tanque'] <= 300           // si es san jose o san lucas, la capacidad debera ser menor a 300 litros
                )
            ) {
                $aServicio["otorga_puntos"] = 1;
            } else {
                $aServicio["otorga_puntos"] = 0;
            }

            $aPadronProcesado[] = array(
                "_1" => $aServicio['numero_control'], //numero_control
                "_2" => utf8_encode($aServicio['nombre_cliente']), //nombre_cliente
                "_3" => $aServicio['capacidad_tanque'], //capacidad_tanque
                "_4" => $aServicio['descuento_centavos'], //descuento_centavos
                //"_5" => $aServicio['precio_aditivo'], //precio_aditivo
                //"_6" => $aServicio['precio_gas'], //precio_gas
                "_7" => $aServicio['surtido_multiple'], //surtido_multiple
                "_8" => $aServicio['plazo_credito'], //plazo_credito
                "_9" => $aServicio['status_credito'], //status_credito
                "_10" => $aServicio['credito_disponible'], //credito_disponible
                // "lista" => $s_valor['lista']
                "_11" => $aServicio['tipo_carburacion'], //tipo_carburacion
                "_12" => $aServicio['puntos_acumulados'], //puntos_acumulados
                "_13" => 0, // $aServicio['latitud'], //latitud
                "_14" => 0, // $aServicio['longitud'], //longitud
                "_15" => $aServicio['fecha_fabricacion'], //fecha_fabricacion
                "_16" => $aServicio['factor_calibracion'], //factor_calibracion
                "_17" => $aServicio["compromiso"], //tipo_cliente
                "_18" => (!empty($aServicio['hora_pref1'])) ? $aServicio['hora_pref1'] : 0, //hora_pref1
                "_19" => (!empty($aServicio['hora_pref2'])) ? $aServicio['hora_pref2'] : 0, //hora_pref2
                "_20" => $aServicio['calle_id'], //calle_id
                "_21" => $aServicio['colonia_id'], //colonia_id
                "_22" => $aServicio['numero_exterior'], //ncasa
                "_23" => $aServicio["tipo_cv_id"], //tipo_pago_id
                "_24" => $aServicio["otorga_puntos"], //otorga_puntos
                "_25" => (!empty($aServicio['clave_credito'])) ? $aServicio['clave_credito'] : '', //clave de autorizacion
                "_26" => $aServicio["tipo_cliente"], //tipo de cliente, identificando si es domestico o comercial
                "_27" => (!empty($aServicio['cuenta_credito'])) ? $aServicio['cuenta_credito'] : '', //cuenta de crédito
                "_28" => (empty($aServicio['fecha_clave']) || $aServicio['fecha_clave'] == "0000-00-00") ? "" : $aServicio['fecha_clave'],//Fecha de uso de clave de autorizacion
                "_29" => (!empty($aServicio['zona_id'])) ? $aServicio['zona_id'] : '',
                "_30" => (!empty($aServicio['tarifa_id'])) ? $aServicio['tarifa_id'] : '',
                "_31" => (!empty($aServicio['tipo_compromiso_id'])) ? $aServicio['tipo_compromiso_id'] : 2,
                "_32" => $aServicio['numero_interior'], //numero_interior
                "_33" => $aServicio["nombre_facturacion"], // nombre facturacion
                "_34" => $aServicio["domicilio_facturacion"], // domicilio facturacion
                "_35" => $aServicio["distancia_permitir_surtir"] // distancia para permitir surtir
            );
        }
        unset($aServicio);

        return $aPadronProcesado;
    }
}
