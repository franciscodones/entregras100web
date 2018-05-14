<?php

namespace App\Controller;

use Exception;

class UnidadesController extends AppController {

    /**
     * Lee el catalogo de unidades
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();

        // obtiene todas las unidades
        $sQuery = "SELECT unidad.*, " .
                "zona.zona, " .
                "plaza.id AS plaza_id, " .
                "plaza.ciudad AS nombre_plaza, " .
                "folios.id AS folios_id, " .
                "folios.serie AS folios_serie, " .
                "folios.nota AS folios_nota, " .
                "folios.puntos AS folios_puntos, " .
                "folios.litrogas AS folios_litrogas, " .
                "folios.recirculacion AS folios_recirculacion, " .
                "folios.consignacion AS folios_consignacion, " .
                "folios.donativo AS folios_donativo, " .
                "folios.cortesia AS folios_cortesia " .
            "FROM unidad " .
            "LEFT JOIN zona ON unidad.zona_id = zona.id " .
            "LEFT JOIN plaza ON zona.plaza_id = plaza.id " .
            "INNER JOIN folios ON unidad.id = folios.unidad_id";
        $aUnidades = $oConexion->query($sQuery);

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de unidades",
            "records" => $aUnidades,
            "metadata" => array(
                "total_registros" => count($aUnidades)
            )
        ));
    }

    /**
     * Crea una unidad
     * @return JsonResponse
     */
    public function create() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        // agrega el registro de la unidad
        foreach ($aRecords as &$aRecord) {
            $aRecord["clientId"] = $aRecord["id"];
            $sQuery = "INSERT INTO unidad (" .
                    "zona_id, " .
                    "unidad, " .
                    "letra, " .
                    "tipo, " .
                    "fecha, " .
                    "latitud, " .
                    "longitud, " .
                    "rssi, " .
                    "online, " .
                    "cobro_aditivo, " .
                    "aditivo_obligatorio, " .
                    "autorizacion, " .
                    "tiempo, " .
                    "sincronizacion, " .
                    "fecha_operacion, " .
                    "version, " .
                    "ruta_actualizacion, " .
                    "fecha_registro, " .
                    "fecha_modificacion, " .
                    "estado " .
                ") VALUES (" .
                    "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?" .
                ")";
            $aQueryParams = array(
                $aRecord["zona_id"],
                $aRecord["unidad"],
                $aRecord["letra"],
                "E",
                "0000-00-00 00:00:00",
                0,
                0,
                0,
                $aRecord["online"],
                $aRecord["cobro_aditivo"],
                false,
                "",
                0,
                0,
                "0000-00-00 00:00:00",
                "2.4",
                "http://gps.gaspasa.com.mx:8080/Entregas100/APK/v2.4/v2.4_gps.apk",
                date("Y-m-d H:i:s"),
                date("Y-m-d H:i:s"),
                ""
            );
            $aResultado = $oConexion->query($sQuery, $aQueryParams);
            $aRecord["id"] = $oConexion->driver()->lastInsertId();

            // inserta el registro de los folios
            $sQuery = "INSERT INTO folios (" .
                    "unidad_id, " .
                    "serie, " .
                    "nota, " .
                    "puntos, " .
                    "litrogas, " .
                    "recirculacion, " .
                    "consignacion, " .
                    "donativo, " .
                    "cortesia, " .
                    "revision " .
                ") VALUES (" .
                    "?, ?, ?, ?, ?, ?, ?, ?, ?, ?" .
                ")";
            $aQueryParams = array(
                $aRecord["id"],
                $aRecord["folios_serie"],
                $aRecord["folios_nota"],
                $aRecord["folios_puntos"],
                $aRecord["folios_litrogas"],
                $aRecord["folios_recirculacion"],
                $aRecord["folios_consignacion"],
                $aRecord["folios_donativo"],
                $aRecord["folios_cortesia"],
                0,
            );
            $aResultado = $oConexion->query($sQuery, $aQueryParams);
            $aRecord["folios_id"] = $oConexion->driver()->lastInsertId();
        }
        unset($aRecord);

        // procesa los records para regresarlos y que los campos se actualicen en el store
        $aRecords = array_map(function($aRecord) {
            return array(
                "id" => $aRecord["id"],
                "folios_id" => $aRecord["folios_id"],
                "clientId" => $aRecord["clientId"]
            );
        }, $aRecords);

        return $this->asJson(array(
            "success" => true,
            "message" => "Unidades agregadas",
            "records" => $aRecords
        ));
    }

    /**
     * Actualiza unidades
     * @return JsonResponse
     */
    public function update() {
        $oConexion = $this->getConexion();

        $aDatos = $this->request->data;
        $aRecords = json_decode($aDatos["records"], true);

        foreach ($aRecords as $aRecord) {
            // actualiza el registro de la unidad
            $sQuery = "UPDATE unidad SET " .
                    "unidad = ?, " .
                    "zona_id = ?, " .
                    "letra = ?, " .
                    "online = ?, " .
                    "cobro_aditivo = ? " .
                "WHERE id = ?";
            $aQueryParams = array(
                $aRecord["unidad"],
                $aRecord["zona_id"],
                $aRecord["letra"],
                $aRecord["online"],
                $aRecord["cobro_aditivo"],
                $aRecord["id"]
            );
            $oConexion->query($sQuery, $aQueryParams);

            // actualiza el registro de los folios
            $sQuery = "UPDATE folios SET " .
                    "serie = ?, " .
                    "nota = ?, " .
                    "puntos = ?, " .
                    "litrogas = ?, " .
                    "recirculacion = ?, " .
                    "consignacion = ?, " .
                    "donativo = ?, " .
                    "cortesia = ? " .
                "WHERE unidad_id = ?";
            $aQueryParams = array(
                $aRecord["folios_serie"],
                $aRecord["folios_nota"],
                $aRecord["folios_puntos"],
                $aRecord["folios_litrogas"],
                $aRecord["folios_recirculacion"],
                $aRecord["folios_consignacion"],
                $aRecord["folios_donativo"],
                $aRecord["folios_cortesia"],
                $aRecord["id"]
            );
            $oConexion->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Unidades actualizadas"
        ));
    }
}
