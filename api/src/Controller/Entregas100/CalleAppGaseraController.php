<?php

namespace App\Controller\Entregas100;

use Exception;

class CalleAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la lista de calles
     *
     * @return JsonResponse
     */
    public function calle_fn() {
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

        // obtiene las calles
        $sQuery = "SELECT calles.cvecall, " .
                "nomcalle " .
            "FROM calles " .
            "INNER JOIN padron ON calles.cvecall = padron.cvecall " .
            "WHERE calles.cvecall != 0 " .
            "GROUP BY calles.cvecall";
        $aCalles = $oConexionPlaza->query($sQuery);

        // si no existen calles se termina el proceso
        if (count($aCalles) <= 0) {
            throw new Exception("No existe un catalogo de calles");
        }

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aCallesProcesadas = array(
            array(
                "id" => 0,
                "descripcion" => "SIN CALLE",
                "colonia_id" => 1
            )
        );
        foreach ($aCalles as $value) {
            $aCallesProcesadas[] = array(
                "id" => $value['cvecall'],
                "descripcion" => utf8_encode($value['nomcalle']),
                "colonia_id" => 1
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Calles",
            "data" => $aCallesProcesadas,
            "metadata" => array(
                "Registros" => count($aCallesProcesadas),
                array(
                    "Registros" => count($aCallesProcesadas)
                )
            )
        ));
    }
}
