<?php

namespace App\Controller\Entregas100;

use Exception;

class AsignarFechaOperacionAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Descarga el catalogo de tarifas
     *
     * @return JsonResponse
     */
    public function asignarfechaoperacion_fn() {
        $nParametrosFn = 1;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene la conexion a la bd de la plaza
        $oConexion = $this->getConexion();
        $sFechaOperacion = $aDatos["fecha_operacion"];

        // actualiza la fecha de operacion de la unidad
        $sQuery = "UPDATE unidad SET fecha_operacion = ? WHERE id = ?";
        $aQueryParams = array($sFechaOperacion, $aUnidad["id"]);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);

        return $this->asJson(array(
            "success" => true,
            "message" => "Fecha de operacion actualizada",
            "data" => null,
        ));
    }
}
