<?php

namespace App\Controller;

use Exception;

class PerfilesPagoController extends AppController {

    /**
     * Lee el catalogo de perfiles de pago
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();

        // obtiene todas las formas de pago
        $sQuery = "SELECT * FROM tipo_cliente";
        $aPerfilesPago = $oConexion->query($sQuery);

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de formas de pago",
            "records" => $aPerfilesPago,
            "metadata" => array(
                "total_registros" => count($aPerfilesPago)
            )
        ));
    }

    /**
     * Actualiza los perfiles de pago
     * @return JsonResponse
     */
    public function update() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aDatos["records"] = json_decode($aDatos["records"], true);

        // actualiza el registro de la zona
        $sQuery = "UPDATE tipo_cliente SET " .
                "descripcion = ?, " .
                "forma_pago_id = ? " .
            "WHERE id = ?";
        foreach ($aDatos["records"] as $record) {
            $aQueryParams = array(
                $record["descripcion"],
                $record["forma_pago_id"],
                $record["id"],
            );
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Perfiles de pago actualizados"
        ));
    }

    /**
     * Crea las combinaciones de las formas de pago - perfil de pago
     * @return JsonResponse
     */
    public function createCombinacionesFormaPerfil() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // elimina los registros de las combinaciones
        $sQuery = "INSERT INTO combinacion_cliente_pago (" .
                "tipo_cliente_id, " .
                "forma_pago_id " .
            ") VALUES (" .
                "?, ?" .
            ")";
        foreach ($aRecords as &$aRecord) {
            $aRecord["clientId"] = $aRecord["id"];
            $aQueryParams = array(
                $aRecord["tipo_cliente_id"],
                $aRecord["forma_pago_id"]
            );
            $oConexion->query($sQuery, $aQueryParams);
            $aRecord["id"] = $oConexion->driver()->lastInsertId();
        }
        unset($aRecord);

        // procesa los records para regresarlos y que los campos se actualicen en el store
        $aRecords = array_map(function($aRecord) {
            return array(
                "id" => $aRecord["id"],
                "clientId" => $aRecord["clientId"]
            );
        }, $aRecords);

        return $this->asJson(array(
            "success" => true,
            "message" => "Combinaciones de forma de pago - perfil de pago agregadas",
            "records" => $aRecords
        ));
    }

    /**
     * Lee las combinaciones de las formas de pago - plaza
     * @return JsonResponse
     */
    public function readCombinacionesFormaPerfil() {
        $oConexion = $this->getConexion();

        // obtiene todas las combinaciones
        $sQuery = "SELECT combinacion_cliente_pago.id AS id, " .
                "tipo_cliente.id AS tipo_cliente_id, " .
                "tipo_cliente.descripcion AS tipo_cliente, " .
                "forma_pago.id AS forma_pago_id, " .
                "forma_pago.descripcion AS forma_pago, " .
                "tipo_cliente.forma_pago_id = combinacion_cliente_pago.forma_pago_id AS es_default " .
            "FROM combinacion_cliente_pago " .
            "INNER JOIN forma_pago ON combinacion_cliente_pago.forma_pago_id = forma_pago.id " .
            "INNER JOIN tipo_cliente ON combinacion_cliente_pago.tipo_cliente_id = tipo_cliente.id " .
            "ORDER BY tipo_cliente_id, forma_pago_id";
        $aCombinaciones = $oConexion->query($sQuery);

        return $this->asJson(array(
            "success" => true,
            "message" => "Combinaciones de forma de pago - perfil de pago",
            "records" => $aCombinaciones,
            "metadata" => array(
                "total_registros" => count($aCombinaciones)
            )
        ));
    }

    /**
     * Elimina las combinaciones de las formas de pago - perfil de pago
     * @return JsonResponse
     */
    public function destroyCombinacionesFormaPerfil() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // elimina los registros de las combinaciones
        $sQuery = "DELETE FROM combinacion_cliente_pago " .
            "WHERE forma_pago_id = ? " .
            "AND tipo_cliente_id = ?";
        foreach ($aRecords as $aRecord) {
            $aQueryParams = array(
                $aRecord["forma_pago_id"],
                $aRecord["tipo_cliente_id"]
            );
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Combinaciones de forma de pago - perfil de pago eliminadas",
        ));
    }
}
