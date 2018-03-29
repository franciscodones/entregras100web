<?php

class LoginController extends AppController {

    /**
     * Autentica el usuario
     * @return JsonResponse
     */
    public function login() {
        $sUsuario = $this->request->data["usuario"];
        $sContrasena = $this->request->data["contrasena"];
        $oConexion = $this->getConexion();

        // busca el usuario con las credenciales proporcionadas
        $sQuery = "SELECT * FROM usuario WHERE usuario = ? AND password = ?";
        $aQueryParams = array($sUsuario, $sContrasena);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $aResultado = $this->parsearQueryResult($aResultado);

        // si no existe el usuario
        if (count($aResultado) <= 0) {
            return $this->asJson(array(
                "success" => false,
                "message" => "El usuario y/o contraseña son incorrecots"
            ));
        }
        $aUsuario = $aResultado[0];

        // obtiene los permisos personalizados del usuario
        $sQuery = "SELECT permisos.id " .
            "FROM permisos " .
            "INNER JOIN permisos_usuarios ON permisos.id = permisos_usuarios.permiso_id " .
            "WHERE permisos_usuarios.usuario_id = ?";
        $aQueryParams = array($aUsuario["id"]);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $aResultado = $this->parsearQueryResult($aResultado);
        $aPermisos = array_map(function($item) {
            return $item["id"];
        }, $aResultado);

        // si no existen permisos personalizados se obtienen los del tipo de usuario
        if (empty($aPermisos)) {
            $sQuery = "SELECT permisos.id " .
                "FROM permisos " .
                "INNER JOIN permisos_usuarios ON permisos.id = permisos_usuarios.permiso_id " .
                "WHERE permisos_usuarios.tipo_usuario_id = ?";
            $aQueryParams = array($aUsuario["tipo_sesion_id"]);
            $aResultado = $oConexion->query($sQuery, $aQueryParams);
            $aResultado = $this->parsearQueryResult($aResultado);
            $aPermisos = array_map(function($item) {
                return $item["id"];
            }, $aResultado);
        }

        // procesa la informacion del usuario
        $aInfoProcesada = array(
            "id" => $aUsuario["id"],
            "nombre" => $aUsuario["nombre"],
            "usuario" => $aUsuario["usuario"],
            "permisos" => $aPermisos
        );

        // si no tiene ninguna plaza especificada se toma como si tuviera acceso a todas
        $aUsuario["plaza_id"] = trim($aUsuario["plaza_id"]);
        if (empty($aUsuario["plaza_id"])) {
            $aInfoProcesada["plaza_id"] = array();
        } else {
            $aInfoProcesada["plaza_id"] = array_map(function($item) {
                return trim($item);
            }, explode(",", $aUsuario["plaza_id"]));
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Sesion iniciada",
            "data" => $aInfoProcesada
        ));
    }

    public function cambiarContrasena() {
        $aDatos = $this->request->data;
        $sContrasenaActual = $aDatos["contrasena_actual"];
        $sNuevaContrasena = $aDatos["contrasena"];
        $sConfirmacionContrasena = $aDatos["confirmacion_contrasena"];
        $nId = $aDatos["id"];
        $oConexion = $this->getConexion();

        // busca el usuario
        $sQuery = "SELECT * FROM usuario WHERE id = ?";
        $aQueryParams = array($nId);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $aResultado = $this->parsearQueryResult($aResultado);
        // si no se encuentra al usuario se termina el proceso
        if (empty($aResultado)) {
            throw new Exception("El usuario no fue encontrado");
        }
        $aUsuario = $aResultado[0];

        if ($sContrasenaActual != $aUsuario["password"]) {
            return $this->asJson(array(
                "success" => false,
                "message" => "La contraseña actual es incorrecta"
            ));
        }

        // actualiza la contraseña del usuario
        $sQuery = "UPDATE usuario SET " .
                "password = ? " .
            "WHERE id = ?";
        $aQueryParams = array(
            $sNuevaContrasena,
            $nId
        );
        $oConexion->query($sQuery, $aQueryParams);

        return $this->asJson(array(
            "success" => true,
            "message" => "Contraseña guardada corretamente"
        ));
    }
}
