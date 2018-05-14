<?php

namespace App\Controller\Entregas100;

use Exception;

class BancoAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la lista de bancos
     *
     * @return JsonResponse
     */
    public function banco_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene los bancos
        $oConexion = $this->getConexion();
        $sQuery = "SELECT * FROM bancos";
        $aBancos = $oConexion->query($sQuery);

        // si no existen bancos se termina el proceso
        if (count($aBancos) <= 0) {
            throw new Exception("No existe un catalogo de bancos");
        }

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aBancosProcesados = array();
        foreach ($aBancos as $value) {
            $aBancosProcesados[] = array(
                "id" => $value['id'],
                "descripcion" => $value['descripcion']
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Bancos",
            "data" => $aBancosProcesados,
            "metadata" => array(
                "Registros" => count($aBancosProcesados),
                array(
                    "Registros" => count($aBancosProcesados)
                )
            )
        ));
    }
}
