<?php

class TipoclienteAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna los perfiles de pago
     *
     * @return JsonResponse
     */
    public function tipocliente_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene los perfiles de pago
        $oConexion = $this->conexion();
        $sQuery = "SELECT * FROM tipo_cliente";
        $aResultado = $oConexion->query($sQuery);
        $aPerfiles = $this->parsearQueryResult($aResultado);

        // si no existen alarmas se termina el proceso
        if (count($aPerfiles) <= 0) {
            throw new Exception("No existe un catalogo de perfiles de pago");
        }

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aPerfilesProcesados = array();
        foreach ($aPerfiles as $value) {
            $aPerfilesProcesados[] = array(
                "id" => $value['id'],
                "descripcion" => $value['descripcion'], // se cambio la columna `descripcion` a `tipo_cv`
                "forma_pago_id" => $value['forma_pago_id']
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Perfiles de pago",
            "data" => $aPerfilesProcesados,
            "metadata" => array(
                "Registros" => count($aPerfilesProcesados),
                array(
                    "Registros" => count($aPerfilesProcesados)
                )
            )
        ));
    }
}
