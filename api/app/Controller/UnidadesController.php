<?php

class UnidadesController extends AppController {

    /**
     * Lee el catalogo de unidades
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();

        // obtiene todas las unidades
        $sQuery = "SELECT unidad.*, " .
                "zona.zona, " .
                "plaza.id AS plaza_id, " .
                "plaza.ciudad AS nombre_plaza " .
            "FROM unidad " .
            "LEFT JOIN zona ON unidad.zona_id = zona.id " .
            "LEFT JOIN plaza ON zona.plaza_id = plaza.id";
        $aResultado = $oConexion->query($sQuery);
        $aUnidades = $this->parsearQueryResult($aResultado);

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de unidades",
            "data" => $aUnidades,
            "metadata" => array(
                "total_registros" => count($aUnidades)
            )
        ));
    }

    /**
     * Crea una unidad
     * @return JsonResponse
     */
    public function create() {
        $oConexion = $this->getConexion();

        $this->request->data["datos"] = json_decode($this->request->data["datos"], true);
        $aDatos = $this->request->data["datos"][0];
        $nZonaId = $aDatos["zona_id"];
        $nUnidad = $aDatos["unidad"];
        $sLetra = $aDatos["letra"];
        $sTipo = "E";
        $sFecha = "0000-00-00 00:00:00";
        $nLatitud = 0;
        $nLongitud = 0;
        $nRssi = 0;
        $bOnline = $aDatos["online"];
        $bCobroAditivo = $aDatos["cobro_aditivo"];
        $bAditivoObligatorio = false;
        $sAutorizacion = "";
        $nTiempo = 0;
        $nSincronizacion = 0;
        $sFechaOperacion = "0000-00-00 00:00:00";
        $sVersion = "2.4";
        $sRutaActualizacion = "http://gps.gaspasa.com.mx:8080/Entregas100/APK/v2.4/v2.4_gps.apk";
        $sFechaRegistro = date("Y-m-d H:i:s");
        $sFechaModificacion = date("Y-m-d H:i:s");
        $sEstado = "";

        // agrega el registro de la unidad
        $sQuery = "INSERT INTO unidad (" .
                "zona_id, " .
                "unidad, " .
                "letra, " .
                "tipo, " .
                "fecha, " .
                "latitud, " .
                "longitud, " .
                "rssi, " .
                "online, " .
                "cobro_aditivo, " .
                "aditivo_obligatorio, " .
                "autorizacion, " .
                "tiempo, " .
                "sincronizacion, " .
                "fecha_operacion, " .
                "version, " .
                "ruta_actualizacion, " .
                "fecha_registro, " .
                "fecha_modificacion, " .
                "estado " .
            ") VALUES (" .
                "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?" .
            ")";
        $aQueryParams = array(
            $nZonaId,
            $nUnidad,
            $sLetra,
            $sTipo,
            $sFecha,
            $nLatitud,
            $nLongitud,
            $nRssi,
            $bOnline,
            $bCobroAditivo,
            $bAditivoObligatorio,
            $sAutorizacion,
            $nTiempo,
            $nSincronizacion,
            $sFechaOperacion,
            $sVersion,
            $sRutaActualizacion,
            $sFechaRegistro,
            $sFechaModificacion,
            $sEstado
        );
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $nUnidadId = $oConexion->lastInsertId();

        return $this->asJson(array(
            "success" => true,
            "message" => "Unidad agregada",
            "data" => array(
                "id" => $nUnidadId
            )
        ));
    }

    /**
     * Actualiza una unidad
     * @return JsonResponse
     */
    public function update() {
        $oConexion = $this->getConexion();

        $this->request->data["datos"] = json_decode($this->request->data["datos"], true);
        $aDatos = $this->request->data["datos"][0];
        $nId = $aDatos["id"];
        $nZonaId = $aDatos["zona_id"];
        $nUnidad = $aDatos["unidad"];
        $sLetra = $aDatos["letra"];
        $bOnline = $aDatos["online"];
        $bCobroAditivo = $aDatos["cobro_aditivo"];

        // actualiza el registro de la unidad
        $sQuery = "UPDATE unidad SET " .
                "unidad = ?, " .
                "zona_id = ?, " .
                "letra = ?, " .
                "online = ?, " .
                "cobro_aditivo = ? " .
            "WHERE id = ?";
        $aQueryParams = array(
            $nUnidad,
            $nZonaId,
            $sLetra,
            $bOnline,
            $bCobroAditivo,
            $nId
        );
        $oConexion->query($sQuery, $aQueryParams);

        return $this->asJson(array(
            "success" => true,
            "message" => "Unidad actualizada"
        ));
    }
}
