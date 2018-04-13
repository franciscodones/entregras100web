<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
App::uses('PyansaConnectionManager', 'Model');

class AppGaseraController extends AppController {

    /**
     * Inserta un registro para el log de la respuesta del webservice
     *
     * @param  string $sKey
     * @param  interger $nUnidad
     * @param  string $sFuncion
     * @param  integer $acceso
     * @param  string $sParametros
     * @param  string $sRespuesta
     * @param  string $sVersion
     * @param  string $sConexion
     * @return boolean
     */
    public function logWS(
        $sKey,
        $nUnidad,
        $sFuncion,
        $acceso,
        $sParametros,
        $sRespuesta,
        $sVersion,
        $sConexion
    ) {
        try {
            $link = $this->conexion();
            $sql = "INSERT INTO logs_ws(".
                "unidad_id, ".
                "sistema_key, ".
                "funcion, ".
                "fecha, ".
                "hora, ".
                "acceso, ".
                "parametros, ".
                "respuesta, ".
                "version, ".
                "conexion, " .
                "api_url) ".
                "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = array(
                $nUnidad,
                $sKey,
                $sFuncion,
                date('Y-m-d'),
                date('H:i:s'),
                $acceso,
                $sParametros,
                $sRespuesta,
                $sVersion,
                $sConexion,
                Router::url(null, true)
            );
            $resultado_entrada = $link->query($sql, $params);
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Obtiene la unidad si existe en la plaza
     *
     * @param  integer $n_unidad
     * @return array|null
     */
    public function getUnidad($n_unidad, $n_empresa) {
        $o_this = $this;
        $o_link = $this->conexion();
        $s_sql = "select unidad.*, ".
                "letra AS tipo, " .
                "precio_gas, ".
                "precio_aditivo, ".
                "zona, ".
                "plaza, ".
                "plaza.id as plaza_id, ".
                "ayudante ".
            "from unidad ".
            "left join zona on unidad.zona_id = zona.id ".
            "left join plaza on zona.plaza_id = plaza.id ".
            "where unidad = ? and plaza.empresa_id = ?";
        $a_resultado = $o_link->query($s_sql, array($n_unidad, $n_empresa));
        $a_resultado = $this->parsearQueryResult($a_resultado);
        if (count($a_resultado) == 0 || $a_resultado[0]['plaza_id'] <= 0) {
            return null;
        }

        return $a_resultado[0];
    }

    /**
     * Alias para la funcion `getConexion`
     *
     * @param  string $name
     * @param  array $config
     * @param  boolean $isSecure
     * @return DataSource
     */
    protected function conexion($name = "default", $config = null, $secure = false) {
        return $this->getConexion($name, $config, $secure);
    }
}
