<?php

class AlarmasAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la lista de alarmas
     *
     * @return JsonResponse
     */
    public function alarmas_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene las alarmas de la plaza
        $oConexion = $this->conexion();
        $sQuery = "SELECT * FROM alarma WHERE estatus != ? ORDER BY id";
        $aQueryParams = array("B");
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $aAlarmas = $this->parsearQueryResult($aResultado);

        // si no existen alarmas se termina el proceso
        if (count($aAlarmas) <= 0) {
            throw new Exception("No existe un catalogo de alarmas");
        }

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aAlarmasProcesadas = array();
        foreach ($aAlarmas as $value) {
            $aAlarmasProcesadas[] = array(
                "id" => $value['id'],
                "alarma" => $value['alarma'],
                "descripcion" => $value['descripcion']
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Alarmas",
            "data" => $aAlarmasProcesadas,
            "metadata" => array(
                "Registros" => count($aAlarmasProcesadas),
                array(
                    "Registros" => count($aAlarmasProcesadas)
                )
            )
        ));
    }
}
