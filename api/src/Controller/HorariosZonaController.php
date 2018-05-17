<?php

namespace App\Controller;

use Exception;

class HorariosZonaController extends AppController {

    /**
     * Lee el catalogo de los horarios de las zonas
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();

        // obtiene todas las zonas
        $sQuery = "SELECT horario_zona.*, " .
                "zona.zona, " .
                "zona.plaza_id, " .
                "plaza.ciudad AS plaza " .
            "FROM horario_zona " .
            "INNER JOIN zona ON horario_zona.zona_id = zona.id " .
            "INNER JOIN plaza ON zona.plaza_id = plaza.id " .
            "ORDER BY plaza.id, zona.zona";
        $aHorariosZona = $oConexion->query($sQuery);

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de horarios de las zonas",
            "records" => $aHorariosZona,
            "metadata" => array(
                "total_registros" => count($aHorariosZona)
            )
        ));
    }

    /**
     * Crea horarios de zonas
     * @return JsonResponse
     */
    public function create() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // agrega el registro del horario
        $sQuery = "INSERT INTO horario_zona (" .
                "zona_id, " .
                "hora_inicial, " .
                "hora_final " .
            ") VALUES (" .
                "?, ?, ?" .
            ")";
        foreach ($aRecords as &$aRecord) {
            $aRecord["clientId"] = $aRecord["id"];
            $aQueryParams = array(
                $aRecord["zona_id"],
                $aRecord["hora_inicial"],
                $aRecord["hora_final"]
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
            "message" => "Horarios agregados",
            "records" => $aRecords
        ));
    }

    /**
     * Actualiza horarios de zonas
     * @return JsonResponse
     */
    public function update() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // actualiza el registro del horario
        $sQuery = "UPDATE horario_zona SET " .
                "zona_id = ?, " .
                "hora_inicial = ?, " .
                "hora_final = ? " .
            "WHERE id = ?";
        foreach ($aRecords as $aRecord) {
            $aQueryParams = array(
                $aRecord["zona_id"],
                $aRecord["hora_inicial"],
                $aRecord["hora_final"],
                $aRecord["id"]
            );
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Horarios actualizados"
        ));
    }

    /**
     * Elimina horarios de zonas
     * @return JsonResponse
     */
    public function destroy() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // actualiza el registro de la zona
        $sQuery = "DELETE FROM horario_zona " .
            "WHERE id = ?";
        foreach ($aRecords as $aRecord) {
            $aQueryParams = array($aRecord["id"]);
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Horarios eliminados"
        ));
    }
}
