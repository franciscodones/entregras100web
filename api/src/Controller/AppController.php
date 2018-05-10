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
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;
use App\Error\ExceptionHandler;

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
     * Sobreescribe el metodo Controller::invokeAction de CakePHP para cachar cualquier instancia de `Exception` que sea arrojada
     * en los controllers y dejar un archivo log con la traza de lo ocurrido.
     *
     * Al cachar una excepcion, esta funcion verificara si el request acepta JSON como respuesta.
     * - En caso de aceptar JSON le sera devuelto uno con success = false y el mensaje de la excepcion.
     * - En caso de no aceptar JSON se volvera a arrojar la excepcion para que CakePHP la procese normalmente.
     *
     * Cada excepcion genera un log con un id unico, este id es guardado en el log para una mejor busqueda y es agregado
     * al mensaje de retorno de acuerdo a los siguientes casos:
     * - En caso que la excepcion sea de la clase `Exception` no se agregara el id al mensaje de la excepcion.
     *      {
     *          "success": false,
     *          "message": "No se encontro informacion de la plaza en la base de datos",
     *          "data": null,
     *          "metadata": {
     *              "log_id": 1234567890
     *          }
     *      }
     * - En caso que la excepcion sea una subclase de `Exception`, se agregara el id al mensaje que se retorne.
     *   Esto facilitara ubicar excepciones no conocidas o no intencionales por los programadores.
     *   Ejemplo: una excepcion de base de datos.
     *       {
     *          "success": false,
     *          "message": "[LOG_ID: 1234567890] Cannot connect to MySQL",
     *          "data": null,
     *          "metadata": {
     *              "log_id": 1234567890
     *          }
     *      }
     *
     * En caso que la excepcion sea intencional para detener el script y devolver un JSON, se recomienda hacerlo arrojando
     * una instancia de la clase `Exception`:
     *
     *      throw new Exception("No se encontro informacion de la plaza en la base de datos");
     *                                          /\
     *             esta linea resultara en un JSON devuelto de la manera antes explicada
     *
     * NOTA: Esta es una mejora para evitar poner un try/catch en todas las funciones de los controller
     * como se hacia anteriormente.
     * Esta pensada para funcionar con request y response en JSON y con los propiedades utilizadas normalmente por
     * Sencha ExtJS
     *
     * @param  CakeRequest $request
     * @return mixed
     */
    /*public function invokeAction() {
        try {
            return parent::invokeAction($request);
        } catch(Exception $e) {
            pr("holis");
            pr($e);
            exit;
            //$handler = new ExceptionHandler($e, $this->request, $this->response);
            //return $handler->handleException();
        }
    }*/

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
