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

        // Se crea esta tabla debido a que al hacer un inner join entre calles y padron
        // tarda demasiado cuando el padron es muy grande (ej. culiacan con ~50k clientes)
        // y por otro lado el descargar el catalogo completo de calles es muy pesado para la tablet
        // (ej. guadalajara con ~16k calles).
        // Por lo tanto el inner join se hace entre calles y esta tabla temporal para que demore menos.
        $sQuery = "CREATE TEMPORARY TABLE calles_app " .
            "SELECT DISTINCT cvecall " .
            "FROM padron " .
            "WHERE cvecall != 0";
        $oConexionPlaza->query($sQuery);

        // obtiene las calles
        $sQuery = "SELECT calles.cvecall AS id, " .
                "nomcalle AS descripcion, " .
                "1 AS colonia_id " .
            "FROM calles " .
            "INNER JOIN calles_app ON calles.cvecall = calles_app.cvecall " .
            "ORDER BY id";
        $aCalles = $oConexionPlaza->query($sQuery);

        // si no existen calles se termina el proceso
        if (count($aCalles) <= 0) {
            throw new Exception("No existe un catalogo de calles");
        }

        // se agrega una calle default
        array_unshift(
            $aCalles,
            array(
                "id" => 0,
                "descripcion" => "SIN CALLE",
                "colonia_id" => 1
            )
        );

        return $this->asJson(array(
            "success" => true,
            "message" => "Calles",
            "data" => $aCalles,
            "metadata" => array(
                "Registros" => count($aCalles),
                array(
                    "Registros" => count($aCalles)
                )
            )
        ));
    }
}
