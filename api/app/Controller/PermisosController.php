<?php

class PermisosController extends AppController {

    /**
     * Lee el catalogo de permisos
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();

        // obtiene todos los permisos
        $sQuery = "SELECT permisos.*, " .
                "categorias_permisos.descripcion AS categoria_permiso " .
            "FROM permisos " .
            "LEFT JOIN categorias_permisos ON permisos.categoria_permiso_id = categorias_permisos.id";
        $aResultado = $oConexion->query($sQuery);
        $aPermisos = $this->parsearQueryResult($aResultado);

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
     * Lee el catalogo de permisos de usuarios
     * @return JsonResponse
     */
    public function readPermisosUsuario() {
        $oConexion = $this->getConexion();

        // obtiene todos los permisos
        $sQuery = "SELECT * " .
            "FROM permisos_usuarios";
        $aResultado = $oConexion->query($sQuery);
        $aPermisos = $this->parsearQueryResult($aResultado);

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de permisos de usuarios",
            "records" => $aPermisos,
            "metadata" => array(
                "total_registros" => count($aPermisos)
            )
        ));
    }

    /**
     * Crea permisos de usuario o tipo de usuario
     * @return JsonResponse
     */
    public function createPermisosUsuario() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // actualiza el registro del operador
        $sQuery = "INSERT INTO permisos_usuarios (" .
                "tipo_usuario_id, " .
                "usuario_id, " .
                "permiso_id " .
            ") VALUES (" .
                "?, ?, ?" .
            ")";
        foreach ($aRecords as &$aRecord) {
            $aRecord["clientId"] = $aRecord["id"];
            $aQueryParams = array(
                $aRecord["tipo_usuario_id"],
                $aRecord["usuario_id"],
                $aRecord["permiso_id"]
            );
            $oConexion->query($sQuery, $aQueryParams);
            $aRecord["id"] = $oConexion->lastInsertId();
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
            "message" => "Permisos agregados",
            "records" => $aRecords
        ));
    }

    /**
     * Elimina los permisos de usuario o tipo de usuario
     * @return JsonResponse
     */
    public function destroyPermisosUsuario() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // actualiza el registro del operador
        $sQuery = "DELETE FROM permisos_usuarios " .
            "WHERE id = ?";
        foreach ($aRecords as $aRecord) {
            $aQueryParams = array(
                $aRecord["id"]
            );
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Permisos eliminados"
        ));
    }
}
