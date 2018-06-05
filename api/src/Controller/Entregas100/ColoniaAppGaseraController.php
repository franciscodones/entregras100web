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

        // Se crea esta tabla debido a que al hacer un inner join entre poblac y padron
        // tarda demasiado cuando el padron es muy grande (ej. culiacan con ~50k clientes)
        // y por otro lado el descargar el catalogo completo de poblac es muy pesado para la tablet
        // (ej. guadalajara con ~4k colonias).
        // Por lo tanto el inner join se hace entre poblac y esta tabla temporal para que demore menos.
        $sQuery = "CREATE TEMPORARY TABLE colonias_app " .
            "SELECT DISTINCT cvepob " .
            "FROM padron " .
            "WHERE cvepob != 0";
        $oConexionPlaza->query($sQuery);

        // obtiene las calles
        $sQuery = "SELECT poblac.cvepob AS id, " .
                "nompob AS descripcion " .
            "FROM poblac " .
            "INNER JOIN colonias_app ON poblac.cvepob = colonias_app.cvepob " .
            "GROUP BY id " .
            "ORDER BY id";
        $aColonias = $oConexionPlaza->query($sQuery);

        // si no existen calles se termina el proceso
        if (count($aColonias) <= 0) {
            throw new Exception("No existe un catalogo de colonias");
        }

        // se agrega una colonia default
        array_unshift(
            $aColonias,
            array(
                "id" => 0,
                "descripcion" => "SIN COLONIA"
            )
        );

        return $this->asJson(array(
            "success" => true,
            "message" => "Colonias",
            "data" => $aColonias,
            "metadata" => array(
                "Registros" => count($aColonias),
                array(
                    "Registros" => count($aColonias)
                )
            )
        ));
    }
}
