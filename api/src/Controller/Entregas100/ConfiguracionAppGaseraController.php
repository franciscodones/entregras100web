<?php

namespace App\Controller\Entregas100;

use Exception;

class ConfiguracionAppGaseraController extends AppGaseraController {

    /**
     * Funcion utilizada para la aplicacion Entregas100.
     * Retorna la lista de configuraciones a descargar
     *
     * @return JsonResponse
     */
    public function configuracion_fn() {
        $nParametrosFn = 0;
        $aData = func_get_args();
        $aDatos = $aData[1];
        $aUnidad = $aData[2];

        // obtiene las configuraciones de la plaza
        $oConexion = $this->getConexion();
        $sQuery = "SELECT configuracion.id, " .
                "configuracion.descripcion, " .
                "configuracion.respaldo, " .
                "requerido " .
            "FROM configuracion_plaza " .
            "INNER JOIN configuracion ON configuracion_plaza.configuracion_id = configuracion.id " .
            "INNER JOIN plaza ON configuracion_plaza.plaza_id = plaza.id " .
            "WHERE plaza.id = ? " .
            "ORDER BY id";
        $aQueryParams = array($aUnidad["plaza_id"]);
        $aConfiguraciones = $oConexion->query($sQuery, $aQueryParams);

        // si no existen configuraciones se termina el proceso
        if (count($aConfiguraciones) <= 0) {
            throw new Exception("No existe un catalogo de configuraciones");
        }

        // se procesa para enviar solo los campos necesarios y hacer menos pesada la respuesta
        $aConfiguracionesProcesadas = array();
        foreach ($aConfiguraciones as $value) {
            $aConfiguracionesProcesadas[] = array(
                "id" => $value['id'],
                "descripcion" => $value['descripcion'],
                "respaldo" => $value['respaldo'],
                "aplica" => $value['requerido'],
                "requerido" => $value['requerido']
            );
        }

        // se agrega la configuracion de descuentos promocion para que no interfiera con las versiones anteriores
        // este bloque de codigo se debera eliminar al terminar de actualizar toda la plaza y se debera agregar
        // la configuracion de manera normal
        $aPlazasPromocion = array();
        if (in_array($aUnidad["plaza_id"], $aPlazasPromocion)) {
            $aConfiguracionesProcesadas[] = array(
                "id" => 25,
                "descripcion" => "Descuentos promocion",
                "respaldo" => 0,
                "aplica" => 1,
                "requerido" => 1
            );
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Configuraciones a descargar",
            "data" => $aConfiguracionesProcesadas,
            "metadata" => array(
                "Registros" => count($aConfiguracionesProcesadas),
                array(
                    "Registros" => count($aConfiguracionesProcesadas),
                    "id" => "Id de la configuracion",
                    "descripcion" => "Nombre de la configuracion",
                    "respaldo" => "Bandera para trabajar con respaldo: " .
                        "(0 = No podra usar el respaldo, " .
                        "1 = Podra usar el respaldo)",
                    "requerido" => "Bandera para hacer la configuracion requerida para salir a ruta: " .
                        "(0 = No es requerida,1 = Es requerida)",
                )
            )
        ));
    }
}
