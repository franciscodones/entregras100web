<?php

namespace App\Controller\Entregas100;

use Exception;
use DateTime;

class IniciofinrutaAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Inicia la sesion del chofer en la tablet y devuelve la informacion de este
     * ademas de la fecha de operacion de la unidad
     *
     * @return JsonResponse
     */
    public function iniciofinruta_fn() {
        $nParametrosFn = 10;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // si el parametro de folios no existe
        $aFolios = array();
        if (!empty($aDatos["folios"])) {
            $aFolios = json_decode($aDatos["folios"], true);
        }

        $nNip1 = $aDatos["nip1"];
        $nNip2 = $aDatos["nip2"];
        $nPorcentajeTanque = $aDatos["porcentaje_tanque"];
        $nPorcentajeTanqueCarburacion = $aDatos["porcentaje_tanque_carburacion"];
        $nOdometro = $aDatos["odometro"];
        $nHorometro = $aDatos["horometro"];
        $nTotalizador = $aDatos["totalizador"];
        $nCargaBateria = $aDatos["carga_bateria"];
        $nTipo = trim($aDatos["tipo"]);
        $dFechaOperacion = DateTime::createFromFormat("Y-m-d H:i:s", $aUnidad["fecha_operacion"] . " 00:00:00");
        $dHoy = new DateTime("today");
        $oConexion = $this->getConexion();

        // obtiene el registro del chofer
        $sQuery = "SELECT * FROM operador " .
            "WHERE nip = ? " .
            "AND tipo_usuario_id = 1 ";
        $aQueryParams = array($nNip1);
        $aUsuario1 = $oConexion->query($sQuery, $aQueryParams);
        $aUsuario1 = count($aUsuario1) > 0 ? $aUsuario1[0] : null;

        // si no existe el usuario1 se marca error
        if ($aUsuario1 == null) {
            throw new Exception("Error de nip de chofer, no exite este nip");
        }

        $bRequiereAyudante = $aUsuario1["tipo_usuario_id"] == 1 && (bool)$aUnidad["ayudante"];
        // si la zona requiere ayudante y el usuario1 es chofer valida el nip2
        if ($bRequiereAyudante) {
            // obtiene el registro del ayudante
            $sQuery = "SELECT * FROM operador " .
                "WHERE nip = ? " .
                "AND tipo_usuario_id = 1 ";
            $aQueryParams = array($nNip2);
            $aUsuario2 = $oConexion->query($sQuery, $aQueryParams);
            $aUsuario2 = count($aUsuario2) > 0 ? $aUsuario2[0] : null;

            // si no existe el usuario2 se marca error
            if ($aUsuario2 == null) {
                throw new Exception("Error de nip de ayudante, no exite este nip");
            }
        } else {
            $aUsuario2 = null;
        }

        // inserta el registor de ruta
        $sQuery = "INSERT INTO ruta (" .
            "fecha, ".
            "hora, ".
            "unidad_id, ".
            "nip1, ".
            "nip2, ".
            "porcentaje_tanque, ".
            "porcentaje_tanque_carburacion, ".
            "odometro, ".
            "horometro, ".
            "totalizador, ".
            "carga_bateria, ".
            "zona, ".
            "tipo) ".
            "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)".
            "ON DUPLICATE KEY UPDATE ".
                "fecha = ?, ".
                "hora = ?, ".
                "unidad_id = ?, ".
                "nip1 = ?, ".
                "nip2 = ?, ".
                "porcentaje_tanque = ?, ".
                "porcentaje_tanque_carburacion = ?, ".
                "odometro = ?, ".
                "horometro = ?, ".
                "totalizador = ?, ".
                "carga_bateria = ?, ".
                "zona = ?, ".
                "tipo = ?";
        $aQueryParams = array(
            $dFechaOperacion->format("Y-m-d"),
            date('H:s:i'),
            $aUnidad["id"],
            $aUsuario1["nip"],
            (!empty($aUsuario2) ? $aUsuario2["nip"] : 0),
            $nPorcentajeTanque,
            $nPorcentajeTanqueCarburacion,
            $nOdometro,
            $nHorometro,
            $nTotalizador,
            $nCargaBateria,
            $aUnidad['zona'],
            $nTipo,
            $dFechaOperacion->format("Y-m-d"),
            date('H:s:i'),
            $aUnidad["id"],
            $aUsuario1["nip"],
            (!empty($aUsuario2) ? $aUsuario2["nip"] : 0),
            $nPorcentajeTanque,
            $nPorcentajeTanqueCarburacion,
            $nOdometro,
            $nHorometro,
            $nTotalizador,
            $nCargaBateria,
            $aUnidad['zona'],
            $nTipo
        );
        $aResultado = $oConexion->query($sQuery, $aQueryParams);

        // respalda el registro de la ruta recien insertado
        $sQuery = "SELECT id " .
            "FROM ruta " .
            "WHERE unidad_id = ? " .
            "AND tipo = ?";
        $aQueryParams = array(
            $aUnidad["id"],
            $nTipo
        );
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $aUltimaRuta = count($aResultado) > 0 ? $aResultado[0] : null;

        // si existe una ruta se respalda
        if ($aUltimaRuta != null) {
            $sQuery = "CALL respalda(?, ?)";
            $aQueryParams = array(
                "ruta",
                $aUltimaRuta["id"]
            );
            $oConexion->query($sQuery, $aQueryParams);
        }

        // si es un fin de ruta (tipo 2)
        if ($nTipo == 2) {
            // elimina la sesion del nip1
            $sQuery = "UPDATE operador " .
                "SET sesion = ?, " .
                    "unidad_id = ? " .
                "WHERE nip = ?";
            $aQueryParams = array(
                0,
                0,
                $nNip1
            );
            $oConexion->query($sQuery, $aQueryParams);

            // elimina la sesion del nip2 si existe
            if (!empty($nNip2)) {
                $sQuery = "UPDATE operador " .
                    "SET sesion = ?, " .
                        "unidad_id = ? " .
                    "WHERE nip = ?";
                $aQueryParams = array(
                    0,
                    0,
                    $nNip2
                );
                $oConexion->query($sQuery, $aQueryParams);
            }

            // actualiza los folios
            foreach ($aFolios as $aFolio) {
                $sQuery = "UPDATE folios " .
                    "SET " . $aFolio["tipo"] . " = ? " .
                    "WHERE unidad_id = ?";
                $aQueryParams = array($aFolio["folio"], $aUnidad["id"]);
                $oConexion->query($sQuery, $aQueryParams);
            }
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Inicio/Fin de ruta correcto",
            "data" => null
        ));
    }
}
