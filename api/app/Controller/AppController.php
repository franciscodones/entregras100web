<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');
App::uses('PyansaConnectionManager', 'Model');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

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
    public function invokeAction(CakeRequest $request) {
        try {
            return parent::invokeAction($request);
        } catch(Exception $e) {
            $logId = $this->logExceptionTrace("log", $e);
            $isSubclassException = is_subclass_of($e, "Exception");
            $message = ($isSubclassException ? "[LOG_ID: " . $logId . "] " : "") . $e->getMessage();
            $acceptsJson = $this->request->accepts('application/json');
            if ($acceptsJson) {
                // previene que el mensaje muestre el path del servidor
                $documentRootRegex = $_SERVER["DOCUMENT_ROOT"];
                $documentRootRegex = "/" . preg_replace("/[\\/]/", "[\\\\\\\\\/]", $documentRootRegex) . "/";
                $message = preg_replace($documentRootRegex, "", $message);
                return $this->asJson(array(
                    "success" => false,
                    "message" => $message,
                    "metadata" => array(
                        "log_id" => $logId
                    )
                ));
            }
            throw $e;
        }
    }

    /**
     * Agrega un log en el archiv `$file` con la informacion de la excepcion `$exception`.
     *
     * @param  String $file
     * @param  Exception $exception
     * @return integer
     */
    public function logExceptionTrace($file, $exception) {
        $id = (new DateTime())->getTimestamp();
        $msgTpl =
        "\n############################### Exception ###############################\n" .
        "Id: {id}\n" .
        "Message: {message}\n" .
        "File: {file}\n" .
        "Line: {line}\n" .
        "Params (GET):\n" .
        "{paramsGet}\n" .
        "Params (POST):\n" .
        "{paramsPost}\n" .
        "#-------------------------------- Trace --------------------------------#\n" .
        "{trace}";

        $traceTpl =
        "File: {file}\n" .
        "Line: {line}\n" .
        "Class: {class}\n" .
        "Function: {function}\n" .
        "Arguments: {args}\n";

        $aTrace = array();
        foreach ($exception->getTrace() as $key => $trace) {
            if (isset($trace["class"]) && $trace["class"] == "ReflectionMethod" || $key == "10") {
                break;
            }

            $aTrace[] = str_replace(
                array(
                    "{file}",
                    "{line}",
                    "{class}",
                    "{function}",
                    "{args}"
                ),
                array(
                    isset($trace["file"]) ? $trace["file"] : "",
                    isset($trace["line"]) ? $trace["line"] : "",
                    isset($trace["class"]) ? $trace["class"] : "",
                    isset($trace["function"]) ? $trace["function"] : "",
                    isset($trace["args"]) ?
                        json_encode(
                            array_map(
                                function($item) {
                                    // mapea los argumentos para evitar que, si son arrays u objetos,
                                    // el archivo log contenga mucha informacion que normalmente es inutil
                                    if (is_object($item)) {
                                        // si es un objeto solo guarda la clase que es
                                        return "ARGUMENTO TIPO OBJETO: (" . get_class($item) . ")";
                                    } else if (is_array($item)) {
                                        // si es un arreglo lo mapea a un solo nivel
                                        return array_map(
                                            function($valor) {
                                                if (is_object($valor)) {
                                                    return "ARGUMENTO TIPO OBJETO: (" . get_class($valor) . ")";
                                                } else if (is_array($valor)) {
                                                    return "ARGUMENTO TIPO ARRAY";
                                                } else {
                                                    return $valor;
                                                }
                                            },
                                            $item
                                        );
                                    } else {
                                        return $item;
                                    }
                                },
                                $trace["args"]
                            ),
                            JSON_PRETTY_PRINT
                        ) :
                        print_r(array(), true)
                ),
                $traceTpl
            );
        }

        $exString = str_replace(
            array(
                "{id}",
                "{message}",
                "{file}",
                "{line}",
                "{paramsGet}",
                "{paramsPost}",
                "{trace}"
            ),
            array(
                $id,
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                json_encode($this->request->query, JSON_PRETTY_PRINT),
                json_encode($this->request->data, JSON_PRETTY_PRINT),
                implode(implode("   ", str_split(str_repeat("-", 20), 1)) . "\n", $aTrace)
            ),
            $msgTpl
        );
        CakeLog::write($file, $exString);

        return $id;
    }

    /**
     * Retorna la conexion default o crea una nueva con los valores de `config`
     *
     * @param  string $name Nomber de la conexion
     * @param  array $config Configuracion de la conexion
     * @param  boolean $isSecure `true` para devolver un boolean en caso de error, `false` no cacha la excepcion
     * @return DataSource
     */
    protected function getConexion($name = "default", $config = null, $secure = false) {
        $conexion = null;
        try {
            if (empty($config)) {
                return PyansaConnectionManager::getDataSource($name);
            } else {
                // crea una nueva conexion con config
                return PyansaConnectionManager::create(
                    $name,
                    array(
                        'datasource' => 'Database/Mysql',
                        'persistent' => false,
                        'host' => $config['host'],
                        'login' => $config['user'],
                        'password' => $config['password'],
                        'database' => $config['database'],
                        'prefix' => '',
                        'encoding' => 'utf8'
                    )
                );
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
     * Junta todos lo posibles array internos que se generan en las query hechas con cakephp en uno solo
     *
     * @param  array $result Resultado de un query cakephp
     * @return array
     */
    protected function parsearQueryResult($result) {
        if (is_array($result)) {
            foreach ($result as &$row) {
                $arr = array();
                foreach ($row as $subrow) {
                    $arr = array_merge($arr, $subrow);
                }
                $row = $arr;
            }
        }
        return $result;
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
