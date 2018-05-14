<?php

namespace App\Controller;

use Exception;

class OperadoresController extends AppController {

    /**
     * Lee el catalogo de operadores
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();

        // obtiene todos los operadores
        $sQuery = "SELECT operador.*, " .
                "tipo_usuario.descripcion AS tipo_usuario, " .
                "plaza.ciudad AS nombre_plaza " .
            "FROM operador " .
            "INNER JOIN tipo_usuario ON operador.tipo_usuario_id = tipo_usuario.id " .
            "INNER JOIN plaza ON operador.plaza_id = plaza.id";
        $aOperadores = $oConexion->query($sQuery);

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de operadores",
            "records" => $aOperadores,
            "metadata" => array(
                "total_registros" => count($aOperadores)
            )
        ));
    }

    /**
     * Crea operadores
     * @return JsonResponse
     */
    public function create() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // agrega el registro del operador
        foreach ($aRecords as &$aRecord) {
            $aRecord["clientId"] = $aRecord["id"];

            // genera un nip aleatorio que no este repetido
            // el ciclo continuara hasta que el nip no este repetido
            $x = 10;
            do {
                $nNip = rand(100000, 999999);
                $sQuery = "SELECT nip FROM operador WHERE nip = ?";
                $aQueryParams = array($nNip);
                $aResultado = $oConexion->query($sQuery, $aQueryParams);
                if (count($aResultado) > 0) {
                    $nNip = 0;
                }
                $x--;
            } while ($nNip <= 0 || $x > 0);
            $aRecord["nip"] = $nNip;

            $sQuery = "INSERT INTO operador (" .
                "nombre, " .
                "nip, " .
                "tipo_usuario_id, " .
                "plaza_id, " .
                "estatus, " .
                "sesion, " .
                "unidad_id, " .
                "plazas, " .
                "fecha_sesion, " .
                "fecha_registro " .
            ") VALUES (" .
                "?, ?, ?, ?, ?, ?, ?, ?, ?, ?" .
            ")";
            $aQueryParams = array(
                $aRecord["nombre"],
                $aRecord["nip"],
                $aRecord["tipo_usuario_id"],
                $aRecord["plaza_id"],
                true,
                false,
                0,
                false,
                "0000-00-00 00:00:00",
                date("Y-m-d H:i:s")
            );
            $aResultado = $oConexion->query($sQuery, $aQueryParams);
            $aRecord["id"] = $oConexion->driver()->lastInsertId();
        }
        unset($aRecord);

        // procesa los records para regresarlos y que los campos se actualicen en el store
        $aRecords = array_map(function($aRecord) {
            return array(
                "id" => $aRecord["id"],
                "clientId" => $aRecord["clientId"],
                "nip" => $aRecord["nip"]
            );
        }, $aRecords);

        return $this->asJson(array(
            "success" => true,
            "message" => "Operador agregado",
            "records" => $aRecords
        ));
    }

    /**
     * Actualiza operadores
     * @return JsonResponse
     */
    public function update() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aDatos["records"] = json_decode($aDatos["records"], true);

        // actualiza el registro del operador
        $sQuery = "UPDATE operador SET " .
                "nombre = ?, " .
                "tipo_usuario_id = ?, " .
                "plaza_id = ?, " .
                "sesion = ? " .
            "WHERE id = ?";
        foreach ($aDatos["records"] as $record) {
            $aQueryParams = array(
                $record["nombre"],
                $record["tipo_usuario_id"],
                $record["plaza_id"],
                $record["sesion"],
                $record["id"]
            );
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Operador actualizado"
        ));
    }
}
