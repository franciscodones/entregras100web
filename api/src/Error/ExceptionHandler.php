<?php

namespace App\Error;

use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Error\ErrorHandler;
use Cake\Error\Debugger;
use Cake\Error\FatalErrorException;
use Exception;
use DateTime;
use Cake\Core\Configure;

class ExceptionHandler extends ErrorHandler {

    /**
     * Id para identificar la excepcion
     * @var string
     */
    protected $id;

    /**
     * Request
     * @var Cake\Network\Request
     */
    protected $request;

    /**
     * Response
     * @var Cake\Network\Response
     */
    protected $response;

    /**
     * Template para el trace de la excepcion
     * @var array
     */
    protected $traceTpl = array(
        "File: {file}\n",
        "Line: {line}\n",
        "Class: {class}\n",
        "Function: {function}\n",
        "Arguments: {args}\n"
    );

    /**
     * Template para el log de la excepcion
     * @var array
     */
    protected $exceptionTpl = array(
        "\n############################### Exception ###############################\n",
        "Id: {id}\n",
        "Message: {message}\n",
        "File: {file}\n",
        "Line: {line}\n",
        "Params (GET):\n",
        "{paramsGet}\n",
        "Params (POST):\n",
        "{paramsPost}\n",
        "#-------------------------------- Trace --------------------------------#\n",
        "{trace}",
    );

    /**
     * Constructor
     * @param Exception $exception
     * @param Cake\Network\Request $request
     */
    public function __construct($options = array()) {
        $this->request = Request::createFromGlobals();
        $this->response = new Response();
        $this->id = $this->generateId();
        parent::__construct($options);
    }

    /**
     * Obtiene el template para el trace de la excepcion
     * @return string
     */
    protected function getTraceTemplate() {
        if (is_array($this->traceTpl)) {
            $this->traceTpl = implode("", $this->traceTpl);
        }

        return $this->traceTpl;
    }

    /**
     * Asigna el template para el trace de la excepcion
     * @param string|array $tpl
     */
    protected function setTraceTemplate($tpl) {
        if (is_array($tpl)) {
            $tpl = implode("", $tpl);
        }

        $this->traceTpl = $tpl;
    }

    /**
     * Obtiene el template para la excepcion
     * @return string
     */
    protected function getExceptionTemplate() {
        if (is_array($this->exceptionTpl)) {
            $this->exceptionTpl = implode("", $this->exceptionTpl);
        }

        return $this->exceptionTpl;
    }

    /**
     * Asigna el template para la excepcion
     * @param string|array $tpl
     */
    protected function setExceptionTemplate($tpl) {
        if (is_array($tpl)) {
            $tpl = implode("", $tpl);
        }

        $this->exceptionTpl = $tpl;
    }

    /**
     * Genera el ID de la excepcion
     * @return string
     */
    protected function generateId() {
        $now = new DateTime();
        return $now->getTimestamp();
    }

    /**
     * Construye el log reemplazando los valores de `$exceptionTpl` con los valores de `$exception`
     * @param  integer $id
     * @return string
     */
    private function buildExceptionLog($exception) {
        return str_replace(
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
                $this->id,
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                json_encode($this->request->query, JSON_PRETTY_PRINT),
                json_encode($this->request->data, JSON_PRETTY_PRINT),
                $this->buildTraceLog($exception)
            ),
            $this->getExceptionTemplate()
        );
    }

    /**
     * Construye el string del trace de la excepcion
     * @param  Exception $exception
     * @return string
     */
    private function buildTraceLog($exception) {
        $stackTrace = array();

        foreach ($exception->getTrace() as $trace) {
            $file = isset($trace["file"]) ? $trace["file"] : "";
            $line = isset($trace["line"]) ? $trace["line"] : "";
            $class = isset($trace["class"]) ? $trace["class"] : "";
            $function = isset($trace["function"]) ? $trace["function"] : "";
            $args = isset($trace["args"]) ? $trace["args"] : array();

            // mapea los argumentos para evitar que, si son arrays u objetos,
            // el archivo log contenga mucha informacion que normalmente es inutil
            $args = array_map(
                function($item) {
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
                $args
            );
            $args = empty($args) ? print_r(array(), true) : json_encode($args, JSON_PRETTY_PRINT);

            $stackTrace[] = str_replace(
                array(
                    "{file}",
                    "{line}",
                    "{class}",
                    "{function}",
                    "{args}"
                ),
                array(
                    $file,
                    $line,
                    $class,
                    $function,
                    $args
                ),
                $this->getTraceTemplate()
            );
        }

        return implode(implode("   ", str_split(str_repeat("-", 20), 1)) . "\n", $stackTrace);
    }

    /**
     * Sobreescribe la funcion `handleFatalError` para evitar duplicar el log en el archivo y
     * modificar el mensaje de error para brindar un poco mas de informacion.
     *
     * @param int $code Code of error
     * @param string $description Error description
     * @param string $file File on which error occurred
     * @param int $line Line that triggered the error
     * @return bool
     */
    public function handleFatalError($code, $description, $file, $line) {
        $data = [
            'code' => $code,
            'description' => $description,
            'file' => $file,
            'line' => $line,
            'error' => 'Fatal Error',
        ];

        $message = 'Fatal Error (' . $code . '): ' . $description . ' in [' . $file . ', line ' . $line . ']';
        $this->handleException(new FatalErrorException($message, 500, $file, $line));
        return true;
    }

    /**
     * Controla lo que se debe mostrar/enviar al ocurrir una excepcion
     * @param  Exception $exception
     * @return Cake\Network\Response
     */
    public function _displayException($exception) {
        try {
            if (!$this->request->accepts('application/json')) {
                return parent::_displayException($exception);
            }

            $message = $exception->getMessage();
            if (is_subclass_of($exception, "Exception")) {
                $message = "[LOG_ID: " . $this->id . "] " .$message;
            }

            // elimina la ruta del documento hasta la carpeta root del proyecto
            $appDirRegex = getcwd();
            $appDirRegex = str_replace("\\", "/", $appDirRegex);
            $appDirRegex = preg_replace("/[\\\\\\/]?webroot$/", "", $appDirRegex);
            $appDirRegex = "/" . preg_replace("/[\\\\\\/]/", "[\\\\\\\\\\/]", $appDirRegex) . "/";
            $message = preg_replace($appDirRegex, "", $message);

            // plan B es eliminar al menos la ruta del documento hasta el carpeta del servidor
            $rootDirRegex = $_SERVER["DOCUMENT_ROOT"];
            $rootDirRegex = str_replace("/", "\\", $rootDirRegex);
            $rootDirRegex = "/" . preg_replace("/[\\\\\\/]/", "[\\\\\\\\\\/]", $rootDirRegex) . "/";
            $message = preg_replace($rootDirRegex, "", $message);

            $data = array(
                "success" => false,
                "message" => $message,
                "metadata" => array(
                    "log_id" => $this->id
                )
            );
            $this->response->type('json');
            $this->response->body(json_encode($data, JSON_NUMERIC_CHECK));
            $this->_clearOutput();
            $this->_sendResponse($this->response);
        } catch (Exception $e) {
            // Disable trace for internal errors.
            $this->_options['trace'] = false;
            $message = sprintf(
                "[%s] %s\n%s", // Keeping same message format
                get_class($e),
                $e->getMessage(),
                $e->getTraceAsString()
            );
            trigger_error($message, E_USER_ERROR);
        }
    }

    /**
     * Obtiene el mensaje de la excepcion para el log
     * @param  Exception $exception
     * @return string
     */
    protected function _getMessage(Exception $exception) {
        return $this->buildExceptionLog($exception);
    }

    /**
     * Sobreescribe la funcion `handleError` para que errores del nivel warning y notice
     * terminen el script.
     *
     * @param int $code Code of error
     * @param string $description Error description
     * @param string|null $file File on which error occurred
     * @param int|null $line Line that triggered the error
     * @param array|null $context Context
     * @return bool True if error was handled
     */
    public function handleError($code, $description, $file = null, $line = null, $context = null) {
        if (error_reporting() === 0) {
            return false;
        }
        list($error, $log) = $this->mapErrorCode($code);
        if ($log === LOG_ERR) {
            return $this->handleFatalError($code, $description, $file, $line);
        }
        $data = [
            'level' => $log,
            'code' => $code,
            'error' => $error,
            'description' => $description,
            'file' => $file,
            'line' => $line,
        ];

        $debug = Configure::read('debug');
        if ($debug) {
            $data += [
                'context' => $context,
                'start' => 3,
                'path' => Debugger::trimPath($file)
            ];
        }
        $this->handleFatalError($code, $description, $file, $line);
        // mata el script para evitar que el script continue en errores como
        // warnings, notice, debug, etc
        exit;
    }
}
