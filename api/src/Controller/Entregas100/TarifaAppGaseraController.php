<?php

namespace App\Controller\Entregas100;

use Exception;

class TarifaAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Descarga el catalogo de tarifas
     *
     * @return JsonResponse
     */
    public function tarifa_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        $oConexion = $this->getConexion();

        // obtiene la conexion a la bd de la plaza
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

        // obtiene los registros de las tarifas
        $sQuery = "SELECT * FROM tarifas ORDER BY cvetar";
        $aTarifas = $oConexionPlaza->query($sQuery);

        $bCobroAditivo = $aUnidad['cobro_aditivo'] == 1;
        $bAditivoObligatorio = $aUnidad['aditivo_obligatorio'] == 1;
        $aTarifasProcesadas = array();

        // encuentra la tarifa base de la plaza.
        // esta tarifa es usada para la tarifa 0 y para remplazar el precio de aditivo
        // de acuerdo a las configuraciones de aditivo que tenga la unidad
        $aTarifaBase = array_values(array_filter($aTarifas, function($aTarifa) use ($aPlaza) {
            return $aTarifa["cvetar"] == $aPlaza["tarifa_id"];
        }));
        $aTarifaBase = count($aTarifaBase) > 0 ? $aTarifaBase[0] : null;

        // si no hay una tarifa base se marca error
        if ($aTarifaBase == null) {
            throw new Exception("Error al obtener la tarifa base de la plaza");
        }

        // agrega la tarifa 0 al principio del array
        array_unshift($aTarifas, array(
            "cvetar" => 0,
            "precio2" => $aTarifaBase["precio2"],
            "aditivo2" => $aTarifaBase["aditivo2"]
        ));

        // procesa todas las tarifas de acuerdo a la configuracion de aditivo de la unidad
        foreach ($aTarifas as $aTarifa) {
            if (!$bCobroAditivo && !$bAditivoObligatorio) {
                // si la unidad no trae aditivo y no obliga cobrarlo, entonces
                // el precio del aditivo se toma de la tarifa
                $nPrecioAditivo = $aTarifa["aditivo2"];
            } else if (!$bCobroAditivo && $bAditivoObligatorio) {
                // si la unidad no trae aditivo y obliga a no cobrarlo, entonces
                // el precio del aditivo sera 0
                $nPrecioAditivo = 0;
            } else if ($bCobroAditivo && !$bAditivoObligatorio) {
                // si la unidad trae aditivo y no obliga a cobrarlo, entonces
                // el precio del aditivo se toma de la tarifa
                $nPrecioAditivo = $aTarifa["aditivo2"];
            } else {
                // por ultimo no es ninguna de las anteriores, entonces
                // la unidad trae aditivo y se obliga a que este se pague
                // el precio del aditivo de la tarifa base
                $nPrecioAditivo = $aTarifaBase["aditivo2"];
            }

            // se agrega la tarifa procesada
            $aTarifasProcesadas[] = array(
                "id" => $aTarifa["cvetar"],
                "precio_gas" => $aTarifa["precio2"],
                "precio_aditivo" => $nPrecioAditivo
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Tarifas encontradas",
            "data" => $aTarifasProcesadas,
            "metadata" => array(
                array(
                    "Registros" => count($aTarifasProcesadas)
                )
            )
        ));
    }
}
