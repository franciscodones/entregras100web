<?php

namespace App\Controller;

use Exception;

class DescuentosPromocionController extends AppController {

    /**
     * Lee el catalogo de descuentos
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();
        $aDatos = $this->request->query;
        $nPlazaId = $aDatos["plaza_id"];

        // obtiene la conexion a la bd de la plaza
        $sQuery = "SELECT plaza.*, " .
            "conexion.ip_te, " .
            "conexion.usuario_te, " .
            "conexion.password_te, " .
            "conexion.base_te " .
            "FROM plaza " .
            "INNER JOIN conexion ON plaza.id = conexion.plaza_id " .
            "WHERE plaza.id = ?";
        $aQueryParams = array($nPlazaId);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        // si no hay info de la plaza se termina el proceso
        if (count($aResultado) <= 0) {
            throw new Exception("Error al obtener los datos de conexion a la plaza");
        }
        $aPlaza = $aResultado[0];
        $oConexionPlaza = $this->getConexion(
            $aPlaza["plaza"],
            array(
                "host" => $aPlaza["ip_te"],
                "user" => $aPlaza["usuario_te"],
                "password" => $aPlaza["password_te"],
                "database" => $aPlaza["base_te"]
            )
        );

        // obtiene los registros de los descuentos
        $sQuery = "SELECT * FROM descuentos_promocion ORDER BY descuento";
        $aDescuentos = $oConexionPlaza->query($sQuery);

        //procesa las descuentos
        $aDescuentosProcesados = array();
        foreach ($aDescuentos as $aDescuento) {
            $aDescuentosProcesados[] = array(
                "id" => $aDescuento["id"],
                "hora_inicio" => $aDescuento["hora_inicio"],
                "hora_fin" => $aDescuento["hora_fin"],
                "litros_min" => $aDescuento["litros_min"],
                "litros_max" => $aDescuento["litros_max"],
                "descuento" => $aDescuento["descuento"]
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de descuentos",
            "records" => $aDescuentosProcesados,
            "metadata" => array(
                "total_registros" => count($aDescuentosProcesados)
            )
        ));
    }

    /**
     * Crea descuentos promocion
     * @return JsonResponse
     */
    public function create() {
        $oConexion = $this->getConexion();
        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);
        $nPlazaId = $aDatos["plaza_id"];

        // obtiene la conexion a la bd de la plaza
        $sQuery = "SELECT plaza.*, " .
            "conexion.ip_te, " .
            "conexion.usuario_te, " .
            "conexion.password_te, " .
            "conexion.base_te " .
            "FROM plaza " .
            "INNER JOIN conexion ON plaza.id = conexion.plaza_id " .
            "WHERE plaza.id = ?";
        $aQueryParams = array($nPlazaId);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        // si no hay info de la plaza se termina el proceso
        if (count($aResultado) <= 0) {
            throw new Exception("Error al obtener los datos de conexion a la plaza");
        }
        $aPlaza = $aResultado[0];
        $oConexionPlaza = $this->getConexion(
            $aPlaza["plaza"],
            array(
                "host" => $aPlaza["ip_te"],
                "user" => $aPlaza["usuario_te"],
                "password" => $aPlaza["password_te"],
                "database" => $aPlaza["base_te"]
            )
        );

        // agrega el registro del horario
        $sQuery = "INSERT INTO descuentos_promocion (" .
                "hora_inicio, " .
                "hora_fin, " .
                "litros_min, " .
                "litros_max, " .
                "descuento " .
            ") VALUES (" .
                "?, ?, ?, ?, ?" .
            ")";
        foreach ($aRecords as &$aRecord) {
            $aRecord["clientId"] = $aRecord["id"];
            $aQueryParams = array(
                $aRecord["hora_inicio"],
                $aRecord["hora_fin"],
                $aRecord["litros_min"],
                $aRecord["litros_max"],
                $aRecord["descuento"]
            );
            $oConexionPlaza->query($sQuery, $aQueryParams);
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
            "message" => "Descuentos agregados",
            "records" => $aRecords
        ));
    }

    /**
     * Actualiza los descuentos promocion
     * @return JsonResponse
     */
    public function update() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);
        $nPlazaId = $aDatos["plaza_id"];

        // obtiene la conexion a la bd de la plaza
        $sQuery = "SELECT plaza.*, " .
            "conexion.ip_te, " .
            "conexion.usuario_te, " .
            "conexion.password_te, " .
            "conexion.base_te " .
            "FROM plaza " .
            "INNER JOIN conexion ON plaza.id = conexion.plaza_id " .
            "WHERE plaza.id = ?";
        $aQueryParams = array($nPlazaId);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        // si no hay info de la plaza se termina el proceso
        if (count($aResultado) <= 0) {
            throw new Exception("Error al obtener los datos de conexion a la plaza");
        }
        $aPlaza = $aResultado[0];
        $oConexionPlaza = $this->getConexion(
            $aPlaza["plaza"],
            array(
                "host" => $aPlaza["ip_te"],
                "user" => $aPlaza["usuario_te"],
                "password" => $aPlaza["password_te"],
                "database" => $aPlaza["base_te"]
            )
        );

        // actualiza el registro del descuento
        $sQuery = "UPDATE descuentos_promocion SET " .
                "hora_inicio = ?, " .
                "hora_fin = ?, " .
                "litros_min = ?, " .
                "litros_max = ?, " .
                "descuento = ? " .
            "WHERE id = ?";
        foreach ($aRecords as $aRecord) {
            $aQueryParams = array(
                $aRecord["hora_inicio"],
                $aRecord["hora_fin"],
                $aRecord["litros_min"],
                $aRecord["litros_max"],
                $aRecord["descuento"],
                $aRecord["id"],
            );
            $oConexionPlaza->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Descuentos actualizados"
        ));
    }

    /**
     * Elimina descuentos promocion
     * @return JsonResponse
     */
    public function destroy() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);
        $nPlazaId = $aDatos["plaza_id"];

        // obtiene la conexion a la bd de la plaza
        $sQuery = "SELECT plaza.*, " .
            "conexion.ip_te, " .
            "conexion.usuario_te, " .
            "conexion.password_te, " .
            "conexion.base_te " .
            "FROM plaza " .
            "INNER JOIN conexion ON plaza.id = conexion.plaza_id " .
            "WHERE plaza.id = ?";
        $aQueryParams = array($nPlazaId);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        // si no hay info de la plaza se termina el proceso
        if (count($aResultado) <= 0) {
            throw new Exception("Error al obtener los datos de conexion a la plaza");
        }
        $aPlaza = $aResultado[0];
        $oConexionPlaza = $this->getConexion(
            $aPlaza["plaza"],
            array(
                "host" => $aPlaza["ip_te"],
                "user" => $aPlaza["usuario_te"],
                "password" => $aPlaza["password_te"],
                "database" => $aPlaza["base_te"]
            )
        );

        // elimina los registros
        $sQuery = "DELETE FROM descuentos_promocion " .
            "WHERE id = ?";
        foreach ($aRecords as $aRecord) {
            $aQueryParams = array($aRecord["id"]);
            $oConexionPlaza->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Descuentos eliminados"
        ));
    }
}
