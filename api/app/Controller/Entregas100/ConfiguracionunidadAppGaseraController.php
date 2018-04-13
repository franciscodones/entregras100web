<?php

class ConfiguracionunidadAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la lista de configuraciones para la unidad
     *
     * @return JsonResponse
     */
    public function configuracionunidad_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene la conexion a la bd de la plaza
        $oConexion = $this->conexion();
        $sQuery = "SELECT * FROM plaza WHERE plaza.id = ?";
        $aQueryParams = array($aUnidad['plaza_id']);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $aResultado = $this->parsearQueryResult($aResultado);
        // si no hay info de la plaza se termina el proceso
        if (count($aResultado) <= 0) {
            throw new Exception("Error al obtener los datos de conexion a la plaza");
        }
        $aPlaza = $aResultado[0];

        // obtiene la informacion de la empresa
        $sQuery = "SELECT * FROM empresa WHERE id = ?";
        $aQueryParams = array($aPlaza['empresa_id']);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $aResultado = $this->parsearQueryResult($aResultado);
        // si no hay info de la empresa se termina el proceso
        if (count($aResultado) <= 0) {
            throw new Exception("Error al obtener los datos de la empresa");
        }
        $aEmpresa = $aResultado[0];

        // obtiene las funcionalidades que la unidad puede solicitar al SV600
        $sQuery = "SELECT clave " .
            "FROM funcionalidades_unidad " .
            "INNER JOIN funcionalidades ON funcionalidad_id = funcionalidades.id " .
            "WHERE unidad_id = ?";
        $aQueryParams = array($aUnidad["id"]);
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $aFuncionalidades = $this->parsearQueryResult($aResultado);
        $aFuncionalidades = array_map(function($item) {
            return $item["clave"];
        }, $aFuncionalidades);

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aConfiguracionesProcesadas = array(
            "ciudad" => $aPlaza["ciudad"] . ", " . $aPlaza["estado"],
            "capacidad_minima_tanque" => 0,
            "direccion_matriz" => $aEmpresa["direccion_matriz"],
            "direccion_sucursal" => $aPlaza["direccion_sucursal"],
            "factor_control" => $aPlaza["factor_control"],
            "factor_space" => $aPlaza["factor_space"],
            "funcionalidades" => $aFuncionalidades,
            "nombre_permiso" => $aEmpresa["nombre_permiso"],
            "online" => $aUnidad["online"],
            "permiso" => $aPlaza["permiso"],
            "porcentaje_minimo_carburacion" => 0,
            "porcentaje_minimo_tanque" => 0,
            "rfc" => str_replace("-", "", $aEmpresa['rfc']),
            "razon_social" => $aEmpresa["razon_social"],
            "serie" => $aPlaza["plaza"],
            "telefono_pedidos" => $aPlaza["telefono_pedido"],
            "telefono_quejas" => $aPlaza["telefono_queja"],
            "telefono_quejas2" => $aPlaza["telefono_queja2"],
            "tipo_vehiculo" => $aUnidad["tipo"],
            "unidad_otorga_puntos" => $aPlaza["otorga_puntos"],
            "url_facturacion" => $aEmpresa["url"],
            "timeoutRecepcionTrama" => 10 * 1000,
            "tiempoContinuarSurtido" => 120 * 1000
        );

        return $this->asJson(array(
            "success" => true,
            "message" => "Configuraciones de la unidad",
            "data" => $aConfiguracionesProcesadas,
            "metadata" => array(
                "Registros" => count($aConfiguracionesProcesadas),
                array(
                    "Registros" => count($aConfiguracionesProcesadas)
                )
            )
        ));
    }
}
