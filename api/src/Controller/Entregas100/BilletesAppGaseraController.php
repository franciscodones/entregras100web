<?php

namespace App\Controller\Entregas100;

use Exception;

class BilletesAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la lista de billetes
     *
     * @return JsonResponse
     */
    public function billetes_fn() {
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

        // obtiene los litrogas
        $sQuery = "SELECT * " .
            "FROM billetes " .
            "WHERE valor = ? " .
            "ORDER BY ncontrol";
        $aQueryParams = array(0);
        $aBilletes = $oConexionPlaza->query($sQuery, $aQueryParams);

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aBilletesProcesados = array();
        foreach ($aBilletes as $value) {
            $aBilletesProcesados[] = array(
                "id" => $value["id"],
                "numero_control" => $value['ncontrol'],
                "fecha" => $value['fecha'],
                "valor" => $value['valor']
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Billetes",
            "data" => $aBilletesProcesados,
            "metadata" => array(
                "Registros" => count($aBilletesProcesados),
                array(
                    "Registros" => count($aBilletesProcesados)
                )
            )
        ));
    }
}
