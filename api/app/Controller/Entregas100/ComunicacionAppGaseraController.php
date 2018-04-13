<?php

class ComunicacionAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Guarda una comunicacion del sv600
     *
     * @return JsonResponse
     */
    public function comunicacion_fn() {
        $nParametrosFn = 7;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        $nLongitud = $aDatos['longitud'];
        $nDireccion = $aDatos['direccion'];
        $nComando = $aDatos['comando'];
        $sInformacion = $aDatos['informacion'];
        $sRaw = @$aDatos['raw'];
        $nChecksum = $aDatos['checksum'];
        $sTipo = $aDatos['tipo'];
        $dFecha = date("Y-m-d");
        $tHora = date("H:i:s");

        // agrega el registro de la alarma
        $oConexion = $this->conexion();
        $sQuery = "INSERT INTO logs_comunicacion(" .
                "unidad_id, " .
                "longitud, " .
                "direccion, " .
                "comando, " .
                "informacion, " .
                "raw, " .
                "checksum, " .
                "tipo, " .
                "fecha, " .
                "hora" .
            ") VALUES (" .
                "?, ?, ?, ?, ?, ?, ?, ?, ?, ?" .
            ")";
        $aQueryParams = array(
            $aUnidad["id"],
            $nLongitud,
            $nDireccion,
            $nComando,
            $sInformacion,
            $sRaw,
            $nChecksum,
            $sTipo,
            $dFecha,
            $tHora
        );
        $aResultado = $oConexion->query($sQuery, $aQueryParams);

        return $this->asJson(array(
            "success" => true,
            "message" => "Guardado con éxito",
            "data" => null
        ));
    }
}
