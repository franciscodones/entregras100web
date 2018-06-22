<?php

namespace App\Controller\Entregas100;

use Exception;

class NosurtidoAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Registra un servicio no surtido
     *
     * @return JsonResponse
     */
    public function nosurtido_fn() {
        $nParametrosFn = 10;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene la conexion a la bd de la plaza
        $oConexion = $this->getConexion();
        $sQuery = "SELECT plaza.*, " .
                "conexion.ip_te, " .
                "conexion.usuario_te, " .
                "conexion.password_te, " .
                "conexion.base_te " .
            "FROM plaza " .
            "INNER JOIN conexion ON plaza.id = conexion.plaza_id " .
            "WHERE plaza.id = ?";
        $aQueryParams = array($aUnidad['plaza_id']);
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

        // informacion del servicio
        $nNumeroControl = $aDatos['numero_control'];
        $dFechaSurtido = $aDatos['fecha_operacion'];
        $nMotivoId = $aDatos['motivo_id'];
        $tHoraSurtido = $aDatos['horasurtido'];
        $dFechaCompromiso = $aDatos['fecha_compromiso'];
        $nCapacidadTanque = $aDatos['capacidad'];
        $nPorcetanjeTanque = $aDatos['porcentaje_tanque'];
        $nTipoCompromisoId = $aDatos['tipo_compromiso_id'];
        $nNipChofer = $aDatos["nip_chofer"];
        $nNipAyudante = !empty($aDatos["nip_ayudante"]) ? $aDatos["nip_ayudante"] : null;

        // actualiza en la lista el estado del servicio si no es fuera de turno
        if ($nMotivoId != 5) {
            $sQuery = "UPDATE listas " .
                "SET unidad = ?, " .
                    "idmotivo = ?, " .
                    "surtido = ?, " .
                    "estado = ?, " .
                    "horasurtido = ? " .
                "WHERE ncontrol = ? " .
                "AND fecha = CURDATE()";
            $aQueryParams = array(
                $aUnidad["unidad"],
                $nMotivoId,
                "N",
                "V",
                $dFechaSurtido . " " . $tHoraSurtido,
                $nNumeroControl
            );
            $oConexionPlaza->query($sQuery, $aQueryParams);
        }

        // agrega el servicio a la tabla servicio_atendido
        $sQuery = "INSERT INTO servicio_atendido (" .
                "unidad_id, " .
                "motivo_id, " .
                "fecha, " .
                "hora, " .
                "numero_control, " .
                "fecha_compromiso, " .
                "capacidad, " .
                "porcentaje_inicial, " .
                "porcentaje_final, " .
                "unidad, " .
                "plaza" .
            ") VALUES (" .
                "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?" .
            ") " .
            "ON DUPLICATE KEY UPDATE numero_control = numero_control";
        $aQueryParams = array(
            $aUnidad["id"],
            $nMotivoId,
            $dFechaSurtido,
            $tHoraSurtido,
            $nNumeroControl,
            $dFechaCompromiso,
            $nCapacidadTanque,
            $nPorcetanjeTanque,
            $nPorcetanjeTanque,
            $aUnidad["unidad"],
            $aPlaza["plaza"]
        );
        $aResultado = $oConexionPlaza->query($sQuery, $aQueryParams);

        // se guarda el servicio en la bd de entregas100
        $aResultado = $oConexion->query($sQuery, $aQueryParams);

        // se obtiene el registro de la lista
        $sQuery = "SELECT * " .
            "FROM listas " .
            "WHERE ncontrol = ?" .
            "AND fecha = ?";
        $aQueryParams = array($nNumeroControl, $dFechaSurtido);
        $aResultado = $oConexionPlaza->query($sQuery, $aQueryParams);
        $aRegistroLista = !empty($aResultado) ? $aResultado[0] : null;

        // si existe un registro de lista entonces se agrega un registro a la tabla de servicios
        // para constatar la visita
        if ($aRegistroLista) {
            $sQuery = "INSERT INTO servicios (" .
                    implode(", ", array_keys($aRegistroLista)) // inserta los cmapos que esten en lista
                ") VALUES (" .
                    implode(", ", str_split(str_repeat("?", count($aRegistroLista))))
                ")";
            $aQueryParams = array_values($aRegistroLista);
            $oConexionPlaza->query($sQuery, $aQueryParams);
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Servicio guardado con exito",
            "data" => null
        ));
    }
}
