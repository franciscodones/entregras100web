<?php

class MotivorecirculacionAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la lista de motivos de recirculacion
     *
     * @return JsonResponse
     */
    public function motivorecirculacion_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene los motivos de recirculacion
        $oConexion = $this->conexion();
        $sQuery = "SELECT  * FROM motivo_recirculacion";
        $aResultado = $oConexion->query($sQuery);
        $aMotivos = $this->parsearQueryResult($aResultado);

        // si no existen alarmas se termina el proceso
        if (count($aMotivos) <= 0) {
            throw new Exception("No existe un catalogo de motivos de recirculacion");
        }

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aMotivosProcesados = array();
        foreach ($aMotivos as $value) {
            $aMotivosProcesados[] = array(
                "id" => $value['id'],
                "descripcion" => $value['descripcion']
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Motivos de recirculacion",
            "data" => $aMotivosProcesados,
            "metadata" => array(
                "Registros" => count($aMotivosProcesados),
                array(
                    "Registros" => count($aMotivosProcesados)
                )
            )
        ));
    }
}
