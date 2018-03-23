<?php

class ZonasController extends AppController {

    /**
     * Lee el catalogo de las zonas
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();

        // obtiene todas las zonas
        $sQuery = "SELECT zona.id AS id, " .
                "zona.zona, " .
                "zona.descripcion, " .
                "zona.ayudante, " .
                "zona.plaza_id, " .
                "plaza.ciudad AS nombre_plaza " .
            "FROM zona " .
            "INNER JOIN plaza ON zona.plaza_id = plaza.id " .
            "ORDER BY plaza.id, zona.zona";
        $aResultado = $oConexion->query($sQuery);
        $aZonas = $this->parsearQueryResult($aResultado);

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de zonas",
            "data" => $aZonas,
            "metadata" => array(
                "total_registros" => count($aZonas)
            )
        ));
    }

    /**
     * Crea una zona
     * @return JsonResponse
     */
    public function create() {
        $oConexion = $this->getConexion();

        $this->request->data["datos"] = json_decode($this->request->data["datos"], true);
        $aDatos = $this->request->data["datos"][0];
        $nZona = $aDatos["zona"];
        $nPlazaId = $aDatos["plaza_id"];
        $sDescripcion = $aDatos["descripcion"];
        $bRequiereAyudante = $aDatos["ayudante"];
        $sEstatus = "";
        $sFechaRegistro = date("Y-m-d H:i:s");
        $sFechaModificacion = date("Y-m-d H:i:s");


        // agrega el registro de la zona
        $sQuery = "INSERT INTO zona (" .
                "plaza_id, " .
                "zona, " .
                "descripcion, " .
                "ayudante, " .
                "estatus, " .
                "fecha_registro, " .
                "fecha_modificacion " .
            ") VALUES (" .
                "?, ?, ?, ?, ?, ?, ?" .
            ")";
        $aQueryParams = array(
            $nPlazaId,
            $nZona,
            $sDescripcion,
            $bRequiereAyudante,
            $sEstatus,
            $sFechaRegistro,
            $sFechaModificacion
        );
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $nZonaId = $oConexion->lastInsertId();

        return $this->asJson(array(
            "success" => true,
            "message" => "Zona agregada",
            "data" => array(
                "id" => $nZonaId
            )
        ));
    }

    /**
     * Actualiza una zona
     * @return JsonResponse
     */
    public function update() {
        $oConexion = $this->getConexion();

        $this->request->data["datos"] = json_decode($this->request->data["datos"], true);
        $aDatos = $this->request->data["datos"][0];
        $nId = $aDatos["id"];
        $nZona = $aDatos["zona"];
        $nPlazaId = $aDatos["plaza_id"];
        $sDescripcion = $aDatos["descripcion"];
        $bRequiereAyudante = $aDatos["ayudante"];
        $sFechaModificacion = date("Y-m-d H:i:s");

        // actualiza el registro de la zona
        $sQuery = "UPDATE zona SET " .
                "plaza_id = ?, " .
                "zona = ?, " .
                "descripcion = ?, " .
                "ayudante = ?, " .
                "fecha_modificacion = ? " .
            "WHERE id = ?";
        $aQueryParams = array(
            $nPlazaId,
            $nZona,
            $sDescripcion,
            $bRequiereAyudante,
            $sFechaModificacion,
            $nId
        );
        $oConexion->query($sQuery, $aQueryParams);

        return $this->asJson(array(
            "success" => true,
            "message" => "Zona actualizada"
        ));
    }
}
