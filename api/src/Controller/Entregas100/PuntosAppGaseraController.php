<?php

namespace App\Controller\Entregas100;

use Exception;

class PuntosAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la tabla de puntos
     *
     * @return JsonResponse
     */
    public function puntos_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];
        $oConexion = $this->getConexion();

        // obtiene la tabla de puntos
        $sQuery = "SELECT * FROM tabla_puntos ORDER BY puntos";
        $aPuntos = $oConexion->query($sQuery);

        // si no existen alarmas se termina el proceso
        if (count($aPuntos) <= 0) {
            throw new Exception("No existe un catalogo de puntos");
        }

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aPuntosProcesados = array();
        foreach ($aPuntos as $value) {
            $aPuntosProcesados[] = array(
                "limite_inferior" => $value['limite_inferior'],
                "limite_superior" => $value['limite_superior'],
                "puntos" => $value['puntos'],
                "hora_inicial" => $value['hora_inicial'],
                "hora_final" => $value['hora_final']
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Puntos",
            "data" => $aPuntosProcesados,
            "metadata" => array(
                "Registros" => count($aPuntosProcesados),
                array(
                    "Registros" => count($aPuntosProcesados)
                )
            )
        ));
    }
}
