<?php

class LitrogasAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la lista de litrogas
     *
     * @return JsonResponse
     */
    public function litrogas_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

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

        // obtiene los litrogas
        $sQuery = "SELECT * " .
            "FROM litrogas " .
            "WHERE marca = ? " .
            "ORDER BY control, folio";
        $aQueryParams = array(0);
        $aResultado = $oConexionPlaza->query($sQuery, $aQueryParams);
        $aLitrogas = $this->parsearQueryResult($aResultado);

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aLitrogasProcesados = array();
        foreach ($aLitrogas as $value) {
            $aLitrogasProcesados[] = array(
                "numero_control" => $value['control'],
                "folio" => $value['folio'],
                "litros" => $value['litrogas'],
                "fecha" => $value['fecha']
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Litrogas",
            "data" => $aLitrogasProcesados,
            "metadata" => array(
                "Registros" => count($aLitrogasProcesados),
                array(
                    "Registros" => count($aLitrogasProcesados)
                )
            )
        ));
    }
}
