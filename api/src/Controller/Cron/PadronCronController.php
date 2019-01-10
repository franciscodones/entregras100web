<?php

namespace App\Controller\Cron;

use DateTime;
use Exception;
use App\Controller\AppController;
use Pyansa\Error\Log as PyansaLog;
use Cake\Log\Log;
use App\Controller\Entregas100\SolicitarlistaAppGaseraController;
use App\Controller\Entregas100\SolicitarPadronAppGaseraController;

class PadronCronController extends AppController
{
    /**
     * Comienza el bucle para generar el padron de la plazas proporcionadas
     *
     * @return Cake\Network\Response
     */
    public function generar()
    {
        // se asigna el limite a 25 min, ~1 min por plaza
        set_time_limit(1500);
        $aPlazasId = json_decode($_GET["plazas"], true);

        // obtiene la conexion a la bd de la plaza
        $oConexion = $this->getConexion();
        $sQuery = "SELECT plaza.*, " .
                "conexion.ip_te, " .
                "conexion.usuario_te, " .
                "conexion.password_te, " .
                "conexion.base_te " .
            "FROM plaza " .
            "INNER JOIN conexion ON plaza.id = conexion.plaza_id ";
        $aPlazas = $oConexion->query($sQuery);
        foreach ($aPlazasId as $nPlazaId) {
            try {
                $aPlaza = array_filter($aPlazas, function($item) use ($nPlazaId) {
                    return $item["id"] == $nPlazaId;
                });
                $aPlaza = empty($aPlaza) ? null : reset($aPlaza);
                // si no hay info de la plaza se termina la iteracion con una excepcion
                if (!$aPlaza) {
                    throw new Exception("No existe una configuracion de conexion de la plazaId: " . $nPlazaId);
                }
                $oConexionPlaza = $this->getConexion(
                    $aPlaza["plaza"],
                    [
                        "host" => $aPlaza["ip_te"],
                        "user" => $aPlaza["usuario_te"],
                        "password" => $aPlaza["password_te"],
                        "database" => $aPlaza["base_te"]
                    ]
                );
                $this->generarPadron($aPlaza, $oConexionPlaza);
            } catch (Exception $e) {
                $log = new PyansaLog($e, $_GET, $_POST);
                Log::error($log->getMessage());
                continue;
            }
        }

        return $this->asJson([
            "success" => true,
            "message" => "Padrones generados"
        ]);
    }

    /**
     * Genera un padron en la plaza y base de datos proporcionada
     *
     * @param  array $aPlaza
     * @param  Pyansa\Database\Connection $oConexionPlaza
     * @return void
     */
    protected function generarPadron($aPlaza, $oConexionPlaza)
    {
        $dFechaPadron = date("Y-m-d");
        $aPlaza["plaza"] = strtolower($aPlaza["plaza"]);
        // si existe la tabla padron_app_<plaza> se elimina para crearla de nuevo
        // esto para refrescar el padron
        $sQuery = "DROP TABLE IF EXISTS padron_app_" . $aPlaza["plaza"];
        $oConexionPlaza->query($sQuery);

        // genera la lista de trabajo en una tabla temporal
        $sQuery = "CREATE TEMPORARY TABLE lista_app AS (" .
            SolicitarlistaAppGaseraController::getListaQueryString() .
            ")";
        $aQueryParams = array($aPlaza["otorga_puntos"], $aPlaza["id"], $dFechaPadron, $dFechaPadron);
        $oConexionPlaza->query($sQuery, $aQueryParams);

        // genera una copia temporal de listas_padron, en esta copia no se repetiran controles en caso
        // que la listas_padron tenga demasiados registros
        $sQuery = "CREATE TEMPORARY TABLE listas_padron_unificado AS " .
            "SELECT DISTINCT * " .
            "FROM listas_padron " .
            "GROUP BY ncontrol " .
            "ORDER BY ncontrol ASC, fecha DESC ";
        $oConexionPlaza->query($sQuery);

        // genera el padron combinado con la lista en una tabla
        $sQuery = "CREATE TABLE padron_app_" . $aPlaza["plaza"] . " AS (" .
            SolicitarPadronAppGaseraController::getPadronQueryString() .
            ")";
        $aQueryParams = array($aPlaza["otorga_puntos"], $aPlaza["id"]);
        $oConexionPlaza->query($sQuery, $aQueryParams);
    }
}
