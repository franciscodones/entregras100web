<?php

namespace App\Controller;

use Exception;

class UsuariosController extends AppController {

    /**
     * Lee el catalogo de usuarios
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();

        // obtiene todos los usuarios
        $sQuery = "SELECT usuario.*, " .
                "tipo_sesion.tipo_sesion " .
            "FROM usuario " .
            "INNER JOIN tipo_sesion ON usuario.tipo_sesion_id = tipo_sesion.id";
        $aUsuarios = $oConexion->query($sQuery);

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de usuarios",
            "records" => $aUsuarios,
            "metadata" => array(
                "total_registros" => count($aUsuarios)
            )
        ));
    }

    /**
     * Crea usuarios
     * @return JsonResponse
     */
    public function create() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // actualiza el registro del usuarios
        $sQuery = "INSERT INTO usuario (" .
                "nombre, " .
                "usuario, " .
                "password, " .
                "email, " .
                "plaza_id, " .
                "tipo_sesion_id, " .
                "estatus, " .
                "fecha_time " .
            ") VALUES (" .
                "?, ?, ?, ?, ?, ?, ?, ?" .
            ")";
        foreach ($aRecords as &$aRecord) {
            $aRecord["clientId"] = $aRecord["id"];
            $aQueryParams = array(
                $aRecord["nombre"],
                $aRecord["usuario"],
                $aRecord["password"],
                $aRecord["email"],
                $aRecord["plaza_id"],
                $aRecord["tipo_sesion_id"],
                $aRecord["estatus"],
                date("Y-m-d H:i:s")
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
            "message" => "Usuarios agregados",
            "records" => $aRecords
        ));
    }

    /**
     * Actualiza los usuarios
     * @return JsonResponse
     */
    public function update() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // actualiza el registro del usuario
        $sQuery = "UPDATE usuario SET " .
                "nombre = ?, " .
                "usuario = ?, " .
                "password = ?, " .
                "email = ?, " .
                "plaza_id = ?, " .
                "tipo_sesion_id = ?, " .
                "estatus = ? " .
            "WHERE id = ?";
        foreach ($aRecords as $aRecord) {
            $aQueryParams = array(
                $aRecord["nombre"],
                $aRecord["usuario"],
                $aRecord["password"],
                $aRecord["email"],
                $aRecord["plaza_id"],
                $aRecord["tipo_sesion_id"],
                $aRecord["estatus"],
                $aRecord["id"]
            );
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Usuarios actualizados"
        ));
    }

    /**
     * Elimina los usuarios
     * @return JsonResponse
     */
    public function destroy() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // actualiza el registro del usuario
        $sQuery = "DELETE FROM usuario " .
            "WHERE id = ?";
        foreach ($aRecords as $aRecord) {
            $aQueryParams = array(
                $aRecord["id"]
            );
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Usuarios eliminados"
        ));
    }
}
