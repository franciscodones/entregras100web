<?php

namespace App\Controller\Entregas100;

use Exception;

class SurtidoAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Registra un servicio surtido
     *
     * @return JsonResponse
     */
    public function surtido_fn() {
        $nParametrosFn = 29;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // si el servicio no trae `clave_surtido` se agrega un string vacio
        if (empty($aDatos['clave_surtido'])) {
            $aDatos['clave_surtido'] = "";
        }

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
        $nLitros = $aDatos['litros'];
        $nPrecioGas = $aDatos['precio_gas'];
        $nPrecioAditivo = $aDatos['precio_aditivo'];
        $nDescuento = $aDatos['descuento_centavo'];
        $nImporteGas = $aDatos['importe_gas'];
        $nImporteAditivo = $aDatos['importe_aditivo'];
        $nImporteDescuento = $aDatos['importe_descuento'];
        $nNumeroServicio = $aDatos['numero_servicio'];
        $nLineaCaptura = $aDatos['linea_captura'];
        $nPuntosGanados = $aDatos['puntos_ganados'];
        $nPuntosOroGanados = $aDatos['puntos_oro_ganados'];
        $dFechaCompromiso = $aDatos['fecha_compromiso'];
        $tHoraInicio = $aDatos['hora_inicio'];
        $tHoraFinal = $aDatos['hora_final'];
        $nCapacidad = $aDatos['capacidad'];
        $nPorcentajeInicial = max(min(99, $aDatos['porcentaje_inicial']), 0);
        $nPorcentajeFinal = max(min(99, $aDatos['porcentaje_final']), 0);
        $nLatitud = $aDatos['latitud'];
        $nLongitud = $aDatos['longitud'];
        $aFormaDePago = json_decode($aDatos['forma_de_pago'], TRUE);
        $nPorcentajeTanque = $aDatos['porcentaje_tanque'];
        $nTipoCompromisoId = (!empty($aDatos['tipo_compromiso_id'])) ? $aDatos['tipo_compromiso_id'] : 0;
        $nPorcentajeTanqueCarburacion = $aDatos['porcentaje_tanque_carburacion'];
        $dFecha = $aDatos['fecha_operacion'];
        $sClaveSurtido = $aDatos['clave_surtido'];
        $nPuntosCanjeados = 0;
        $nNipChofer = $aDatos["nip_chofer"];
        $nNipAyudante = !empty($aDatos["nip_ayudante"]) ? $aDatos["nip_ayudante"] : null;
        $bServicioAgregado = false;
        $nCvecia = 0;
        $nTipoCte = 0;
        $nZona = 0;
        $nSector = 0;
        $sNombre = "CLIENTE NUEVO";
        $sDireccion = "CONOCIDO";
        $nColonia = 0;
        $nCalle = 0;
        $sLetraCompromiso = "E";

        // si el servicio no trae linea de captura se asigna 0
        if (trim($nLineaCaptura) == "") {
            $nLineaCaptura = str_pad("0", 20, "0", STR_PAD_LEFT);
        }

        // se comvierte el tipo_compromiso_id a su respectiva letra
        switch ($nTipoCompromisoId) {
            case 1:
                $sLetraCompromiso = "C";
                break;
            case 2:
                $sLetraCompromiso = "E";
                break;
            case 3:
                $sLetraCompromiso = "L";
                break;
            case 4:
                $sLetraCompromiso = "O";
                break;
            case 5:
                $sLetraCompromiso = "R";
                break;
            case 6:
                $sLetraCompromiso = "T";
                break;
            case 7:
                $sLetraCompromiso = "L";
                break;
            default:
                $sLetraCompromiso = "E";
                break;
        }

        // se suman al `$nImporteDescuento` las formas de pago que son consideradas descuento
        // 14 -> Billete
        // 16 -> Descuento Promocion
        foreach ($aFormaDePago as $value) {
            if (in_array($value["id"], array(14, 16))) {
                $nImporteDescuento += $value["importe"];
            }
        }

        // obtiene el registro del tanque de listas_padron
        $sQuery = "SELECT * " .
            "FROM listas_padron " .
            "WHERE ncontrol = ?";
        $aQueryParams = array($nNumeroControl);
        $aResultado = $oConexionPlaza->query($sQuery, $aQueryParams);
        $aTanque = count($aResultado) > 0 ? $aResultado[0] : null;
        // si existe el tanque cambia los valores de algunas variables
        if ($aTanque != null) {
            $nCvecia = $aTanque["cvecia"];
            $nTipoCte = $aTanque["tipo_cte"];
            $nZona = $aTanque["zona"];
            $nSector = $aTanque["sector"];
            $sNombre = $aTanque["nombre"];
            $sDireccion = $aTanque["direccion"];
            $nColonia = $aTanque["colonia"];
            $nCalle = $aTanque["calle"];
        }

        // actualiza la posicion del tanque si esta definida
        if (!empty($nLatitud) && !empty($nLongitud) && $nLatitud != "undefined" && $nLongitud != "undefined") {
            $sQuery = "UPDATE padron " .
                "SET latitud = ?, " .
                "longitud = ? " .
                "WHERE ncontrol = ?";
            $aQueryParams = array($nLatitud, $nLongitud, $nNumeroControl);
            $oConexionPlaza->query($sQuery, $aQueryParams);
        }

        // actualiza en la lista el estado del servicio
        $sQuery = "UPDATE listas " .
            "SET unidad = ?, " .
                "surtido = ?, " .
                "estado = ?, " .
                "horasurtido = ? " .
            "WHERE ncontrol = ? " .
            "AND fecha = CURDATE()";
        $aQueryParams = array(
            $aUnidad["unidad"],
            "S",
            "U",
            $dFecha . " " . $tHoraFinal,
            $nNumeroControl
        );
        $oConexionPlaza->query($sQuery, $aQueryParams);

        // inserta el servicio atendido en la plaza
        $sQueryInsertarServicio = "INSERT INTO servicio_atendido(" .
                "unidad_id, " .
                "fecha, " .
                "hora, " .
                "numero_control, " .
                "litros, " .
                "importe_gas, " .
                "importe_aditivo, " .
                "importe_descuento, " .
                "numero_servicio, " .
                "linea_captura, " .
                "puntos_ganados, " .
                "puntos_oro_ganados, " .
                "fecha_compromiso, " .
                "hora_inicio, " .
                "hora_final, " .
                "capacidad, " .
                "porcentaje_inicial, " .
                "porcentaje_final, " .
                "clave_surtido, " .
                "latitud, " .
                "longitud, " .
                "unidad, " .
                "plaza) " .
            "VALUES (" .
                "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?" .
            ") " .
            "ON DUPLICATE KEY UPDATE numero_control = numero_control";
        $aQueryParamsInsertarServicio = array(
            $aUnidad["id"],
            $dFecha,
            $tHoraFinal,
            $nNumeroControl,
            $nLitros,
            $nImporteGas,
            $nImporteAditivo,
            $nImporteDescuento,
            $nNumeroServicio,
            str_pad($nLineaCaptura, 20, "0", STR_PAD_LEFT),
            $nPuntosGanados,
            $nPuntosOroGanados,
            $dFechaCompromiso,
            $tHoraInicio,
            $tHoraFinal,
            $nCapacidad,
            $nPorcentajeInicial,
            $nPorcentajeFinal,
            $sClaveSurtido,
            $nLatitud,
            $nLongitud,
            $aUnidad["unidad"],
            $aPlaza["plaza"]
        );
        $aResultado = $oConexionPlaza->query($sQueryInsertarServicio, $aQueryParamsInsertarServicio);

        // obtiene el registro del servicio recien insertado en la plaza
        $sQuerySelecionarServicio = "SELECT id " .
            "FROM servicio_atendido " .
            "WHERE unidad_id = ? " .
            "AND numero_control = ? " .
            "AND fecha = ? " .
            "AND linea_captura = LPAD(?, 20, \"0\")";
        $aQueryParamsSeleccionarServicio = array($aUnidad["id"], $nNumeroControl, $dFecha, $nLineaCaptura);
        $aResultado = $oConexionPlaza->query($sQuerySelecionarServicio, $aQueryParamsSeleccionarServicio);
        $nServicioIdPlaza = $aResultado[0]["id"];

        // se inserta el servicio tambien en la bd del entregas100
        $oConexion->query($sQueryInsertarServicio, $aQueryParamsInsertarServicio);
        // obitene el servicio insertado en la bd del entregas100
        $aResultado = $oConexion->query($sQuerySelecionarServicio, $aQueryParamsSeleccionarServicio);
        // si el servicio no fue insertado se termina el proceso
        if ($aResultado < 1) {
            throw new Exception("Error al guardar el servicio en entregas100");
        }
        $nServicioIdEntregas100 = $aResultado[0]["id"];

        // respalda el servicio en la plaza
        try {
            $sQuery = "CALL respalda(?, ?)";
            $aQueryParams = array("servicio_atendido", $nServicioIdPlaza);
            $oConexionPlaza->query($sQuery, $aQueryParams);

            // respalda el servicio en el entregas100
            $sQuery = "CALL respalda(?, ?)";
            $aQueryParams = array("servicio_atendido", $nServicioIdEntregas100);
            $oConexion->query($sQuery, $aQueryParams);
        } catch (Exception $ex) {
            // si el respaldo da una excepcion se pasa por alto
        }

        // obtiene los registros de las formas de pago si es que el servicio ya fue guardado con anterioridad
        $sQuery = "SELECT * " .
            "FROM servicio_formapago " .
            "WHERE servicio_atendido_id = ?";
        $aQueryParams = array($nServicioIdPlaza);
        $aFormasPagoGuardadas = $oConexionPlaza->query($sQuery, $aQueryParams);

        // si no hay formas guardadas se guardan las recibidas
        if (count($aFormasPagoGuardadas) <= 0) {
            // si se pago con litrogas o puntos se hace una primera iteracion sobre las
            // formas de pago para descontar los litros de la cuenta
            $bRestarLitros = count(
                array_filter(
                    $aFormaDePago,
                    function($item) {
                        return in_array($item["id"], array(8, 9));
                    }
                )
            ) > 0;
            if ($bRestarLitros) {
                foreach ($aFormaDePago as $aForma) {
                    if ($aForma["id"] == 9 || $aForma["id"] == 8) {
                        $nLitros -= $aForma["litros"];
                    }
                }
                $nImporteGas = $nLitros * $nPrecioGas;
                $nImporteAditivo = $nLitros * $nPrecioAditivo;
            }

            foreach ($aFormaDePago as $aForma) {
                // si la forma es litrogas se marcan los litrogas utilizados
                $sFoliosLitrogas = "";
                if ($aForma["id"] == 9) {
                    $sFoliosLitrogas = [];
                    foreach ($aForma['folio_litrogas'] as $aLitrogas) {
                        $sQuery = "UPDATE litrogas " .
                            "SET marca = 1 " .
                            "WHERE folio = ?";
                        $aQueryParams = array($aLitrogas["folio"]);
                        $sFoliosLitrogas[] = $aLitrogas["folio"];
                        $oConexionPlaza->query($sQuery, $aQueryParams);
                    }
                    $sFoliosLitrogas = implode(",", $sFoliosLitrogas);
                }

                // si la forma es billetes se marca el utilizado
                if ($aForma["id"] == 14) {
                    foreach (json_decode($aForma["cuenta_pago"], true) as $billete) {
                        $sQuery = "UPDATE billetes " .
                            "SET valor = ?, " .
                                "fecha_canje = ?, " .
                                "nota = ?, " .
                                "unidad = ? " .
                            "WHERE id = ?";
                        $aQueryParams = array(
                            $billete["valor"],
                            $billete["fecha"],
                            $aForma["numero_nota"],
                            $aUnidad["unidad"],
                            $billete["folio"]
                        );
                        $oConexionPlaza->query($sQuery, $aQueryParams);
                    }
                }

                // si la forma de pago es puntos, se asigna el indice `puntos_utilizados`
                // debido a un bug de la aplicacion que no guarda bien los puntos
                if ($aForma['id'] == 8) {
                    $aForma['puntos_utilizados'] = $aForma["litros"];
                } else {
                    $aForma['puntos_utilizados'] = 0;
                }

                // guarda el registro de la forma de pago en la plaza y en entregas100
                $sQuery = "INSERT INTO servicio_formapago (" .
                    "servicio_atendido_id, " .
                    "forma_pago_id, " .
                    "banco_id, " .
                    "cuenta_pago, " .
                    "numero_autorizacion, " .
                    "numero_nota, " .
                    "litros, " .
                    "importe, " .
                    "puntos_utilizados, " .
                    "folio_litrogas) " .
                    "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $oConexionPlaza->query(
                    $sQuery,
                    array(
                        $nServicioIdPlaza,
                        $aForma["id"],
                        strval($aForma["banco_id"]),
                        strval($aForma["cuenta_pago"]),
                        0,
                        $aForma["numero_nota"],
                        $aForma["litros"],
                        $aForma["importe"],
                        $aForma["puntos_utilizados"],
                        $sFoliosLitrogas
                    )
                );
                $oConexion->query(
                    $sQuery,
                    array(
                        $nServicioIdEntregas100,
                        $aForma["id"],
                        strval($aForma["banco_id"]),
                        strval($aForma["cuenta_pago"]),
                        0,
                        $aForma["numero_nota"],
                        $aForma["litros"],
                        $aForma["importe"],
                        $aForma["puntos_utilizados"],
                        $sFoliosLitrogas
                    )
                );

                // actualiza los folios de venta
                try {
                    // de acuerdo a la forma de pago decide si el folio es "nota" o
                    // el nombre de la forma de pago que deberia e.g. "puntos"
                    $aQuery = "SELECT descripcion, nota " .
                        "FROM forma_pago " .
                        "WHERE id = ?";
                    $aQueryParams = array($aForma["id"]);
                    $aResultado = $oConexion->query($sQuery, $aQueryParams);
                    $sNota = ($aResultado[0]["nota"] ? "nota" : $aResultado[0]["descripcion"]);

                    $sQuery = "UPDATE folios SET " .
                            $sNota . " = " . $sNota . " + 1 " .
                        "WHERE unidad_id = ?";
                    $aQueryParams = array($aUnidad["id"]);
                    $oConexion->query($sQuery, $aQueryParams);
                } catch (Exception $ex) {
                    // en caso de excepcion no se hace nada
                }

                // agrega el servicio en la base de datos de tanques
                // asegurandose que se agregue un servicio para puntos, un servicio para litrogas
                // y otro servicio para las formas de pago restantes
                if (!$bServicioAgregado || in_array($aForma["id"], array(8, 9))) {
                    $bServicioAgregado = ($bServicioAgregado || !in_array($aForma["id"], array(8, 9)));
                    $sQuery = "INSERT INTO servicios (" .
                            "cvecia, " .
                            "tipo_cte, " .
                            "zona, " .
                            "sector, " .
                            "nombre, " .
                            "direccion, " .
                            "ncontrol, " .
                            "fecha, " .
                            "estado, " .
                            "surtido, " .
                            "horasurtido, " .
                            "numero_servicio, " .
                            "litros_surtidos, " .
                            "importe_gas, " .
                            "importe_aditivo, " .
                            "importe_descuento, " .
                            "forma_de_pago, " .
                            "hora_inicio, " .
                            "hora_final, " .
                            "porcentaje_tanque_cliente, " .
                            "fecha_compromiso, " .
                            "porcentaje_tanque, " .
                            "porcentaje_tanque_carburacion, " .
                            "puntos_ganados, " .
                            "puntos_oro_ganados, " .
                            "tipo_de_venta, " .
                            "numero_de_nota, " .
                            "capacid, " .
                            "alarma_ra, " .
                            "constante_calibracion, " .
                            "porcentaje_final, " .
                            "puntos_utilizados, " .
                            "linea_captura, " .
                            "cuenta_pago, " .
                            "unidad, " .
                            "observ, " .
                            "banco_id, " .
                            "plaza, " .
                            "precio_gas, " .
                            "precio_aditivo, " .
                            "descuento_centavos, " .
                            "colonia, " .
                            "calle, " .
                            "tipo_surt) " .
                        "VALUES (" .
                            "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, " .
                            "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, " .
                            "?, ?, ?, ?" .
                        ")";
                    $aQueryParams = array(
                        $nCvecia,
                        $nTipoCte,
                        $nZona,
                        $nSector,
                        $sNombre,
                        $sDireccion,
                        $nNumeroControl,
                        $dFecha,
                        "U",
                        "S",
                        $dFecha . " " . $tHoraInicio,
                        $nNumeroServicio,
                        (in_array($aForma["id"], array(8, 9)) ? $aForma["litros"] : $nLitros),
                        (in_array($aForma["id"], array(8, 9)) ? 0 : $nImporteGas),
                        (in_array($aForma["id"], array(8, 9)) ? 0 : $nImporteAditivo),
                        (in_array($aForma["id"], array(8, 9)) ? 0 : $nImporteDescuento),
                        $aForma["id"],
                        $dFecha . " " . $tHoraInicio,
                        $dFecha . " " . $tHoraFinal,
                        $nPorcentajeInicial,
                        $dFechaCompromiso,
                        $nPorcentajeTanque,
                        $nPorcentajeTanqueCarburacion,
                        (in_array($aForma["id"], array(8, 9)) ? 0 : $nPuntosGanados),
                        (in_array($aForma["id"], array(8, 9)) ? 0 : $nPuntosOroGanados),
                        "F",
                        str_pad($aForma["numero_nota"], 8, "0", STR_PAD_LEFT),
                        $nCapacidad,
                        "",
                        "",
                        $nPorcentajeFinal,
                        $aForma["puntos_utilizados"],
                        str_pad($nLineaCaptura, 20, "0", STR_PAD_LEFT),
                        strval($aForma["cuenta_pago"]),
                        $aUnidad["unidad"],
                        "",
                        strval($aForma["banco_id"]),
                        $aPlaza["plaza2"],
                        $nPrecioGas,
                        $nPrecioAditivo,
                        $nDescuento,
                        $nColonia,
                        $nCalle,
                        $sLetraCompromiso
                    );

                    $aResultado = $oConexionPlaza->query($sQuery, $aQueryParams);

                    // tambien inserta un registro identico en la tabla listas para compatibilidad
                    // con los viejos sistemas
                    $sQuery = str_replace("servicios", "listas", $sQuery);
                    $sQuery .= " ON DUPLICATE KEY UPDATE ncontrol = ncontrol";
                    $oConexionPlaza->query($sQuery, $aQueryParams);
                }
            }
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Servicio guardado con exito",
            "data" => null
        ));
    }
}
