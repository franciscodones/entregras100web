<?php

namespace App\Controller\Entregas100;

use Exception;

class DescuentospromocionAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la tabla de puntos
     *
     * @return JsonResponse
     */
    public function descuentospromocion_fn() {
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

        // obtiene la tabla de puntos
        $sQuery = "SELECT * FROM descuentos_promocion";
        $aDescuentos = $oConexionPlaza->query($sQuery);

        return $this->asJson(array(
            "success" => true,
            "message" => "Descuentos promocion",
            "data" => $aDescuentos,
            "metadata" => array(
                "Registros" => count($aDescuentos),
                array(
                    "Registros" => count($aDescuentos)
                )
            )
        ));
    }
}
