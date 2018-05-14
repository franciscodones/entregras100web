<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Datasource\ConnectionManager;
use Exception;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Desactiva el renderizado de una vista
     * @var boolean
     */
    public $autoRender = false;

    /**
     * Retorna la conexion default o crea una nueva con los valores de `config`
     *
     * @param  string $name Nomber de la conexion
     * @param  array $config Configuracion de la conexion
     * @param  boolean $isSecure `true` para devolver un boolean en caso de error, `false` no cacha la excepcion
     * @return DataSource
     */
    protected function getConexion($name = "default", $config = null, $secure = false) {
        try {
            if (empty($config)) {
                return ConnectionManager::get($name);
            } else {
                // crea una nueva conexion con config
                ConnectionManager::config(
                    $name,
                    array(
                        'className' => 'App\Database\Connection',
                        'driver' => 'Cake\Database\Driver\Mysql',
                        'persistent' => false,
                        'host' => $config['host'],
                        'username' => $config['user'],
                        'password' => $config['password'],
                        'database' => $config['database'],
                        'encoding' => 'utf8',
                        'cacheMetadata' => true,
                        'quoteIdentifiers' => false,
                        'log' => false,
                    )
                );
                return ConnectionManager::get($name);
            }
        } catch(Exception $ex) {
            if ($secure) {
                return null;
            } else {
                throw $ex;
            }
        }
    }

    /**
     * Se realiza el JSON con los datos a retornar con su debido Content-Type
     * y las propiedades normalmente usadas para ExtJS
     *
     * @return JsonResponse
     */
    public function asJson($data) {
        $data = array_merge(
            array(
                "success" => false,
                "message" => "",
                "records" => null,
                "metadata" => null
            ),
            $data
        );
        $this->response->type('json');
        $this->response->body(json_encode($data, JSON_NUMERIC_CHECK));
        return $this->response;
    }
}
