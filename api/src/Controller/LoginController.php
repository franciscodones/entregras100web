<?php

namespace App\Controller;

use Exception;

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
        $sQuery = "SELECT * FROM usuario";
        //$aQueryParams = array($sUsuario, $sContrasena);
        $aResultado = $oConexion->query($sQuery);
        $x = $aResultado["inde"];

        return $this->asJson(array(
            "success" => true
        ));
    }
}
