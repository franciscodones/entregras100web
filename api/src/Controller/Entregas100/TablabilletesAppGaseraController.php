<?php

namespace App\Controller\Entregas100;

use Exception;

class TablabilletesAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la lista de capacidades
     *
     * @return JsonResponse
     */
    public function tablabilletes_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene la tabla de billetes
        $oConexion = $this->getConexion();
        $sQuery = "SELECT * FROM tabla_billetes";
        $aTablaBilletes = $oConexion->query($sQuery);

        // si no existen capacidades se termina el proceso
        if (count($aTablaBilletes) <= 0) {
            throw new Exception("No existe una tabla de billetes");
        }

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aTablaBilletesProcesada = array();
        foreach ($aTablaBilletes as $value) {
            $aTablaBilletesProcesada[] = array(
                "id" => $value['id'],
                "limite_inferior" => $value["limite_inferior"],
                "limite_superior" => $value["limite_superior"],
                "valor" => $value['valor']
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Tabla de billetes",
            "data" => $aTablaBilletesProcesada,
            "metadata" => array(
                "Registros" => count($aTablaBilletesProcesada),
                array(
                    "Registros" => count($aTablaBilletesProcesada)
                )
            )
        ));
    }
}
