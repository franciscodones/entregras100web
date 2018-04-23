<?php

class SolicitarlistaAppGaseraController extends AppGaseraController {

    public function solicitarlista_fn(){
        $nParametrosFn = 0;
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

        $nTotalLista = 0;
        $dFechaLista = date("Y-m-d");

        // obtiene el horario nocturno de la zona en caso que tenga
        $sQuery = "SELECT * " .
            "FROM horario_zona " .
            "WHERE zona_id = ?";
        $aQueryParams = array($aUnidad["zona_id"]);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $aResultado = $this->parsearQueryResult($aResultado);
        $aHorarioNocturno = count($aResultado) > 0 ? $aResultado[0] : null;

        // genera la lista de trabajo en una tabla temporal
        $sQuery = "CREATE TEMPORARY TABLE lista_app AS (" .
            self::getListaQueryString() .
            ")";
        $aQueryParams = array($aPlaza["otorga_puntos"], $dFechaLista, $dFechaLista);
        $oConexionPlaza->query($sQuery, $aQueryParams);

        /**
         * obtiene los registros de la lista de trabajo de la zona de la unidad y de acuerdo a su horario de lista:
         *
         *
         *       06:00                              15:00                           23:00
         *         |-------- HORARIO DIURNO ----------|--------- HORARIO NOCTURNO ----|
         * 00:00                                    15:00                                23:59
         *   |----------------------------------------|------------------------------------|
         *           |--- PINSA---|             |-- BIMBO --|        |-- TECATE --|
         *         07:00        10:00         14:00       16:00    20:00        22:00
         *
         *                                  ENVASES ZAPATA (SIN HORARIO)
         *
         * Los resultados serian los sigueintes:
         * LISTA DIURNA:
         *     PINSA  07:00 - 10:00
         *     BIMBO  14:00 - 16:00
         *     ENVASES ZAPATA
         *
         * LISTA NOCTURNA:
         *     BIMBO  14:00 - 16:00
         *     TECATE 20:00 - 22:00
         *     ENVASES ZAPATA
         */
        $sQuery = "SELECT * " .
            "FROM lista_app " .
            "WHERE zona = ?";
        $aQueryParams = array(
            $aUnidad["zona"]
        );
        if ($aHorarioNocturno) {
            if ($aDatos["horario_zona"] == "N") {
                $sQuery .= " AND (" .
                        "hora_pref1 BETWEEN ? AND ? " .
                        "OR hora_pref2 BETWEEN ? AND ? " .
                        "OR hora_pref1 = 0" .
                    ")";
                $aQueryParams = array_merge(
                    $aQueryParams,
                    array(
                        intval(substr(str_replace(":", "", $aHorarioNocturno["hora_inicial"]), 0, 4)),
                        intval(substr(str_replace(":", "", $aHorarioNocturno["hora_final"]), 0, 4)),
                        intval(substr(str_replace(":", "", $aHorarioNocturno["hora_inicial"]), 0, 4)),
                        intval(substr(str_replace(":", "", $aHorarioNocturno["hora_final"]), 0, 4))
                    )
                );
            } else {
                $sQuery .= " AND (" .
                        "hora_pref1 NOT BETWEEN ? AND ? " .
                        "OR hora_pref1 = 0" .
                    ")";
                $aQueryParams = array_merge(
                    $aQueryParams,
                    array(
                        intval(substr(str_replace(":", "", $aHorarioNocturno["hora_inicial"]), 0, 4)),
                        intval(substr(str_replace(":", "", $aHorarioNocturno["hora_final"]), 0, 4)),
                    )
                );
            }
        }
        $aResultado = $oConexionPlaza->query($sQuery, $aQueryParams);
        $aListaTrabajo = $this->parsearQueryResult($aResultado);
        $nTotalLista = count($aListaTrabajo);

        // si no hay lista de trabajo se regresa vacia
        if ($nTotalLista <= 0) {
            return $this->asJson(array(
                "success" => true,
                "message" => "LISTA: [ No hay lista de trabajo ]",
                "metadata" => array(
                    "0" => array(
                        "Registros" => 0,
                        "fecha_lista" => $dFechaLista
                    ),
                    "fecha_lista" => $dFechaLista
                ),
                "data" => array()
            ));
        }

        $aListaTrabajo = $this->procesarClientesLista($aListaTrabajo);
        return $this->asJson(array(
            "success" => true,
            "message" => "Lista de trabajo con " . count($aListaTrabajo) . " servicios",
            "data" => $aListaTrabajo,
            "metadata" => array(
                "0" => array(
                    "Registros" => count($aListaTrabajo),
                    "Clientes" => 0,
                    "Vueltas" => count($aListaTrabajo),
                    "fecha_lista" => $dFechaLista,
                ),
                "Registros" => count($aListaTrabajo),
                "Clientes" => 0,
                "Vueltas" => count($aListaTrabajo),
                "fecha_lista" => $dFechaLista,
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
     * Arma y retorna la query para obtener la lista
     * @return string
     */
    public static function getListaQueryString() {
        return "SELECT DISTINCT " .
            "listas.id, " .
            "listas.ncontrol AS numero_control, " .
            "listas.nombre AS nombre_cliente, " .
            "listas.capacid AS capacidad_tanque, " .
            "listas.descuento_centavos AS descuento_centavos, " .
            "listas.surtido_multiple AS surtido_multiple, " .
            "listas.plazo_credito AS plazo_credito, " .
            "listas.status_credito AS status_credito, " .
            "listas.credito_disponible AS credito_disponible, " .
            "listas.tipo_carburacion AS tipo_carburacion, " .
            "listas.puntos_acumulados AS puntos_acumulados, " .
            "listas.latitud AS latitud, " .
            "listas.longitud AS longitud, " .
            "listas.fecha_fabricacion AS fecha_fabricacion, " .
            "padron.operacion AS factor_calibracion, " .
            "listas.calle AS calle_id, " .
            "listas.colonia AS colonia_id, " .
            "listas.ncasa AS numero_exterior, " .
            "listas.clave_surtido AS clave_credito, " .
            "padron.cuenta_cxc AS cuenta_credito, " .
            "listas.fecvta AS fecha_clave, " .
            "listas.zona AS zona, " .
            "0 AS tipo_compromiso_id, " .
            "? AS plaza_otorga_puntos, " .
            "IF(" .
                "padron.interior IS NULL, " .
                "\"\", " .
                "padron.interior" .
            ") AS numero_interior, " .
            // identifica si el servicio es programado
            "CASE " .
                "WHEN llamadas.ncontrol IS NOT NULL AND TRIM(listas.lista) IN (\"N\", \"P\", \"C\", \"L\", \"\") " .
                    "THEN 1 " .
                "WHEN llamadas.ncontrol IS NULL AND TRIM(listas.lista) IN (\"P\", \"L\", \"\") " .
                    "THEN 1 " .
                "ELSE 0 " .
            "END " .
            "AS es_programado, " .
            // identifica el tipo de compromiso programado
            "CASE " .
                "WHEN (llamadas.ncontrol IS NULL AND TRIM(listas.tipo_surt) = \"O\") " .
                    "OR (llamadas.ncontrol IS NOT NULL AND TRIM(listas.tipo_surt) IN (\"\", \"O\")) " .
                    "THEN \"OPERADORA\" " .
                "WHEN TRIM(listas.tipo_surt) = \"C\" " .
                    "THEN \"CHOFER\" " .
                "WHEN TRIM(listas.tipo_surt) = \"T\" " .
                    "THEN \"TELEMARKETING\" " .
                "ELSE \"EMERGENCIA\" " .
            "END " .
            "AS tipo_programado, " .
            // identifica el tipo de compromiso de emergencia
            "CASE " .
                "WHEN TRIM(listas.emergencia) = \"R\" " .
                    "THEN \"RUTEADO\" " .
                "ELSE \"EMERGENCIA\" " .
            "END " .
            "AS tipo_emergencia, " .
            // si no hay llamada la hora de preferencia sera la del padron
            "IF(" .
                "llamadas.de_las IS NULL, " .
                "padron.hora_pref1, " .
                "llamadas.de_las" .
            ") AS hora_pref1, " .
            // si no hay llamada la hora de preferencia sera la del padron
            "IF(" .
                "llamadas.de_las IS NULL, " .
                "padron.hora_pref2, " .
                "llamadas.a_las" .
            ") AS hora_pref2, " .
            // se asigna el perfil de pago
            "CASE " .
                "WHEN padron.tresm = 1 " .
                    "THEN 3 " . // llenafacil
                "WHEN padron.filial = 1 " .
                    "THEN 7 " . // filial
                "WHEN listas.status_credito = \"A\" AND listas.credito_disponible > 0 OR listas.status_credito = \"S\" " .
                    "THEN 2 " . // credito
                "WHEN listas.tipo_cte = 14 " .
                    "THEN 4 " . // consignacion
                "WHEN listas.tipo_cte = 15 " .
                    "THEN 6 " . // donativo
                "WHEN listas.tipo_cte = 16 " .
                    "THEN 5 " . // cortesia
                "ELSE 1 " . // general
            "END " .
            "AS tipo_cv_id, " .
            // se verifica si el tipo de cliente es comercial
            "IF(" .
                "listas.tipo_cte IN (1, 7), " .
                "0, " .
                "1" .
            ") AS tipo_cliente, " .
            "IF(" .
                "tarifas.cvetar IS NULL, " .
                "0, " .
                "tarifas.cvetar" .
            ") AS tarifa_id " .
        "FROM listas " .
        "LEFT JOIN padron ON listas.ncontrol = padron.ncontrol " .
        "LEFT JOIN tarifas ON tarifas.cvetar = padron.tarifa " .
        "LEFT JOIN llamadas ON listas.ncontrol = llamadas.ncontrol " .
            "AND llamadas.dia_sig = \"S\" " .
            "AND llamadas.motivo != 5 " .
            "AND DATE(llamadas.fecha) = ? " .
        "WHERE listas.fecha = ? " .
        "AND listas.estado = \"\" ";
    }

    /**
     * Procesa la informacion de la lista para que pueda ser usada por la appgasera
     *
     * @param  array $aListaTrabajo
     * @return array Lista procesada
     */
    private function procesarClientesLista($aListaTrabajo) {
        $aListaProcesada = array();

        foreach ($aListaTrabajo as &$aServicio) {
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

            // agrega el servicio procesado
            $aListaProcesada[] = array(
                "_1" => $aServicio['numero_control'], //numero_control
                "_2" => utf8_encode($aServicio['nombre_cliente']), //nombre_cliente
                "_3" => $aServicio['capacidad_tanque'], //capacidad_tanque
                "_4" => $aServicio['descuento_centavos'], //descuento_centavos
                //"_5" => $aServicio['precio_aditivo'], //precio_aditivo
                //"_6" => $aServicio['precio_gas'], //precio_gas
                "_7" => $aServicio['surtido_multiple'], //surtido_multiple
                "_8" => (!empty($aServicio['plazo_credito'])) ? $aServicio['plazo_credito'] : "", //plazo_credito
                "_9" => (!empty($aServicio['status_credito'])) ? $aServicio['status_credito'] : "", //status_credito
                "_10" => (!empty($aServicio['credito_disponible'])) ? $aServicio['credito_disponible'] : 0, //credito_disponible
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
                "_23" => $aServicio['tipo_cv_id'], //tipo_pago_id
                "_24" => $aServicio["otorga_puntos"], //otorga_puntos
                "_25" => (!empty($aServicio['clave_credito'])) ? $aServicio['clave_credito'] : '', //clave de autorizacion
                "_26" => $aServicio['tipo_cliente'], //tipo de cliente, identificando si es domestico o comercial
                "_27" => (!empty($aServicio['cuenta_credito'])) ? $aServicio['cuenta_credito'] : 0, //cuenta de crédito
                "_28" => (!empty($aServicio['fecha_clave'])) ? $aServicio['fecha_clave'] : '', //Fecha de uso de clave de autorización
                "_29" => (!empty($aServicio['zona_id'])) ? $aServicio['zona_id'] : '',
                "_30" => (!empty($aServicio['tarifa_id'])) ? $aServicio['tarifa_id'] : '',
                "_31" => (!empty($aServicio['tipo_compromiso_id'])) ? $aServicio['tipo_compromiso_id'] : 0,
                "_32" => $aServicio['numero_interior'] //ncasa
            );
        }
        unset($aServicio);

        return $aListaProcesada;
    }
}
