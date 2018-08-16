<?php

namespace App\Controller\Cron;

use App\Controller\AppController;

class LogsCronController extends AppController {

    public function eliminar() {
        set_time_limit(300);
        $oConexion = $this->getConexion();

        // elimina los logs_ws
        $sQuery = "DELETE FROM logs_ws " .
            "WHERE fecha < DATE_SUB(CURDATE(), INTERVAL ? DAY)";
        $aQueryParams = array(
            5
        );
        $oConexion->query($sQuery, $aQueryParams);

        // elimina los logs_comunicacion
        $sQuery = "DELETE FROM logs_comunicacion " .
            "WHERE fecha < DATE_SUB(CURDATE(), INTERVAL ? DAY)";
        $aQueryParams = array(
            5
        );
        $oConexion->query($sQuery, $aQueryParams);

        // elimina los logs_error
        $sQuery = "DELETE FROM logs_error " .
            "WHERE fecha < DATE_SUB(CURDATE(), INTERVAL ? DAY)";
        $aQueryParams = array(
            5
        );
        $oConexion->query($sQuery, $aQueryParams);

        // elimina los logs_alarmas
        $sQuery = "DELETE FROM logs_alarmas " .
            "WHERE fecha < DATE_SUB(CURDATE(), INTERVAL ? DAY)";
        $aQueryParams = array(
            7
        );
        $oConexion->query($sQuery, $aQueryParams);

        $json = array(
            "success" => true,
            "message" => "Logs eliminados"
        );
        return $this->asJson($json);
    }
}
