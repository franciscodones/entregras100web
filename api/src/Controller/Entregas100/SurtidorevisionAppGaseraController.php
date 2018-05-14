<?php

namespace App\Controller\Entregas100;

use Exception;

class SurtidorevisionAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Registra un servicio de revision
     *
     * @return JsonResponse
     */
    public function surtidorevision_fn() {
        $nParametrosFn = 23;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        $nNumeroControl = $aDatos["numero_control"];
        $nLitros = $aDatos["litros"];
        $nNumeroServicio = $aDatos["numero_servicio"];
        $sHoraInicio = $aDatos["hora_inicio"];
        $sHoraFin = $aDatos["hora_final"];
        $oConexion = $this->getConexion();

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

        // inserta el registro de la recirculacion
        $sQuery = "INSERT INTO servicio_revision (" .
            "unidad_id, " .
            "numero_control, " .
            "fecha, " .
            "hora, " .
            "litros, " .
            "numero_servicio, " .
            "hora_inicio, " .
            "hora_final) " .
            "VALUES (?, ?, ?, ?, ?, ?, ?, ?)" .
            "ON DUPLICATE KEY UPDATE numero_control = numero_control";
        $aQueryParams = array(
            $aUnidad["id"],
            $nNumeroControl,
            date("Y-m-d"),
            $sHoraFin,
            $nLitros,
            $nNumeroServicio,
            $sHoraInicio,
            $sHoraFin
        );
        $oConexion->query($sQuery, $aQueryParams);

        return $this->asJson(array(
            "success" => true,
            "message" => "Servicio guardado con exito",
            "data" => null
        ));
    }
}
