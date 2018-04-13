<?php

class OperadorAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la lista de operadores
     *
     * @return JsonResponse
     */
    public function operador_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene los operadores de la plaza y los multiplaza
        $oConexion = $this->conexion();
        $sQuery = "SELECT * " .
            "FROM operador " .
            "WHERE tipo_usuario_id IN (1, 2, 3, 4) " .
            "AND (plaza_id = ? OR plazas = 1)";
        $aQueryParams = array($aUnidad["plaza_id"]);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $aOperadores = $this->parsearQueryResult($aResultado);

        // si no existen operadores se termina el proceso
        if (count($aOperadores) <= 0) {
            return $this->asJson(array(
                "success" => false,
                "message" => "No existe un catalogo de usuarios",
                "data" => null,
                "metadata" => array(
                    "Registros" => 0,
                    array(
                        "Registros" => 0
                    )
                )
            ));
        }

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aOperadoresProcesados = array();
        foreach ($aOperadores as $value) {
            $aOperadoresProcesados[] = array(
                "id" => $value['id'],
                "nombre" => $value['nombre'],
                "nip" => $value['nip'],
                "tipo_usuario_id" => $value['tipo_usuario_id']
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Operadores",
            "data" => $aOperadoresProcesados,
            "metadata" => array(
                "Registros" => 0,
                array(
                    "Registros" => count($aOperadoresProcesados)
                )
            )
        ));
    }
}
