<?php

namespace App\Controller\Cron;

use DateTime;
use App\Controller\AppController;
use Cake\Mailer\Email;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_PageSetup;
use PHPExcel_Style;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;

class AlarmasCronController extends AppController {

    public $aDestinatarios = array(
        "MZT" => array(
            "josemaria@gaspasa.com.mx",
            "plantamz@gaspasa.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "CLN" => array(
            "operacionescln@gaspasa.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "LCR" => array(
            "gerencialc@gaspasa.com.mx",
            "josemaria@gaspasa.com.mx",
            "plantalacruz@gaspasa.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "ESC" => array(
            "gpaes1@gaspasa.com.mx",
            "josemaria@gaspasa.com.mx",
            "plantaesc@gaspasa.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "SAL" => array(
            "jcuevas@diesgas.com.mx",
            "plantagdl@diesgas.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "LPZ" => array(
            "jurias@caligas.com.mx",
            "plantalp@caligas.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "GVE" => array(
            "jorgezazueta@gaspasa.com.mx",
            "plantagve@gaspasa.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "GME" => array(
            "neubojorquez@gaspasa.com.mx",
            "plantagm@gaspasa.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "QRE" => array(
            "ibojorquez@caligas.com.mx",
            "plantaqro@diesgas.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "CYE" => array(
            "gilbertol@diesgas.com.mx",
            "avaldez@diesgas.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "IRE" => array(
            "gerenciairapuato@diesgas.com.mx",
            "operacionirp@diesgas.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "HME" => array(
            "armandohiguera@diesgas.com.mx",
            "plantahermosillo@diesgas.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "LMS" => array(
            "javierapodaca@gaspasa.com.mx",
            "plantalm@gaspasa.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "OBR" => array(
            "edgarquintero@gaspasa.com.mx",
            "plantahq@diesgas.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "NVJ" => array(
            "administrativohq@gaspasa.com.mx",
            "operacionesnav@gaspasa.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "SJC" => array(
            "smoreno@caligas.com.mx",
            "plantasj@caligas.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "SLE" => array(
            "smoreno@caligas.com.mx",
            "plantasl@caligas.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "SRE" => array(
            "jurias@caligas.com.mx",
            "operacionesbcs@gaspasa.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "LOE" => array(
            "jurias@caligas.com.mx",
            "operacionesbcs@gaspasa.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "CCE" => array(
            "jurias@caligas.com.mx",
            "operacionesbcs@gaspasa.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "GNE" => array(
            "jurias@caligas.com.mx",
            "operacionesbcs@gaspasa.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "ENE" => array(
            "kfigueroa@gaspasa.com.mx",
            "operativoens@gaspasa.com.mx",
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        ),
        "TJE" => array(
            "luisrodriguez@gaspasa.com.mx",
            "rquintero@gaspasa.com.mx",
            "b.venegas@pyansa.com.mx"
        )
    );

    public function enviarEmailReporteAlarmas() {
        set_time_limit(300);
        $oConexion = $this->getConexion();
        $dFechaAlarmas = new DateTime();
        $sRegexAlarma = "\"[1-8]\":[1-9][0-9]?";
        $aAlarmasPlaza = null;
        $aExcel = null;
        $oEmail = null;
        $oExcelWriter = null;
        $sExcelString = null;
        $aEmails = array();

        // se obtiene el catalogo de plazas
        $sQuery = "SELECT id, " .
                "plaza, " .
                "ciudad " .
            "FROM plaza";
        $aPlazas = $oConexion->query($sQuery);

        // se obtiene el catalogo de alarmas
        $sQuery = "SELECT * FROM alarma";
        $aCatalogoAlarmas = $oConexion->query($sQuery);

        // obtiene los logs de las alarmas
        $sQuery = "SELECT logs_alarmas.*, " .
                "unidad.unidad, " .
                "plaza.id AS plaza_id, " .
                "plaza.ciudad AS plaza " .
            "FROM logs_alarmas " .
            "INNER JOIN unidad ON logs_alarmas.unidad_id = unidad.id " .
            "INNER JOIN zona ON unidad.zona_id = zona.id " .
            "INNER JOIN plaza ON zona.plaza_id = plaza.id " .
            "WHERE logs_alarmas.fecha = ? " .
            "AND (alarma REGEXP ? OR litros_no_autorizados >= ?) " .
            "ORDER BY plaza, unidad.unidad, hora";
        $aQueryParams = array(
            $dFechaAlarmas->format("Y-m-d"),
            $sRegexAlarma,
            5
        );
        $aLogsAlarmas = $oConexion->query($sQuery, $aQueryParams);

        // por cada plaza se creara un archivo excel solo si la plaza tiene alarmas
        foreach ($aPlazas as $aPlaza) {
            // filtra las alarmas de la plaza
            $aLogsAlarmasPlaza = array_filter($aLogsAlarmas, function($item) use ($aPlaza) {
                return $item["plaza_id"] == $aPlaza["id"];
            });
            if (empty($aLogsAlarmasPlaza) || empty($this->aDestinatarios[$aPlaza["plaza"]])) {
                continue;
            }

            // genera el archivo excel con las alarmas
            $oExcel = $this->generarExcelPlaza($aPlaza, $aCatalogoAlarmas, $aLogsAlarmasPlaza);
            // guarda el excel en un archivto temporal el cual es impriso en el output buffer de php,
            // el output buffer es leido y el contenido es guardado en una variable.
            // En otras palabras se parsea el excel a un string
            $oExcelWriter = PHPExcel_IOFactory::createWriter($oExcel, 'Excel2007');
            @ob_start();
            $oExcelWriter->save("php://output");
            $sExcelString = @ob_get_contents();
            @ob_end_clean();

            // se guarda el string en el array de excels
            $oEmail = new Email();
            $oEmail->addTo($this->aDestinatarios[$aPlaza["plaza"]])
                ->subject("REPORTE DE ALARMAS DE " . $aPlaza["ciudad"] . " " . $dFechaAlarmas->format("d/m/Y"))
                ->attachments(array(
                    "REPORTE ALARMAS " . $aPlaza["ciudad"] . " " . $dFechaAlarmas->format("d-m-Y") . ".xlsx" => array(
                        "data" => $sExcelString,
                        "mimetype" => $this->response->getMimeType("xlsx")
                    )
                ));
            $aEmails[] = $oEmail;
        }

        // se configuran los correos y se envian
        foreach ($aEmails as $oEmail) {
            $oEmail->send("Reporte de alarmas detectadas el dia " . date("d/m/Y"));
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Reporte enviado"
        ));
    }

    /**
     * Crea un excel con las alarmas de la plaza
     *
     * @param  array $aPlaza
     * @param  array $aAlarmas
     * @return PHPExcel
     */
    private function generarExcelPlaza($aPlaza, $aCatalogoAlarmas, $aLogsAlarmasPlaza)
    {
        $oExcel = new PHPExcel();
        $nMargin = 0.5 / 2.54;
        $aLogsProcesados = array(
            0 => array(),
            1 => array(),
            2 => array(),
            3 => array(),
            4 => array(),
            5 => array(),
            6 => array(),
            7 => array(),
            8 => array(),
        );
        $nRowIndex = 3;

        // procesa y agrupa las alarmas
        foreach ($aLogsAlarmasPlaza as $key => $aLogAlarma) {
            if ($aLogAlarma["litros_no_autorizados"] && $aLogAlarma["litros_no_autorizados"] >= 5) {
                $aLogsProcesados[0][] = array(
                    "alarma_id" => 0,
                    "alarma" => "LITROS NO AUTORIZADOS",
                    "cantidad" => null,
                    "unidad_id" => $aLogAlarma["unidad_id"],
                    "unidad" => $aLogAlarma["unidad"],
                    "numero_servicio" => $aLogAlarma["servicio"],
                    "fecha" => $aLogAlarma["fecha"],
                    "hora" => $aLogAlarma["hora"],
                    "litros" => null,
                    "litros_no_autorizados" => $aLogAlarma["litros_no_autorizados"],
                    "numero_control" => $aLogAlarma["numero_control"],
                    "plaza_id" => $aLogAlarma["plaza_id"],
                    "plaza" => $aLogAlarma["plaza"],
                );
            }
            $aAlarmasArray = json_decode($aLogAlarma["alarma"], true);
            foreach ($aAlarmasArray as $index => $cantidad) {
                if ($cantidad > 0) {
                    $aLogsProcesados[$index][] = array(
                        "alarma_id" => $index,
                        "alarma" => $aCatalogoAlarmas[$index]["alarma"],
                        "cantidad" => $cantidad,
                        "unidad_id" => $aLogAlarma["unidad_id"],
                        "unidad" => $aLogAlarma["unidad"],
                        "numero_servicio" => $aLogAlarma["servicio"],
                        "fecha" => $aLogAlarma["fecha"],
                        "hora" => $aLogAlarma["hora"],
                        "litros" => $aLogAlarma["litros"],
                        "litros_no_autorizados" => null,
                        "numero_control" => $aLogAlarma["numero_control"],
                        "plaza_id" => $aLogAlarma["plaza_id"],
                        "plaza" => $aLogAlarma["plaza"],
                    );
                }
            }
        }

        // asigna las propiedades default del excel
        $oExcel->getProperties()
            ->setCreator("Grupo Alerta")
            ->setLastModifiedBy("Grupo Alerta");

        // crea y configura la hoja
        $oSheet = $oExcel->getActiveSheet()->setTitle($aPlaza["ciudad"]);
        $oSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
        $oSheet->getPageMargins()
            ->setTop($nMargin)
            ->setBottom($nMargin)
            ->setLeft($nMargin)
            ->setRight($nMargin);

        // titulo
        $oSheet->setCellValue("A1", "Reporte de Alarmas")
            ->mergeCells("A1:H1")
            ->setSharedStyle(
                (new PHPExcel_Style())->applyFromArray(array(
                    'alignment' => array(
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    ),
                    'font' => array(
                        'size' => 18,
                        'name' => 'Arial',
                        "family" => "Swiss",
                        'color' => array(
                            'rgb' => '1F497D'
                        )
                    ),
                )),
                "A1"
            )
            ->getRowDimension(1)->setRowHeight(22.5);

        // headers
        $oSheet->fromArray(
            array(
                "Unidad",
                "No. de servicio",
                "Fecha",
                "Hora",
                "Cantidad",
                "Litros",
                "Litros no autorizados",
                "Control"
            ),
            null,
            "A2"
        );
        $oSheet->setSharedStyle(
            (new PHPExcel_Style())->applyFromArray(array(
                'alignment' => array(
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                ),
                'font' => array(
                    'size' => 11,
                    'name' => 'Arial',
                    "family" => "Swiss",
                    'color' => array(
                        'rgb' => '1F497D'
                    )
                ),
                'borders' => array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array(
                            'rgb' => '4F81BD'
                        )
                    )
                 )
            )),
            "A2:H2"
        )
        ->getRowDimension(2)->setRowHeight(20.25);
        $oSheet->getColumnDimension("A")->setWidth(7.70);
        $oSheet->getColumnDimension("B")->setWidth(14.30);
        $oSheet->getColumnDimension("C")->setWidth(11.30);
        $oSheet->getColumnDimension("D")->setWidth(9.30);
        $oSheet->getColumnDimension("E")->setWidth(8.70);
        $oSheet->getColumnDimension("F")->setWidth(7.30);
        $oSheet->getColumnDimension("G")->setWidth(21.30);
        $oSheet->getColumnDimension("H")->setWidth(8.30);

        // grupos
        foreach ($aLogsProcesados as $key => $aLogs) {
            if (!empty($aLogs)) {
                // header del grupo
                $oSheet->setCellValue("A" . $nRowIndex, $aLogs[0]["alarma"])
                    ->mergeCells("A" . $nRowIndex . ":H" . $nRowIndex)
                    ->setSharedStyle(
                        (new PHPExcel_Style())->applyFromArray(array(
                            'borders' => array(
                                'bottom' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                    'color' => array(
                                        'rgb' => '4F81BD'
                                    )
                                )
                             )
                        )),
                        "A" . $nRowIndex . ":H" . $nRowIndex
                    );
                $nRowIndex++;

                // logs
                foreach ($aLogs as $aLog) {
                    $oSheet->fromArray(
                        array(
                            $aLog["unidad"],
                            $aLog["numero_servicio"],
                            $aLog["fecha"],
                            $aLog["hora"],
                            $aLog["cantidad"],
                            $aLog["litros"],
                            $aLog["litros_no_autorizados"],
                            $aLog["numero_control"]
                        ),
                        null,
                        "A" . $nRowIndex
                    );
                    $nRowIndex++;
                }
            }
        }

        return $oExcel;
    }
}
