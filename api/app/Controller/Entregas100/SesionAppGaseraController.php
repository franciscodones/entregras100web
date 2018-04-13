<?php

class SesionAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Inicia la sesion del chofer en la tablet y devuelve la informacion de este
     * ademas de la fecha de operacion de la unidad
     *
     * @return JsonResponse
     */
    public function sesion_fn() {
        $nParametrosFn = 3;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        $nNip1 = $aDatos["nip"];
        $nNip2 = $aDatos["nip2"];
        $oConexion = $this->conexion();
        $dFechaOperacion = DateTime::createFromFormat("Y-m-d H:i:s", $aUnidad["fecha_operacion"] . " 00:00:00");
        $dHoy = new DateTime("today");

        // si ambos nip son iguales se marca error
        if ($nNip1 == $nNip2) {
            throw new Exception("Error de nip de ayudante, no puede ser igual al del chofer");
        }

        // obtiene el registro del chofer|supervisor|jefe operativo|tecnico
        $sQuery = "SELECT * " .
            "FROM operador " .
            "WHERE nip = ? " .
            "AND tipo_usuario_id IN (1, 2, 3, 4)";
        $aQueryParams = array($nNip1);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $aUsuario1 = $this->parsearQueryResult($aResultado);
        $aUsuario1 = count($aUsuario1) > 0 ? $aUsuario1[0] : null;

        // si no existe el usuario1 se marca error
        if ($aUsuario1 == null) {
            throw new Exception("Error de nip de chofer, no exite este nip");
        } else if ($aUsuario1["tipo_usuario_id"] == 1 && !$aUsuario1["plazas"] && $aUsuario1["sesion"]) {
            throw new Exception("Error de nip de chofer, su sesion ya fue iniciada");
        }

        $bRequiereAyudante = $aUsuario1["tipo_usuario_id"] == 1 && (bool)$aUnidad["ayudante"];
        // si la zona requiere ayudante y el usuario1 es chofer valida el nip2
        if ($bRequiereAyudante) {
            // obtiene el registro del ayudante
            $sQuery = "SELECT * " .
                "FROM operador " .
                "WHERE nip = ? " .
                "AND tipo_usuario_id = 1";
            $aQueryParams = array($nNip2);
            $aResultado = $oConexion->query($sQuery, $aQueryParams);
            $aUsuario2 = $this->parsearQueryResult($aResultado);
            $aUsuario2 = count($aUsuario2) > 0 ? $aUsuario2[0] : null;

            // si no existe el usuario2 se marca error
            if ($aUsuario2 == null) {
                throw new Exception("Error de nip de ayudante, no exite este nip");
            } else if ($aUsuario1["tipo_usuario_id"] && !$aUsuario2["plazas"] && $aUsuario2["sesion"]) {
                throw new Exception("Error de nip de ayudante, su sesion ya fue iniciada");
            }
        }

        // se marcan como iniciadas las sesiones de los nip
        $sQuery = "UPDATE operador SET " .
                "sesion = ?, " .
                "unidad_id = ?, " .
                "fecha_sesion = ? " .
            "WHERE id IN (" . ($bRequiereAyudante ? "?, ?" : "?") . ")";
        $aQueryParams = array(1, $aUnidad["id"], date("Y-m-d H:i:s"), $aUsuario1["id"]);
        if ($bRequiereAyudante) {
            $aQueryParams[] = $aUsuario2["id"];
        }
        $oConexion->query($sQuery, $aQueryParams);

        return $this->asJson(array(
            "success" => true,
            "message" => "Sesion iniciada correctamente",
            "data" => array(
                "operador1" => trim($aUsuario1["nombre"]),
                "operador2" => $bRequiereAyudante ?
                    trim($aUsuario2["nombre"]) :
                    "",
                "tipo" => $aUsuario1["tipo_usuario_id"],
                "version" => $aUnidad["version"],
                "ruta_actualizacion" => $aUnidad["ruta_actualizacion"],
                "fecha" => $dFechaOperacion->format("Y-m-d")
            )
        ));
    }
}
