<?php

namespace App\Controller\Entregas100;

use Exception;

class CombinacionformaplazaAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la lista de combinaciones de plaza - forma de pago
     *
     * @return JsonResponse
     */
    public function combinacionforma_plaza_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene las combinaciones plaza - forma de pago
        $oConexion = $this->getConexion();
        $sQuery = "SELECT * " .
            "FROM combinacion_forma_plaza " .
            "WHERE plaza_id = ? " .
            "ORDER BY forma_pago_id";
        $aQueryParams = array($aUnidad["plaza_id"]);
        $aCombinaciones = $oConexion->query($sQuery, $aQueryParams);

        // si no existen bancos se termina el proceso
        if (count($aCombinaciones) <= 0) {
            throw new Exception("No existe un catalogo de combinaciones de plaza - forma de pago");
        }

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aCombinacionesProcesadas = array();
        foreach ($aCombinaciones as $value) {
            $aCombinacionesProcesadas[] = array(
                "id" => $value['id'],
                "plaza_id" => $value['plaza_id'],
                "forma_pago_id" => $value['forma_pago_id']
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Combinaciones de plaza - forma de pago",
            "data" => $aCombinacionesProcesadas,
            "metadata" => array(
                "Registros" => count($aCombinacionesProcesadas),
                array(
                    "Registros" => count($aCombinacionesProcesadas)
                )
            )
        ));
    }
}
