<?php

class PosicionAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Agrega un registro con la posicion de la unidad
     *
     * @return JsonResponse
     */
    public function posicion_fn() {
        $nParametrosFn = 3;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // agrega el registro de la posicion
        $oConexion = $this->conexion();
        $sQuery = "INSERT INTO posiciones_unidades (" .
            "latitud, " .
            "longitud, " .
            "unidad_id, " .
            "fecha) " .
            "VALUES (?, ?, ?, ?)";
        $aQueryParams = array(
            $aDatos["latitud"],
            $aDatos["longitud"],
            $aUnidad["id"],
            $aDatos["fecha"]
        );
        $aResultado = $oConexion->query($sQuery, $aQueryParams);

        // actualizar la posicion en la tabla de unidades
        $sQuery = "UPDATE unidad " .
            "SET latitud = ?, " .
                "longitud = ? " .
            "WHERE id = ?";
        $aQueryParams = array(
            $aDatos["latitud"],
            $aDatos["longitud"],
            $aUnidad["id"],
        );
        $aResultado = $oConexion->query($sQuery, $aQueryParams);

        return $this->asJson(array(
            "success" => true,
            "message" => "Posicion Guardada",
            "data" => null
        ));
    }
}
