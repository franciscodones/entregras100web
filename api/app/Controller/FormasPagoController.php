<?php

class FormasPagoController extends AppController {

    /**
     * Lee el catalogo de formas de pago
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();

        // obtiene todas las formas de pago
        $sQuery = "SELECT * FROM forma_pago";
        $aResultado = $oConexion->query($sQuery);
        $aFormasPago = $this->parsearQueryResult($aResultado);

        // procesa las formas de pago para solo enviar la informacion util
        $aFormasPagoProcesadas = array();
        foreach ($aFormasPago as $aFormaPago) {
            $aFormasPagoProcesadas[] = array(
                "id" => $aFormaPago["id"],
                "descripcion" => $aFormaPago["descripcion"],
                "es_eliminable" => $aFormaPago["es_eliminable"],
                "es_seleccionable" => $aFormaPago["es_seleccionable"],
                "orden" => $aFormaPago["orden"]
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de formas de pago",
            "records" => $aFormasPagoProcesadas,
            "metadata" => array(
                "total_registros" => count($aFormasPagoProcesadas)
            )
        ));
    }

    /**
     * Actualiza una forma de pago
     * @return JsonResponse
     */
    public function update() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aDatos["records"] = json_decode($aDatos["records"], true);

        // actualiza el registro de la zona
        $sQuery = "UPDATE forma_pago SET " .
                "descripcion = ?, " .
                "es_eliminable = ?, " .
                "es_seleccionable = ?, " .
                "orden = ? " .
            "WHERE id = ?";
        foreach ($aDatos["records"] as $record) {
            $aQueryParams = array(
                $record["descripcion"],
                $record["es_eliminable"],
                $record["es_seleccionable"],
                $record["orden"],
                $record["id"],
            );
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Zona actualizada"
        ));
    }

    /**
     * Crea las combinaciones de las formas de pago
     * @return JsonResponse
     */
    public function createCombinacionesFormaPago() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // elimina los registros de las combinaciones
        $sQuery = "INSERT INTO combinacion_forma_pago (" .
                "forma_pago_id, " .
                "forma_pago_id2 " .
            ") VALUES (" .
                "?, ?" .
            ")";
        foreach ($aRecords as &$aRecord) {
            $aRecord["clientId"] = $aRecord["id"];
            $aQueryParams = array(
                $aRecord["forma_pago_id"],
                $aRecord["forma_pago_id2"]
            );
            $oConexion->query($sQuery, $aQueryParams);
            $aRecord["id"] = $oConexion->lastInsertId();
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
            "message" => "Combinaciones de forma de pago agregadas",
            "records" => $aRecords
        ));
    }

    /**
     * Lee las combinaciones de las formas de pago
     * @return JsonResponse
     */
    public function readCombinacionesFormaPago() {
        $oConexion = $this->getConexion();

        // obtiene todas las combinaciones
        $sQuery = "SELECT combinacion_forma_pago.id, " .
                "combinacion_forma_pago.forma_pago_id AS forma_pago_id, " .
                "forma_pago1.descripcion AS forma_pago_descripcion, " .
                "combinacion_forma_pago.forma_pago_id2 AS forma_pago_id2, " .
                "forma_pago2.descripcion AS forma_pago_descripcion2 " .
            "FROM combinacion_forma_pago " .
            "INNER JOIN forma_pago AS forma_pago1 ON combinacion_forma_pago.forma_pago_id = forma_pago1.id " .
            "INNER JOIN forma_pago AS forma_pago2 ON combinacion_forma_pago.forma_pago_id2 = forma_pago2.id " .
            "ORDER BY forma_pago_id, forma_pago_id2";
        $aResultado = $oConexion->query($sQuery);
        $aCombinaciones = $this->parsearQueryResult($aResultado);


        return $this->asJson(array(
            "success" => true,
            "message" => "Combinaciones de forma de pago",
            "records" => $aCombinaciones,
            "metadata" => array(
                "total_registros" => count($aCombinaciones)
            )
        ));
    }

    /**
     * Elimina las combinaciones de las formas de pago
     * @return JsonResponse
     */
    public function destroyCombinacionesFormaPago() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // elimina los registros de las combinaciones
        $sQuery = "DELETE FROM combinacion_forma_pago " .
            "WHERE forma_pago_id = ? " .
            "AND forma_pago_id2 = ?";
        foreach ($aRecords as $aRecord) {
            $aQueryParams = array(
                $aRecord["forma_pago_id"],
                $aRecord["forma_pago_id2"]
            );
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Combinaciones de forma de pago eliminadas",
        ));
    }

    /**
     * Crea las combinaciones de las formas de pago - plaza
     * @return JsonResponse
     */
    public function createCombinacionesFormaPlaza() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // elimina los registros de las combinaciones
        $sQuery = "INSERT INTO combinacion_forma_plaza (" .
                "forma_pago_id, " .
                "plaza_id " .
            ") VALUES (" .
                "?, ?" .
            ")";
        foreach ($aRecords as &$aRecord) {
            $aRecord["clientId"] = $aRecord["id"];
            $aQueryParams = array(
                $aRecord["forma_pago_id"],
                $aRecord["plaza_id"]
            );
            $oConexion->query($sQuery, $aQueryParams);
            $aRecord["id"] = $oConexion->lastInsertId();
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
            "message" => "Combinaciones de forma de pago - plaza agregadas",
            "records" => $aRecords
        ));
    }

    /**
     * Lee las combinaciones de las formas de pago - plaza
     * @return JsonResponse
     */
    public function readCombinacionesFormaPlaza() {
        $oConexion = $this->getConexion();

        // obtiene todas las combinaciones
        $sQuery = "SELECT combinacion_forma_plaza.id AS id, " .
                "forma_pago.id AS forma_pago_id, " .
                "forma_pago.descripcion AS forma_pago, " .
                "plaza.id AS plaza_id, " .
                "plaza.ciudad AS plaza " .
            "FROM combinacion_forma_plaza " .
            "INNER JOIN forma_pago ON combinacion_forma_plaza.forma_pago_id = forma_pago.id " .
            "INNER JOIN plaza ON combinacion_forma_plaza.plaza_id = plaza.id " .
            "ORDER BY forma_pago_id, plaza_id";
        $aResultado = $oConexion->query($sQuery);
        $aCombinaciones = $this->parsearQueryResult($aResultado);

        return $this->asJson(array(
            "success" => true,
            "message" => "Combinaciones de forma de pago - plaza",
            "records" => $aCombinaciones,
            "metadata" => array(
                "total_registros" => count($aCombinaciones)
            )
        ));
    }

    /**
     * Elimina las combinaciones de las formas de pago - plaza
     * @return JsonResponse
     */
    public function destroyCombinacionesFormaPlaza() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // elimina los registros de las combinaciones
        $sQuery = "DELETE FROM combinacion_forma_plaza " .
            "WHERE forma_pago_id = ? " .
            "AND plaza_id = ?";
        foreach ($aRecords as $aRecord) {
            $aQueryParams = array(
                $aRecord["forma_pago_id"],
                $aRecord["plaza_id"]
            );
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Combinaciones de forma de pago - plaza eliminadas",
        ));
    }
}
