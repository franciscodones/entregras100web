<?php

namespace App\Controller;

use DateTime;

class LogsAlarmasController extends AppController {

    /**
     * Lee los logs_alarmas de una plaza
     * @return JsonResponse
     */
    public function read() {
        $oConexion = $this->getConexion();
        $aDatos = $this->request->query;

        $aLogsProcesados = array();
        $aAlarmasJson = null;
        $aFiltros = isset($aDatos["filter"]) ? json_decode($aDatos["filter"], true) : array();

        // parser de los filtros
        $aFiltrosParsers = array(
            "!=" => function($property) {
                return $property . " != ?";
            },
            "!==" => function($property) {
                return $property . " != ?";
            },
            "/=" => function($property) {
                return $property . " REGEXP ?";
            },
            "<" => function($property) {
                return $property . " < ?";
            },
            "<=" => function($property) {
                return $property . " <= ?";
            },
            "=" => function($property) {
                return $property . " = ?";
            },
            "==" => function($property) {
                return $property . " = ?";
            },
            "===" => function($property) {
                return $property . " = ?";
            },
            ">" => function($property) {
                return $property . " > ?";
            },
            ">=" => function($property) {
                return $property . " >= ?";
            },
            "eq" => function($property) {
                return $property . " = ?";
            },
            "ge" => function($property) {
                return $property . " >= ?";
            },
            "gt" => function($property) {
                return $property . " > ?";
            },
            "in" => function($property, $values) {
                $params = implode(
                    ", ",
                    str_split(
                        str_repeat("?", count($values)),
                        1
                    )
                );
                return $property . " IN (" . $params . ")";
            },
            "le" => function($property) {
                return $property . " <= ?";
            },
            "like" => function($property) {
                return $property . " LIKE CONCAT(\"%\", ?, \"%\")";
            },
            "lt" => function($property) {
                return $property . " < ?";
            },
            "ne" => function($property) {
                return $property . " != ?";
            },
            "notin" => function($property, $values) {
                $params = implode(
                    ", ",
                    str_split(
                        str_repeat("?", count($values)),
                        1
                    )
                );
                return $property . " NOT IN (" . $params . ")";
            }
        );

        // genera las query de los filtros recibidos
        $aFiltros = array_map(function($item) use ($aFiltrosParsers) {
            $item["query"] = $aFiltrosParsers[$item["operator"]]($item["property"], $item["value"]);
            return $item;
        }, $aFiltros);

        // agrega un filtro adicional al inicio
        array_unshift(
            $aFiltros,
            array(
                "value" => array("\"[1-8]\":[1-9][0-9]?", 5),
                "query" => "(alarma REGEXP ? OR litros_no_autorizados >= ?)"
            )
        );

        // obtiene el catalogo de alarmas
        $sQuery = "SELECT * " .
            "FROM alarma";
        $aResultado = $oConexion->query($sQuery);
        $aCatAlarmas = array();
        foreach ($aResultado as $key => $value) {
            $aCatAlarmas[$value["id"]] = $value;
        }

        // obtiene logs logs_alarmas de la plaza
        $sQuery = "SELECT logs_alarmas.*, " .
                "unidad.unidad, " .
                "plaza.id AS plaza_id, " .
                "plaza.ciudad AS plaza " .
            "FROM logs_alarmas " .
            "INNER JOIN unidad ON logs_alarmas.unidad_id = unidad.id " .
            "INNER JOIN zona ON unidad.zona_id = zona.id " .
            "INNER JOIN plaza ON zona.plaza_id = plaza.id " .
            "WHERE " .
            implode(
                " AND ",
                array_map(function($item) {
                    return $item["query"];
                }, $aFiltros)
            );
        $aQueryParams = array_reduce($aFiltros, function($carry, $item) {
            return array_merge($carry, is_array($item["value"]) ? $item["value"] : array($item["value"]));
        }, array());

        $aLogsAlarmas = $oConexion->query($sQuery, $aQueryParams);

        // procesa las alarmas
        foreach ($aLogsAlarmas as $key => $aLogAlarma) {
            if ($aLogAlarma["litros_no_autorizados"]) {
                $aLogsProcesados[] = array(
                    "alarma_id" => 0,
                    "alarma" => "LITROS NO AUTORIZADOS",
                    "cantidad" => null,
                    "unidad_id" => $aLogAlarma["unidad_id"],
                    "unidad" => $aLogAlarma["unidad"],
                    "numero_servicio" => $aLogAlarma["servicio"],
                    "fecha" => $aLogAlarma["fecha"],
                    "hora" => $aLogAlarma["hora"],
                    "litros" => null,
                    "litros_no_autorizados" => $aLogAlarma["litros_no_autorizados"],
                    "numero_control" => $aLogAlarma["numero_control"],
                    "plaza_id" => $aLogAlarma["plaza_id"],
                    "plaza" => $aLogAlarma["plaza"],
                );
            }
            $aAlarmasArray = json_decode($aLogAlarma["alarma"], true);
            foreach ($aAlarmasArray as $index => $cantidad) {
                if ($cantidad > 0) {
                    $aLogsProcesados[] = array(
                        "alarma_id" => $index,
                        "alarma" => $aCatAlarmas[$index]["alarma"],
                        "cantidad" => $cantidad,
                        "unidad_id" => $aLogAlarma["unidad_id"],
                        "unidad" => $aLogAlarma["unidad"],
                        "numero_servicio" => $aLogAlarma["servicio"],
                        "fecha" => $aLogAlarma["fecha"],
                        "hora" => $aLogAlarma["hora"],
                        "litros" => $aLogAlarma["litros"],
                        "litros_no_autorizados" => null,
                        "numero_control" => $aLogAlarma["numero_control"],
                        "plaza_id" => $aLogAlarma["plaza_id"],
                        "plaza" => $aLogAlarma["plaza"],
                    );
                }
            }
        }

        // ordena los logs por alarma_id, unidad, hora
        usort($aLogsProcesados, function($a, $b) {
            $dFechaA = DateTime::createFromFormat("Y-m-d H:i:s", $a["fecha"] . " " . $a["hora"]);
            $dFechaB = DateTime::createFromFormat("Y-m-d H:i:s", $b["fecha"] . " " . $b["hora"]);

            if ($a["alarma_id"] < $b["alarma_id"]) {
                return -1;
            } else if ($a["alarma_id"] > $b["alarma_id"]) {
                return 1;
            } else if ($a["unidad"] < $b["unidad"]) {
                return -1;
            } else if ($a["unidad"] > $b["unidad"]) {
                return 1;
            } else if ($dFechaA < $dFechaB) {
                return -1;
            } else if ($dFechaA > $dFechaB) {
                return 1;
            } else {
                return 0;
            }
        });

        return $this->asJson(array(
            "success" => true,
            "message" => "Alarmas",
            "records" => $aLogsProcesados,
            "metadata" => array(
                "total_registros" => count($aLogsProcesados)
            )
        ));
    }
}
