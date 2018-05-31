<?php

namespace App\Controller;

use Exception;

class TablaPuntosController extends AppController {

    /**
     * Lee la tabla de puntos
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();
        $aDatos = $this->request->query;

        // obtiene los registros de la tabla de puntos
        $sQuery = "SELECT tabla_puntos.*, " .
                "plaza.ciudad AS plaza " .
            "FROM tabla_puntos " .
            "INNER JOIN plaza ON tabla_puntos.plaza_id = plaza.id " .
            "ORDER BY puntos, plaza_id";
        $aPuntos = $oConexion->query($sQuery);

        //procesa las descuentos
        $aPuntosProcesados = array();
        foreach ($aPuntos as $value) {
            $aPuntosProcesados[] = array(
                "id" => $value["id"],
                "limite_inferior" => $value["limite_inferior"],
                "limite_superior" => $value["limite_superior"],
                "puntos" => $value["puntos"],
                "hora_inicial" => $value["hora_inicial"],
                "hora_final" => $value["hora_final"],
                "plaza_id" => $value["plaza_id"],
                "plaza" => $value["plaza"]
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Tabla de puntos",
            "records" => $aPuntosProcesados,
            "metadata" => array(
                "total_registros" => count($aPuntosProcesados)
            )
        ));
    }

    /**
     * Crea un registros en la tabla de puntos
     * @return JsonResponse
     */
    public function create() {
        $oConexion = $this->getConexion();
        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // agrega el registro del horario
        $sQuery = "INSERT INTO tabla_puntos (" .
                "limite_inferior, " .
                "limite_superior, " .
                "puntos, " .
                "hora_inicial, " .
                "hora_final, " .
                "plaza_id " .
            ") VALUES (" .
                "?, ?, ?, ?, ?, ?" .
            ")";
        foreach ($aRecords as &$aRecord) {
            $aRecord["clientId"] = $aRecord["id"];
            $aQueryParams = array(
                $aRecord["limite_inferior"],
                $aRecord["limite_superior"],
                $aRecord["puntos"],
                $aRecord["hora_inicial"],
                $aRecord["hora_final"],
                $aRecord["plaza_id"]
            );
            $oConexion->query($sQuery, $aQueryParams);
            $aRecord["id"] = $oConexion->driver()->lastInsertId();
        }
        unset($aRecord);

        // procesa los records para regresarlos y que los campos se actualicen en el store
        $aRecords = array_map(function($aRecord) {
            return array(
                "id" => $aRecord["id"],
                "clientId" => $aRecord["clientId"]
            );
        }, $aRecords);

        return $this->asJson(array(
            "success" => true,
            "message" => "Tabla de puntos agregados",
            "records" => $aRecords
        ));
    }

    /**
     * Actualiza los descuentos promocion
     * @return JsonResponse
     */
    public function update() {
        $oConexion = $this->getConexion();
        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // actualiza el registro del descuento
        $sQuery = "UPDATE tabla_puntos SET " .
                "limite_inferior = ?, " .
                "limite_superior = ?, " .
                "puntos = ?, " .
                "hora_inicial = ?, " .
                "hora_final = ?, " .
                "plaza_id = ? " .
            "WHERE id = ?";
        foreach ($aRecords as $aRecord) {
            $aQueryParams = array(
                $aRecord["limite_inferior"],
                $aRecord["limite_superior"],
                $aRecord["puntos"],
                $aRecord["hora_inicial"],
                $aRecord["hora_final"],
                $aRecord["plaza_id"],
                $aRecord["id"],
            );
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Tabla de puntos actualizada"
        ));
    }

    /**
     * Elimina descuentos promocion
     * @return JsonResponse
     */
    public function destroy() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // elimina los registros
        $sQuery = "DELETE FROM tabla_puntos " .
            "WHERE id = ?";
        foreach ($aRecords as $aRecord) {
            $aQueryParams = array($aRecord["id"]);
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Puntos eliminados"
        ));
    }
}
