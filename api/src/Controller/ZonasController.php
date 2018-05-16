<?php

namespace App\Controller;

use Exception;

class ZonasController extends AppController {

    /**
     * Lee el catalogo de las zonas
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();

        // obtiene todas las zonas
        $sQuery = "SELECT zona.id AS id, " .
                "zona.zona, " .
                "zona.descripcion, " .
                "zona.ayudante, " .
                "zona.plaza_id, " .
                "zona.estatus, " .
                "plaza.ciudad AS nombre_plaza " .
            "FROM zona " .
            "INNER JOIN plaza ON zona.plaza_id = plaza.id " .
            "ORDER BY plaza.id, zona.zona";
        $aZonas = $oConexion->query($sQuery);

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de zonas",
            "records" => $aZonas,
            "metadata" => array(
                "total_registros" => count($aZonas)
            )
        ));
    }

    /**
     * Crea zonas
     * @return JsonResponse
     */
    public function create() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // agrega el registro de la zona
        $sQuery = "INSERT INTO zona (" .
                "plaza_id, " .
                "zona, " .
                "descripcion, " .
                "ayudante, " .
                "estatus, " .
                "fecha_registro, " .
                "fecha_modificacion " .
            ") VALUES (" .
                "?, ?, ?, ?, ?, ?, ?" .
            ")";
        foreach ($aRecords as &$aRecord) {
            $aRecord["clientId"] = $aRecord["id"];
            $aQueryParams = array(
                $aRecord["plaza_id"],
                $aRecord["zona"],
                $aRecord["descripcion"],
                $aRecord["ayudante"],
                $aRecord["estatus"],
                date("Y-m-d H:i:s"),
                date("Y-m-d H:i:s")
            );
            $aResultado = $oConexion->query($sQuery, $aQueryParams);
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
            "message" => "Zonas agregadas",
            "records" => $aRecords
        ));
    }

    /**
     * Actualiza zonas
     * @return JsonResponse
     */
    public function update() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // actualiza el registro de la zona
        $sQuery = "UPDATE zona SET " .
                "plaza_id = ?, " .
                "zona = ?, " .
                "descripcion = ?, " .
                "ayudante = ?, " .
                "fecha_modificacion = ?, " .
                "estatus = ? " .
            "WHERE id = ?";
        foreach ($aRecords as $aRecord) {
            $aQueryParams = array(
                $aRecord["plaza_id"],
                $aRecord["zona"],
                $aRecord["descripcion"],
                $aRecord["ayudante"],
                date("Y-m-d H:i:s"),
                $aRecord["estatus"],
                $aRecord["id"]
            );
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Zonas actualizadas"
        ));
    }

    /**
     * Elimina zonas
     * @return JsonResponse
     */
    public function destroy() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // actualiza el registro de la zona
        $sQuery = "DELETE FROM zona " .
            "WHERE id = ?";
        foreach ($aRecords as $aRecord) {
            $aQueryParams = array($aRecord["id"]);
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Zonas eliminadas"
        ));
    }
}
