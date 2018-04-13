<?php
App::uses('SolicitarlistaController', 'Controller');

class SolicitarpadronAppGaseraController extends AppGaseraController {

    public function solicitarpadron_fn() {
        $nParametrosFn = 1;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene la conexion a la bd de la plaza
        $oConexion = $this->conexion();
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
        $aResultado = $this->parsearQueryResult($aResultado);
        // si no hay info de la plaza se termina el proceso
        if (count($aResultado) <= 0) {
            throw new Exception("Error al obtener los datos de conexion a la plaza");
        }
        $aPlaza = $aResultado[0];
        $oConexionPlaza = $this->conexion(
            $aPlaza["plaza"],
            array(
                "host" => $aPlaza["ip_te"],
                "user" => $aPlaza["usuario_te"],
                "password" => $aPlaza["password_te"],
                "database" => $aPlaza["base_te"]
            )
        );

        $dFechaPadron = date("Y-m-d");
        $nPagina = $aDatos["pagina"];
        $nTamanoPagina = intval($aPlaza["limite_descarga"]);
        $nOffset = intval($nPagina * $nTamanoPagina);

        // genera la lista de trabajo en una tabla temporal
        $sQuery = "CREATE TEMPORARY TABLE lista_app AS (" .
            SolicitarlistaController::getListaQueryString() .
            ")";
        $aQueryParams = array($aPlaza["otorga_puntos"], $dFechaPadron, $dFechaPadron);
        $oConexionPlaza->query($sQuery, $aQueryParams);

        // genera el padron combinado con la lista en una tabla temporal
        $sQuery = "CREATE TEMPORARY TABLE padron_app AS (" .
            self::getPadronQueryString() .
            ")";
        $aQueryParams = array($aPlaza["otorga_puntos"]);
        $oConexionPlaza->query($sQuery, $aQueryParams);

        // obtiene el total del padron
        $sQuery = "SELECT COUNT(*) AS cantidad FROM padron_app";
        $aResultado = $oConexionPlaza->query($sQuery);
        $aResultado = $this->parsearQueryResult($aResultado);
        $nTotalPadron = intval($aResultado[0]["cantidad"]);
        $nVueltas = $nTamanoPagina > 0 ? round($nTotalPadron / $nTamanoPagina) : 0;

        // obtiene la pagina del padron
        $sQuery = "SELECT * " .
            "FROM padron_app " .
            "LIMIT ? OFFSET ?";
        $aQueryParams = array($nTamanoPagina, $nOffset);
        $aResultado = $oConexionPlaza->query($sQuery, $aQueryParams);
        $aPadron = $this->parsearQueryResult($aResultado);
        $nTotalPagina = count($aPadron);

        // si ya no existen mas clientes
        if ($nTotalPagina <= 0) {
            return $this->asJson(array(
                "success" => true,
                "message" => "No existen mas clientes",
                "metadata" => array(
                    "Registros" => 0,
                    "fecha_padron" => $dFechaPadron,
                    array(
                        "Registros" => 0,
                        "fecha_padron" => $dFechaPadron
                    )
                ),
                "data" => array()
            ));
        }

        $aPadron = $this->procesarClientesPadron($aPadron);

        return $this->asJson(array(
            "success" => true,
            "message" => "",
            "data" => $aPadron,
            "metadata" => array(
                "0" => array(
                    "Registros" => $nTotalPagina,
                    "Clientes" => $nTotalPadron,
                    "Vueltas" => $nVueltas,
                    "fecha_padron" => $dFechaPadron,
                ),
                "Registros" => $nTotalPagina,
                "Clientes" => $nTotalPadron,
                "Vueltas" => $nVueltas,
                "fecha_padron" => $dFechaPadron,
                "numero_control",
                "nombre_cliente",
                "capacidad_tanque",
                "descuento_centavos",
                "precio_aditivo",
                "precio_gas",
                "surtido_multiple",
                "plazo_credito",
                "status_credito",
                "credito_disponible",
                "tipo_carburacion",
                "puntos_acumulados",
                "latitud",
                "longitud",
                "fecha_fabricacion",
                "factor_calibracion",
                "tipo_cliente",
                "hora_pref1",
                "hora_pref2",
                "calle_id",
                "colonia_id",
                "ncasa",
                "tipo_pago_id",
                "otorga_puntos",
                "clave_autorizacion",
                "tipo_cliente",
                "cuenta_cxc",
                "fecha_clave",
                "zona",
                "clave_tarifa",
                "tipo_compromiso_id"
            )
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
            "listas_padron.clave_surtido AS clave_credito, " .
            "padron.cuenta_cxc AS cuenta_credito, " .
            "listas_padron.fecvta AS fecha_clave, " .
            "listas_padron.zona AS zona, " .
            "\"\" AS numero_interior, " .
            "0 AS tipo_compromiso_id, " .
            "? AS plaza_otorga_puntos, " .
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
                "ELSE 1 " . // general " .
            "END " .
            "AS tipo_cv_id, " .
            // se verifica si el tipo de cliente es comercial
            "IF(" .
                "listas_padron.tipo_cte IN (1, 7), " .
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
        "WHERE listas_padron.ncontrol != 0 " .
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

            // asigna otorga_puntos de acuerdo a los criterios
            if ($aServicio["plaza_otorga_puntos"] && $aServicio["es_programado"] && $aServicio["tipo_cliente"] == 0) {
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
                "_13" => $aServicio['latitud'], //latitud
                "_14" => $aServicio['longitud'], //longitud
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
                "_28" => (!empty($aServicio['fecha_clave'])) ? $aServicio['fecha_clave'] : '', //date('Y-m-d') //Fecha de uso de clave de autorización
                "_29" => (!empty($aServicio['zona_id'])) ? $aServicio['zona_id'] : '',
                "_30" => (!empty($aServicio['tarifa_id'])) ? $aServicio['tarifa_id'] : '',
                "_31" => (!empty($aServicio['tipo_compromiso_id'])) ? $aServicio['tipo_compromiso_id'] : 2,
                "_32" => $aServicio['numero_interior'] //numero_interior
            );
        }
        unset($aServicio);

        return $aPadronProcesado;
    }
}
