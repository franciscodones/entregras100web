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

    public function enviarEmailReporteAlarmas() {
        set_time_limit(300);
        $oConexion = $this->getConexion();
        $dFechaAlarmas = "2018-08-10";//date("Y-m-d");
        $nLitrosNoAutorizados = 5;
        $sRegexAlarma = "\"[1-8]\":[1-9][0-9]?";
        $aAlarmasPlaza = null;
        $aExcels = null;


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
            $dFechaAlarmas,
            $sRegexAlarma,
            $nLitrosNoAutorizados
        );
        $aLogsAlarmas = $oConexion->query($sQuery, $aQueryParams);

        // por cada plaza se creara un archivo excel solo si la plaza tiene alarmas
        $aExcels = array();
        foreach ($aPlazas as $aPlaza) {
            // filtra las alarmas de la plaza
            $aLogsAlarmasPlaza = array_filter($aLogsAlarmas, function($item) use ($aPlaza) {
                return $item["plaza_id"] == $aPlaza["id"];
            });
            if (empty($aLogsAlarmasPlaza)) {
                continue;
            }

            // genera el archivo excel con las alarmas
            $oExcel = $this->generarExcelPlaza($aPlaza, $aCatalogoAlarmas, $aLogsAlarmasPlaza);

            // se guarda en el array de excels
            $aExcels[$aPlaza["plaza"]] = $oExcel;
        }

        // guarda el excel en un archivto temporal el cual es impriso en el output buffer de php,
        // el output buffer es leido y el contenido es guardado en una variable.
        // En otras palabras se parsea el excel a un string
        $oExcelWriter = PHPExcel_IOFactory::createWriter(reset($aExcels), 'Excel2007');
        @ob_start();
        $oExcelWriter->save("php://output");
        $sExcelString = @ob_get_contents();
        @ob_end_clean();

        // DEBUG
        // decarga el excel
        $this->response->body($sExcelString);
        $this->response->type("xlsx");
        $this->response->download("reporte_alarmas.xlsx");

        return $this->response;
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
            if ($aLogAlarma["litros_no_autorizados"]) {
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
