<?php

class PlazasController extends AppController {

    /**
     * Lee el catalogo de las plazas
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();

        // obtiene todas las plazas
        $sQuery = "SELECT * FROM plaza";
        $aResultado = $oConexion->query($sQuery);
        $aPlazas = $this->parsearQueryResult($aResultado);

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de plazas",
            "data" => $aPlazas,
            "metadata" => array(
                "total_registros" => count($aPlazas)
            )
        ));
    }

    /**
     * Crea una plaza
     * @return JsonResponse
     */
    public function create() {
        $oConexion = $this->getConexion();

        $this->request->data["datos"] = json_decode($this->request->data["datos"], true);
        $aDatos = $this->request->data["datos"][0];
        $nEmpresaId = $aDatos["empresa_id"];
        $sPlaza = $aDatos["plaza"];
        $sPlaza2 = $aDatos["plaza2"];
        $sCiudad = $aDatos["ciudad"];
        $sEstado = $aDatos["estado"];
        $sDireccionSucursal = $aDatos["direccion_sucursal"];
        $sTelefonoPedido = $aDatos["telefono_pedido"];
        $sTelefonoQueja = $aDatos["telefono_queja"];
        $sTelefonoQueja2 = $aDatos["telefono_queja2"];
        $sPermiso = $aDatos["permiso"];
        $sOficio = "";
        $nPrecioGas = 0;
        $nPrecioAditivo = 0;
        $nPrecioAditivoc = 0;
        $nFactorControl = $aDatos["factor_control"];
        $nFactorSpace = $aDatos["factor_space"];
        $nLitrosVale = 10;
        $nClientesEstacionario = $aDatos["clientes_estacionario"];
        $nClientesPortatil = 0;
        $nLimiteDescarga = $aDatos["limite_descarga"];
        $nFechaLista = 0;
        $bOtorgaPuntos = $aDatos["otorga_puntos"];
        $sEstatus = "";
        $sFechaOperacion = "0000-00-00";
        $sFechaPlanta = "0000-00-00";
        $nTarifaId = 1;

        // agrega el registro de la plaza
        $sQuery = "INSERT INTO plaza (" .
                "empresa_id, " .
                "plaza, " .
                "plaza2, " .
                "ciudad, " .
                "estado, " .
                "direccion_sucursal, " .
                "telefono_pedido, " .
                "telefono_queja, " .
                "telefono_queja2, " .
                "permiso, " .
                "oficio, " .
                "precio_gas, " .
                "precio_aditivo, " .
                "precio_aditivoc, " .
                "factor_control, " .
                "factor_space, " .
                "litros_vale, " .
                "clientes_estacionario, " .
                "clientes_portatil, " .
                "limite_descarga, " .
                "fecha_lista, " .
                "otorga_puntos, " .
                "estatus, " .
                "fecha_operacion, " .
                "fecha_planta, " .
                "tarifa_id" .
            ") VALUES (" .
                "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?" .
            ")";
        $aQueryParams = array(
            $nEmpresaId,
            $sPlaza,
            $sPlaza2,
            $sCiudad,
            $sEstado,
            $sDireccionSucursal,
            $sTelefonoPedido,
            $sTelefonoQueja,
            $sTelefonoQueja2,
            $sPermiso,
            $sOficio,
            $nPrecioGas,
            $nPrecioAditivo,
            $nPrecioAditivoc,
            $nFactorControl,
            $nFactorSpace,
            $nLitrosVale,
            $nClientesEstacionario,
            $nClientesPortatil,
            $nLimiteDescarga,
            $nFechaLista,
            $bOtorgaPuntos,
            $sEstatus,
            $sFechaOperacion,
            $sFechaPlanta,
            $nTarifaId
        );
        $aResultado = $oConexion->query($sQuery, $aQueryParams);
        $nPlazaId = $oConexion->lastInsertId();

        return $this->asJson(array(
            "success" => true,
            "message" => "Plaza agregada",
            "data" => array(
                "id" => $nPlazaId
            )
        ));
    }

    /**
     * Actualiza una plaza
     * @return JsonResponse
     */
    public function update() {
        $oConexion = $this->getConexion();
        throw new Exception("HOLIS");

        $this->request->data["datos"] = json_decode($this->request->data["datos"], true);
        $aDatos = $this->request->data["datos"][0];
        $nId = $aDatos["id"];
        $nEmpresaId = $aDatos["empresa_id"];
        $sPlaza = $aDatos["plaza"];
        $sPlaza2 = $aDatos["plaza2"];
        $sCiudad = $aDatos["ciudad"];
        $sEstado = $aDatos["estado"];
        $sDireccionSucursal = $aDatos["direccion_sucursal"];
        $sTelefonoPedido = $aDatos["telefono_pedido"];
        $sTelefonoQueja = $aDatos["telefono_queja"];
        $sTelefonoQueja2 = $aDatos["telefono_queja2"];
        $sPermiso = $aDatos["permiso"];
        $nFactorControl = $aDatos["factor_control"];
        $nFactorSpace = $aDatos["factor_space"];
        $nClientesEstacionario = $aDatos["clientes_estacionario"];
        $nLimiteDescarga = $aDatos["limite_descarga"];
        $bOtorgaPuntos = $aDatos["otorga_puntos"];

        // actualiza el registro de la plaza
        $sQuery = "UPDATE plaza SET " .
                "empresa_id = ?, " .
                "plaza = ?, " .
                "plaza2 = ?, " .
                "ciudad = ?, " .
                "estado = ?, " .
                "direccion_sucursal = ?, " .
                "telefono_pedido = ?, " .
                "telefono_queja = ?, " .
                "telefono_queja2 = ?, " .
                "permiso = ?, " .
                "factor_control = ?, " .
                "factor_space = ?, " .
                "clientes_estacionario = ?, " .
                "limite_descarga = ?, " .
                "otorga_puntos = ? " .
            "WHERE id = ?";
        $aQueryParams = array(
            $nEmpresaId,
            $sPlaza,
            $sPlaza2,
            $sCiudad,
            $sEstado,
            $sDireccionSucursal,
            $sTelefonoPedido,
            $sTelefonoQueja,
            $sTelefonoQueja2,
            $sPermiso,
            $nFactorControl,
            $nFactorSpace,
            $nClientesEstacionario,
            $nLimiteDescarga,
            $bOtorgaPuntos,
            $nId
        );
        $oConexion->query($sQuery, $aQueryParams);

        return $this->asJson(array(
            "success" => true,
            "message" => "Plaza actualizada"
        ));
    }
}
