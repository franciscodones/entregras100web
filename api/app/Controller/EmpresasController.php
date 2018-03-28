<?php

class EmpresasController extends AppController {

    /**
     * Lee el catalogo de las empresas
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();

        // obtiene todas las empresas
        $sQuery = "SELECT * FROM empresa";
        $aResultado = $oConexion->query($sQuery);
        $aPlazas = $this->parsearQueryResult($aResultado);

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de empresas",
            "records" => $aPlazas,
            "metadata" => array(
                "total_registros" => count($aPlazas)
            )
        ));
    }
}
