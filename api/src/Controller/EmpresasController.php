<?php

namespace App\Controller;

use Exception;

class EmpresasController extends AppController {

    /**
     * Lee el catalogo de las empresas
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();

        // obtiene todas las empresas
        $sQuery = "SELECT * FROM empresa";
        $aEmpresas = $oConexion->query($sQuery);

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de empresas",
            "records" => $aEmpresas,
            "metadata" => array(
                "total_registros" => count($aEmpresas)
            )
        ));
    }
}
