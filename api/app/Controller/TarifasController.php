<?php

class TarifasController extends AppController {

    /**
     * Lee el catalogo de las tarifas
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();
        $aDatos = $this->request->query;
        $nPlazaId = $aDatos["plaza_id"];

        // obtiene la conexion a la bd de la plaza
        $sQuery = "SELECT plaza.*, " .
            "conexion.ip_te, " .
            "conexion.usuario_te, " .
            "conexion.password_te, " .
            "conexion.base_te " .
            "FROM plaza " .
            "INNER JOIN conexion ON plaza.id = conexion.plaza_id " .
            "WHERE plaza.id = ?";
        $aQueryParams = array($nPlazaId);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $aResultado = $this->parsearQueryResult($aResultado);
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

        // obtiene los registros de las tarifas
        $sQuery = "SELECT * FROM tarifas ORDER BY cvetar";
        $aResultado = $oConexionPlaza->query($sQuery);
        $aTarifas = $this->parsearQueryResult($aResultado);

        //procesa las tarifas
        $aTarifasProcesadas = array();
        foreach ($aTarifas as $aTarifa) {
            $aTarifasProcesadas[] = array(
                "id" => $aTarifa["cvetar"],
                "cvetar" => $aTarifa["cvetar"],
                "precio2" => $aTarifa["precio2"],
                "aditivo2" => $aTarifa["aditivo2"],
                "es_base" => $aTarifa["cvetar"] == $aPlaza["tarifa_id"],
                "plaza_id" => $aPlaza["id"],
                "nombre_plaza" => $aPlaza["ciudad"]
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de tarifas",
            "data" => $aTarifasProcesadas,
            "metadata" => array(
                "total_registros" => count($aTarifasProcesadas)
            )
        ));
    }

    /**
     * Actualiza las tarifas
     * @return JsonResponse
     */
    public function update() {
        $oConexion = $this->getConexion();

        $this->request->data["datos"] = json_decode($this->request->data["datos"], true);
        $aDatos = $this->request->data;
        $nPlazaId = $aDatos["plaza_id"];

        // obtiene la conexion a la bd de la plaza
        $sQuery = "SELECT plaza.*, " .
            "conexion.ip_te, " .
            "conexion.usuario_te, " .
            "conexion.password_te, " .
            "conexion.base_te " .
            "FROM plaza " .
            "INNER JOIN conexion ON plaza.id = conexion.plaza_id " .
            "WHERE plaza.id = ?";
        $aQueryParams = array($nPlazaId);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $aResultado = $this->parsearQueryResult($aResultado);
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

        // actualiza el registro de la zona
        foreach ($aDatos["datos"] as $key => $aTarifa) {
            $sQuery = "UPDATE tarifas SET " .
                    "precio2 = ?, " .
                    "aditivo2 = ? " .
                "WHERE cvetar = ?";
            $aQueryParams = array(
                $aTarifa["precio2"],
                $aTarifa["aditivo2"],
                $aTarifa["cvetar"]
            );
            $oConexionPlaza->query($sQuery, $aQueryParams);
            if ($aTarifa["es_base"]) {
                $sQuery = "UPDATE plaza SET " .
                        "tarifa_id = ? " .
                    "WHERE id = ?";
                $aQueryParams = array($aTarifa["cvetar"], $aPlaza["id"]);
                $oConexion->query($sQuery, $aQueryParams);
            }
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Tarifas actualizadas"
        ));
    }
}
