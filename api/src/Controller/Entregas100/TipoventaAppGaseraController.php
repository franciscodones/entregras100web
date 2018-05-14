<?php

namespace App\Controller\Entregas100;

use Exception;

class TipoventaAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna los folios de venta
     *
     * @return JsonResponse
     */
    public function tipoventa_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene los folios de la unidad
        $oConexion = $this->getConexion();
        $sQuery = "SELECT nota, " .
                "puntos, " .
                "litrogas, " .
                "recirculacion, " .
                "consignacion, " .
                "donativo, " .
                "cortesia " .
            "FROM folios " .
            "WHERE unidad_id = ?";
        $aQueryParams = array($aUnidad["id"]);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        // si no existen folios de la unidad se termina el proceso
        if (count($aResultado) <= 0) {
            throw new Exception("No existen folios de esta unidad");
        }
        $aFolios = $aResultado[0];


        // obtiene los tipos de folios
        $oConexion = $this->getConexion();
        $sQuery = "SELECT * FROM tipo_venta";
        $aTiposFolio = $oConexion->query($sQuery);

        // si no existen tipos de folios se termina el proceso
        if (count($aTiposFolio) <= 0) {
            throw new Exception("No existen un catalogo de folios");
        }

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aFoliosProcesados = array();
        foreach ($aTiposFolio as $value) {
            $aFoliosProcesados[] = array(
                "id" => $value['id'],
                "descripcion" => $value['descripcion'],
                "consecutivo" => $aFolios[strtolower($value['descripcion'])]
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Tipos de venta",
            "data" => $aFoliosProcesados,
            "metadata" => array(
                "Registros" => count($aFoliosProcesados),
                array(
                    "Registros" => count($aFoliosProcesados)
                )
            )
        ));
    }
}
