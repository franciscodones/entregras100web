<?php

namespace App\Controller\Entregas100;

use Exception;

class SesionoutAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Finaliza la sesion de todos los operadores con sesion iniciada en la unidad
     * proporcionada
     *
     * @return JsonResponse
     */
    public function sesionout_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        $oConexion = $this->getConexion();

        // elimina las sesiones de los operadores con sesion iniciada en la unidad
        $sQuery = "UPDATE operador " .
            "SET sesion = 0, " .
                "unidad_id = 0 " .
            "WHERE nip IN ( " .
                "SELECT nip FROM ruta " .
                "WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 2 DAY) " .
                "AND unidad_id = ? " .
                "UNION " .
                "SELECT nip2 FROM ruta " .
                "WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 2 DAY) " .
                "AND unidad_id = ? " .
            ")";
        $aQueryParams = array($aUnidad["id"], $aUnidad["id"]);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);

        return $this->asJson(array(
            "success" => true,
            "message" => "Sesion cerrada con exito",
        ));
    }
}
