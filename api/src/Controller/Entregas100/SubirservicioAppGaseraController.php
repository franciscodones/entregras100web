<?php

namespace App\Controller\Entregas100;

use Exception;

class SubirservicioAppGaseraController extends AppGaseraController{
     /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Guarda una alarma del sv600
     *
     * @return JsonResponse
     */
    public function subirservicio_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        $oConexion = $this->getConexion();
        $sQuery = "SELECT * " .
            "FROM logs_ws " .
            "WHERE funcion = ? " .
                "AND unidad_id = ? " .
                "AND fecha = ?";
        $aQueryParams = array(
            "surtido_fn",
            $aUnidad["id"],
            date("Y-m-d")
        );
        $aResultado = $oConexion->query($sQuery, $aQueryParams);

        // filtra los logs para que no ejecute repetidos
        $aLogsProcesados = array();
        foreach ($aResultado as $value) {
            $params = json_decode($value["parametros"], true);
            $key = $params["unidad"] . "_" . $params["numero_control"] . "_" . $params["numero_servicio"] . "_" . $params["fecha_operacion"];
            if (!array_key_exists($key, $aLogsProcesados)) {
                $aLogsProcesados[$key] = $value;
            }
        }

        // procesa los logs
        foreach ($aLogsProcesados as $key => &$value) {
            $value["parametros"] = json_decode($value["parametros"], true);
            $curl = curl_init($value["api_url"]);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $value["parametros"]);
            curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            curl_exec($curl);
            curl_close($curl);
        }
        unset($value);

        return $this->asJson(array(
            "success" => true,
            "message" => "Fueron procesados " . count($aLogsProcesados) . " servicios"
        ));
    }
}
