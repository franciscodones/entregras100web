<?php

namespace App\Controller\Entregas100;

use Exception;

class CapacidadAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la lista de capacidades
     *
     * @return JsonResponse
     */
    public function capacidad_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene las capacidades
        $oConexion = $this->getConexion();
        $sQuery = "SELECT * FROM capacidad";
        $aCapacidades = $oConexion->query($sQuery);

        // si no existen capacidades se termina el proceso
        if (count($aCapacidades) <= 0) {
            throw new Exception("No existe un catalogo de capacidades");
        }

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aCapacidadesProcesadas = array();
        foreach ($aCapacidades as $value) {
            $aCapacidadesProcesadas[] = array(
                "id" => $value['id'],
                "capacidad" => $value['capacidad']
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Capacidades",
            "data" => $aCapacidadesProcesadas,
            "metadata" => array(
                "Registros" => count($aCapacidadesProcesadas),
                array(
                    "Registros" => count($aCapacidadesProcesadas)
                )
            )
        ));
    }
}
