<?php

namespace App\Controller\Entregas100;

use Exception;

class FormapagoAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la lista de formas de pago
     *
     * @return JsonResponse
     */
    public function formapago_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene las formas de pago
        $oConexion = $this->getConexion();
        $sQuery = "SELECT forma_pago.*, " .
                "IF(litros IS NULL, 0, litros) AS limite " .
            "FROM forma_pago " .
            "LEFT JOIN limites_litro ON forma_pago.id = forma_pago_id AND plaza_id = ? " .
            "INNER JOIN combinacion_forma_plaza ON forma_pago.id = combinacion_forma_plaza.forma_pago_id " .
            "WHERE combinacion_forma_plaza.plaza_id = ? " .
            "ORDER BY forma_pago.id";
        $aQueryParams = array($aUnidad["plaza_id"], $aUnidad["plaza_id"]);
        $aFormasPago = $oConexion->query($sQuery, $aQueryParams);

        // si no existen alarmas se termina el proceso
        if (count($aFormasPago) <= 0) {
            throw new Exception("No existe un catalogo de formas de pago");
        }

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $nTipoVentaId = 1;
        $aFormasPagoProcesadas = array();
        foreach ($aFormasPago as $value) {
            switch ($value['id']) {
                case 8 :
                    $nTipoVentaId = 2;
                    break;
                case 9 :
                    $nTipoVentaId = 3;
                    break;
                case 10 :
                    $nTipoVentaId = 5;
                    break;
                case 11 :
                    $nTipoVentaId = 7;
                    break;
                case 12 :
                    $nTipoVentaId = 6;
                    break;
                default:
                    $nTipoVentaId = 1;
                    break;
            }
            $aFormasPagoProcesadas[] = array(
                "id" => $value['id'],
                "descripcion" => $value['descripcion'],
                "tipoventa_id" => $nTipoVentaId,
                "limite" => $value['limite'],
                "es_eliminable" => $value['es_eliminable'],
                "es_seleccionable" => $value['es_seleccionable'],
                "es_visible" => $value['es_visible'],
                "orden" => $value['orden']
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Formas de pago",
            "data" => $aFormasPagoProcesadas,
            "metadata" => array(
                "Registros" => count($aFormasPagoProcesadas),
                array(
                    "Registros" => count($aFormasPagoProcesadas)
                )
            )
        ));
    }
}
