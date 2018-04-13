<?php

class Combinacioncliente_pagoAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la lista de combinaciones de perfil de pago - forma de pago
     *
     * @return JsonResponse
     */
    public function combinacioncliente_pago_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene las combinaciones perfil de pago - forma de pago
        $oConexion = $this->conexion();
        $sQuery = "SELECT combinacion_cliente_pago.* " .
            "FROM combinacion_cliente_pago " .
            "INNER JOIN combinacion_forma_plaza " .
                "ON combinacion_cliente_pago.forma_pago_id = combinacion_forma_plaza.forma_pago_id " .
            "WHERE combinacion_forma_plaza.plaza_id = ? " .
            "ORDER BY tipo_cliente_id, combinacion_cliente_pago.forma_pago_id";
        $aQueryParams = array($aUnidad["plaza_id"]);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $aCombinaciones = $this->parsearQueryResult($aResultado);

        // si no existen bancos se termina el proceso
        if (count($aCombinaciones) <= 0) {
            throw new Exception("No existe un catalogo de combinaciones de perfil de pago - forma de pago");
        }

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aCombinacionesProcesadas = array();
        foreach ($aCombinaciones as $value) {
            $aCombinacionesProcesadas[] = array(
                "id" => $value['id'],
                "tipo_cliente_id" => $value['tipo_cliente_id'],
                "forma_pago_id" => $value['forma_pago_id']
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Combinaciones de perfil de pago - forma de pago",
            "data" => $aCombinacionesProcesadas,
            "metadata" => array(
                "Registros" => count($aCombinacionesProcesadas),
                array(
                    "Registros" => count($aCombinacionesProcesadas)
                )
            )
        ));
    }
}
