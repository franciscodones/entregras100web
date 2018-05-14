<?php

namespace App\Controller\Entregas100;

use Exception;

class ConciliarservicioAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Concilia los servicios que tiene la app con los servicios en el servidor
     *
     * @return JsonResponse
     */
    public function conciliarservicio_fn() {
        $nParametrosFn = 1;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // valida que se haya recibido un array de servicios de parte de la app
        // de lo contrario se termina el proceso
        $oConexion = $this->getConexion();
        $aServicios = json_decode($aDatos["servicios"], true);
        if (!is_array($aServicios)) {
            throw new Exception("No se envio información válida para conciliar");
        }
        $aServicios = $aServicios["servicios"];

        // busca los servicios y guarda los faltantes
        $aServiciosFaltantes = array();
        $sQuery = "SELECT * " .
            "FROM servicio_atendido " .
            "WHERE unidad_id = ? ".
            "AND fecha = ? " .
            "AND numero_control = ? " .
            "AND numero_servicio = ? " .
            "AND linea_captura = ?";
        foreach ($aServicios as &$aServicioApp) {
            // si el servicio no trae linea de captura se asigna 0
            if (trim($aServicioApp["linea_captura"]) == "") {
                $aServicioApp["linea_captura"] = str_pad("0", 20, "0", STR_PAD_LEFT);
            }

            $aQueryParams = array(
                $aUnidad['id'],
                $aServicioApp["fecha_operacion"],
                $aServicioApp["numero_control"],
                $aServicioApp["numero_servicio"],
                str_pad($aServicioApp["linea_captura"], 20, "0", STR_PAD_LEFT)
            );
            $aResultado = $oConexion->query($sQuery, $aQueryParams);
            // si no encontro el servicio se agrega a los faltantes
            if (count($aResultado) <= 0) {
                $aServiciosFaltantes[] = array(
                    "numero_control" => $aServicioApp["numero_control"],
                    "numero_servicio" => $aServicioApp["numero_servicio"]
                );
            }
        }
        unset($aServicioApp);

        return $this->asJson(array(
            "success" => true,
            "message" => "Información de servicios faltantes obtenida",
            "data" => $aServiciosFaltantes
        ));
    }
}
