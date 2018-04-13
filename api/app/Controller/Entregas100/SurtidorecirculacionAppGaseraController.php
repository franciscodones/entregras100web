<?php

class SurtidorecirculacionAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Registra un servicio surtido
     *
     * @return JsonResponse
     */
    public function surtidorecirculacion_fn() {
        $nParametrosFn = 5;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        $nMotivoId = $aDatos["motivo_id"];
        $nLitros = $aDatos["litros"];
        $nNumeroServicio = $aDatos["numero_servicio"];
        $sHoraInicio = $aDatos["hora_inicio"];
        $sHoraFin = $aDatos["hora_final"];
        $oConexion = $this->conexion();

        // obtiene la conexion a la bd de la plaza
        $oConexion = $this->conexion();
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
        $aResultado = $this->parsearQueryResult($aResultado);
        // si no hay info de la plaza se termina el proceso
        if (count($aResultado) <= 0) {
            throw new Exception("Error al obtener los datos de conexion a la plaza");
        }
        $aPlaza = $aResultado[0];
        $oConexionPlaza = $this->conexion(
            $aPlaza["plaza"],
            array(
                "host" => $aPlaza["ip_te"],
                "user" => $aPlaza["usuario_te"],
                "password" => $aPlaza["password_te"],
                "database" => $aPlaza["base_te"]
            )
        );

        // inserta el registro de la recirculacion
        $sQuery = "INSERT INTO servicio_recirculacion (" .
            "unidad_id, " .
            "motivo_id, " .
            "fecha, " .
            "hora, " .
            "litros, " .
            "numero_servicio, " .
            "hora_inicio, " .
            "hora_final) " .
            "VALUES (?, ?, ?, ?, ?, ?, ?, ?)" .
            "ON DUPLICATE KEY UPDATE unidad_id = unidad_id";
        $aQueryParams = array(
            $aUnidad["id"],
            $nMotivoId,
            date("Y-m-d"),
            $sHoraFin,
            $nLitros,
            $nNumeroServicio,
            $sHoraInicio,
            $sHoraFin
        );
        $oConexionPlaza->query($sQuery, $aQueryParams);

        return $this->asJson(array(
            "success" => true,
            "message" => "Recirculacion guardada con exito",
            "data" => null
        ));
    }
}
