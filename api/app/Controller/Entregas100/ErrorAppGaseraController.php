<?php

class ErrorAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Guarda una error de la aplicacion
     *
     * @return JsonResponse
     */
    public function error_fn() {
        $nParametrosFn = 3;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        $dFecha = $aDatos['fecha'];
        $tHora = $aDatos['hora'];
        $sError = $aDatos['error'];

        // agrega el registro del error
        $oConexion = $this->conexion();
        $sQuery = "INSERT INTO logs_error(" .
                "unidad_id, " .
                "fecha, " .
                "hora, " .
                "error" .
            ") VALUES (" .
                "?, ?, ?, ?" .
            ")";
        $aQueryParams = array(
            $aUnidad["id"],
            $dFecha,
            $tHora,
            $sError
        );
        $aResultado = $oConexion->query($sQuery, $aQueryParams);

        return $this->asJson(array(
            "success" => true,
            "message" => "Guardado con éxito",
            "data" => null
        ));
    }
}
