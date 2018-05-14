<?php

namespace App\Controller\Entregas100;

use Exception;

class ColoniaAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la lista de colonias
     *
     * @return JsonResponse
     */
    public function colonia_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene la conexion a la bd de la plaza
        $oConexion = $this->getConexion();
        $sQuery = "SELECT plaza.*, " .
                "conexion.ip_te, " .
                "conexion.usuario_te, " .
                "conexion.password_te, " .
                "conexion.base_te " .
            "FROM plaza " .
            "INNER JOIN conexion ON plaza.id = conexion.plaza_id " .
            "WHERE plaza.id = ?";
        $aQueryParams = array($aUnidad['plaza_id']);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        // si no hay info de la plaza se termina el proceso
        if (count($aResultado) <= 0) {
            throw new Exception("Error al obtener los datos de conexion a la plaza");
        }
        $aPlaza = $aResultado[0];
        $oConexionPlaza = $this->getConexion(
            $aPlaza["plaza"],
            array(
                "host" => $aPlaza["ip_te"],
                "user" => $aPlaza["usuario_te"],
                "password" => $aPlaza["password_te"],
                "database" => $aPlaza["base_te"]
            )
        );

        // obtiene las colonias
        $sQuery = "SELECT colonias.cvepob, " .
                "nompob " .
            "FROM poblac AS colonias " .
            "INNER JOIN padron ON colonias.cvepob = padron.cvepob " .
            "WHERE colonias.cvepob != 0 " .
            "GROUP BY colonias.cvepob";
        $aColonias = $oConexionPlaza->query($sQuery);

        // si no existen calles se termina el proceso
        if (count($aColonias) <= 0) {
            throw new Exception("No existe un catalogo de colonias");
        }

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aColoniasProcesadas = array(
            array(
                "id" => 0,
                "descripcion" => "SIN COLONIA"
            )
        );
        foreach ($aColonias as $value) {
            $aColoniasProcesadas[] = array(
                "id" => $value['cvepob'],
                "descripcion" => utf8_encode($value['nompob'])
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Colonias",
            "data" => $aColoniasProcesadas,
            "metadata" => array(
                "Registros" => count($aColoniasProcesadas),
                array(
                    "Registros" => count($aColoniasProcesadas)
                )
            )
        ));
    }
}
