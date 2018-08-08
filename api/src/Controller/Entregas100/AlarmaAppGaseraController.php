<?php

namespace App\Controller\Entregas100;

use Exception;

class AlarmaAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Guarda una alarma del sv600
     *
     * @return JsonResponse
     */
    public function alarma_fn() {
        $nParametrosFn = 7;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        $sAlarmas = !empty($aDatos['alarma_id']) ? $aDatos['alarma_id'] : $aDatos['alarmas'];
        $nNumeroControl = !empty($aDatos['numero_control']) ? $aDatos['numero_control'] : null;
        $nNumeroServicio = !empty($aDatos['servicio']) ? $aDatos['servicio'] : null;
        $nLitros = !empty($aDatos['litros']) ? $aDatos['litros'] / 100: null;
        $nLitrosNoAutorizados = !empty($aDatos['litros_no']) ? $aDatos['litros_no'] / 100 : null;
        $dFecha = $aDatos['fecha'];
        $tHora = $aDatos['hora'];
        $aAlarmasArray = array(
            "1" => 0,
            "2" => 0,
            "3" => 0,
            "4" => 0,
            "5" => 0,
            "6" => 0,
            "7" => 0,
            "8" => 0
        );

        // hack para normalizar las alarmas que son individuales en el json
        // usado para guardar varias alarmas a la vez
        if (preg_match("/\d+/", $sAlarmas)) {
            $aAlarmasArray[$sAlarmas] = 1;
            $sAlarmas = json_encode($aAlarmasArray);
        }

        // agrega el registro de la alarma
        $oConexion = $this->getConexion();
        $sQuery = "INSERT INTO logs_alarmas(" .
                "unidad_id, " .
                "numero_control, " .
                "servicio, " .
                "litros, " .
                "litros_no_autorizados, " .
                "presion_referencia, " .
                "presion_mangerazo, " .
                "alarma, " .
                "fecha, " .
                "hora, " .
                "fecha_registro" .
            ") VALUES (" .
                "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?" .
            ") ON DUPLICATE KEY UPDATE alarma = alarma";
        $aQueryParams = array(
            $aUnidad["id"],
            $nNumeroControl,
            $nNumeroServicio,
            $nLitros,
            $nLitrosNoAutorizados,
            0,
            0,
            $sAlarmas,
            $dFecha,
            $tHora,
            date("Y-m-d H:i:s")
        );
        $aResultado = $oConexion->query($sQuery, $aQueryParams);

        return $this->asJson(array(
            "success" => true,
            "message" => "Guardado con éxito",
            "data" => null
        ));
    }
}
