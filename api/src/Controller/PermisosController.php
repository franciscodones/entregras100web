<?php

namespace App\Controller;

use Exception;

class PermisosController extends AppController {

    /**
     * Lee el catalogo de permisos
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();

        // obtiene todos los permisos
        $sQuery = "SELECT * " .
            "FROM permisos ";
        $aPermisos = $oConexion->query($sQuery);

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de permisos",
            "records" => $aPermisos,
            "metadata" => array(
                "total_registros" => count($aPermisos)
            )
        ));
    }

    /**
     * Lee el pivote de permisos
     * @return JsonResponse
     */
    public function readPivotePermisos() {
        $oConexion = $this->getConexion();
        $aDatos = $this->request->query;

        // obtiene todos los permisos
        $sQuery = "SELECT * " .
            "FROM pivote_permisos " .
            "WHERE pertenece_id = ? " .
            "AND tipo = ?";
        $aQueryParams = array(
            $aDatos["pertenece_id"],
            $aDatos["tipo"]
        );
        $aPermisos = $oConexion->query($sQuery, $aQueryParams);

        return $this->asJson(array(
            "success" => true,
            "message" => "Permisos otorgados",
            "records" => $aPermisos,
            "metadata" => array(
                "total_registros" => count($aPermisos)
            )
        ));
    }

    /**
     * Otorga permisos creando registros en pivote_permisos
     * @return JsonResponse
     */
    public function createPivotePermisos() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // actualiza el registro del operador
        $sQuery = "INSERT INTO pivote_permisos (" .
                "pertenece_id, " .
                "permiso_id, " .
                "tipo " .
            ") VALUES (" .
                "?, ?, ?" .
            ")";
        foreach ($aRecords as &$aRecord) {
            $aRecord["clientId"] = $aRecord["id"];
            $aQueryParams = array(
                $aRecord["pertenece_id"],
                $aRecord["permiso_id"],
                $aRecord["tipo"]
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
            "message" => "Permisos otorgados",
            "records" => $aRecords
        ));
    }

    /**
     * Quita permisos eliminando registros de pivote_permisos
     * @return JsonResponse
     */
    public function destroyPivotePermisos() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // actualiza el registro del operador
        $sQuery = "DELETE FROM pivote_permisos " .
            "WHERE id = ?";
        foreach ($aRecords as $aRecord) {
            $aQueryParams = array(
                $aRecord["id"]
            );
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Permisos quitados"
        ));
    }
}
