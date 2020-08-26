<?php

namespace App\Controller;

use Exception;

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
        // si no hay info de la plaza se termina el proceso
        if (count($aResultado) <= 0) {
            throw new Exception("Error al obtener los datos de conexion a la plaza");
        }
        $aPlaza = $aResultado[0];
        $oConexionPlaza = $this->getConexion(
            $aPlaza["plaza"],
            array(
                "host" => $aPlaza["ip_te"],
                "username" => $aPlaza["usuario_te"],
                "password" => $aPlaza["password_te"],
                "database" => $aPlaza["base_te"]
            )
        );

        // obtiene los registros de las tarifas
        $sQuery = "SELECT * FROM tarifas ORDER BY cvetar";
        $aTarifas = $oConexionPlaza->query($sQuery);

        //procesa las tarifas
        $aTarifasProcesadas = array();
        foreach ($aTarifas as $aTarifa) {
            $aTarifasProcesadas[] = array(
                "id" => $aTarifa["cvetar"],
                "cvetar" => $aTarifa["cvetar"],
                "precio2" => $aTarifa["precio2"],
                "aditivo2" => $aTarifa["aditivo2"],
                "es_base" => $aTarifa["cvetar"] == $aPlaza["tarifa_id"]
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de tarifas",
            "records" => $aTarifasProcesadas,
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

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);
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
        // si no hay info de la plaza se termina el proceso
        if (count($aResultado) <= 0) {
            throw new Exception("Error al obtener los datos de conexion a la plaza");
        }
        $aPlaza = $aResultado[0];
        $oConexionPlaza = $this->getConexion(
            $aPlaza["plaza"],
            array(
                "host" => $aPlaza["ip_te"],
                "username" => $aPlaza["usuario_te"],
                "password" => $aPlaza["password_te"],
                "database" => $aPlaza["base_te"]
            )
        );

        // actualiza el registro de la tarifa
        foreach ($aRecords as $aRecord) {
            $sQuery = "UPDATE tarifas SET " .
                    "precio2 = ?, " .
                    "aditivo2 = ? " .
                "WHERE cvetar = ?";
            $aQueryParams = array(
                $aRecord["precio2"],
                $aRecord["aditivo2"],
                $aRecord["cvetar"]
            );
            $oConexionPlaza->query($sQuery, $aQueryParams);
            if ($aRecord["es_base"]) {
                $sQuery = "UPDATE plaza SET " .
                        "tarifa_id = ? " .
                    "WHERE id = ?";
                $aQueryParams = array($aRecord["cvetar"], $aPlaza["id"]);
                $oConexion->query($sQuery, $aQueryParams);
            }
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Tarifas actualizadas"
        ));
    }
}
