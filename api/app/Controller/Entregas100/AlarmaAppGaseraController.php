<?php

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
        $nLitros = !empty($aDatos['litros']) ? $aDatos['litros'] : null;
        $nLitrosNoAutorizados = !empty($aDatos['litros_no']) ? $aDatos['litros_no'] : null;
        $dFecha = $aDatos['fecha'];
        $tHora = $aDatos['hora'];

        // agrega el registro de la alarma
        $oConexion = $this->conexion();
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
            ")";
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
