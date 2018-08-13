<?php

namespace App\Controller\Cron;

use DateTime;
use App\Controller\AppController;
use Cake\Mailer\Email;

class AlarmasCronController extends AppController {

    public function enviarEmailReporteAlarmas() {
        $oConexion = $this->getConexion();
        $dNow = new DateTime();

        $dFechaAlarmas = date("Y-m-d");

        /*$sQuery = "SELECT logs_alarmas.*, " .
                "unidad.unidad, " .
                "plaza.id AS plaza_id, " .
                "plaza.ciudad AS plaza " .
            "FROM logs_alarmas " .
            "INNER JOIN unidad ON logs_alarmas.unidad_id = unidad.id " .
            "INNER JOIN zona ON unidad.zona_id = zona.id " .
            "INNER JOIN plaza ON zona.plaza_id = plaza.id " .
            "WHERE fecha = ?";
        $aQueryParams = array($dFechaAlarmas);
        $aAlarmas = $oConexion->query($sQuery, $aQueryParams);*/

        // configuracion del email tomado del archivo config\app.php
        pr(Email::configTransport("default"));
        exit;

        // envio del correo a los diferentes destinatarios
    }
}
