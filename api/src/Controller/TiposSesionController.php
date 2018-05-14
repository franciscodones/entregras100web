<?php

namespace App\Controller;

use Exception;

class TiposSesionController extends AppController {

    /**
     * Lee el catalogo de los tipos de sesion
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();

        // obtiene todas las empresas
        $sQuery = "SELECT * FROM tipo_sesion";
        $aTiposSesion = $oConexion->query($sQuery);

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de tipos de sesion",
            "records" => $aTiposSesion,
            "metadata" => array(
                "total_registros" => count($aTiposSesion)
            )
        ));
    }
}
