<?php

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
        $aResultado = $oConexion->query($sQuery);
        $aOperadores = $this->parsearQueryResult($aResultado);

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de operadores",
            "data" => $aOperadores,
            "metadata" => array(
                "total_registros" => count($aOperadores)
            )
        ));
    }

    /**
     * Crea un operador
     * @return JsonResponse
     */
    public function create() {
        $oConexion = $this->getConexion();

        $this->request->data["datos"] = json_decode($this->request->data["datos"], true);
        $aDatos = $this->request->data["datos"][0];
        $sNombre = $aDatos["nombre"];
        $nNip = 0;
        $nTipoUsuarioId = $aDatos["tipo_usuario_id"];
        $nPlazaId = $aDatos["plaza_id"];
        $bEstatus = true;
        $bSesion = false;
        $nUnidadId = 0;
        $bPlazas = false;
        $sFechaSesion = "0000-00-00 00:00:00";
        $sFechaRegistro = date("Y-m-d H:i:s");

        // genera un nip aleatorio que no este repetido
        // el ciclo continuara hasta que el nip no este repetido
        $x = 10;
        do {
            $nNip = rand(100000, 999999);
            $sQuery = "SELECT nip FROM operador WHERE nip = ?";
            $aQueryParams = array($nNip);
            $aResultado = $oConexion->query($sQuery, $aQueryParams);
            $aResultado = $this->parsearQueryResult($aResultado);
            if (count($aResultado) > 0) {
                $nNip = 0;
            }
            $x--;
        } while ($nNip <= 0 || $x > 0);

        // agrega el registro del operador
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
            $sNombre,
            $nNip,
            $nTipoUsuarioId,
            $nPlazaId,
            $bEstatus,
            $bSesion,
            $nUnidadId,
            $bPlazas,
            $sFechaSesion,
            $sFechaRegistro
        );
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $nOperadorId = $oConexion->lastInsertId();

        return $this->asJson(array(
            "success" => true,
            "message" => "Operador agregado",
            "data" => array(
                "id" => $nOperadorId,
                "nip" => $nNip
            )
        ));
    }

    /**
     * Actualiza un operador
     * @return JsonResponse
     */
    public function update() {
        $oConexion = $this->getConexion();

        $this->request->data["datos"] = json_decode($this->request->data["datos"], true);
        $aDatos = $this->request->data["datos"][0];
        $nId = $aDatos["id"];
        $sNombre = $aDatos["nombre"];
        $nTipoUsuarioId = $aDatos["tipo_usuario_id"];
        $nPlazaId = $aDatos["plaza_id"];
        $bSesion = $aDatos["sesion"];

        // actualiza el registro del operador
        $sQuery = "UPDATE operador SET " .
                "nombre = ?, " .
                "tipo_usuario_id = ?, " .
                "plaza_id = ?, " .
                "sesion = ? " .
            "WHERE id = ?";
        $aQueryParams = array(
            $sNombre,
            $nTipoUsuarioId,
            $nPlazaId,
            $bSesion,
            $nId
        );
        $oConexion->query($sQuery, $aQueryParams);

        return $this->asJson(array(
            "success" => true,
            "message" => "Operador actualizado"
        ));
    }
}
