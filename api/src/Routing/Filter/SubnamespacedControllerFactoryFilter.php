<?php

namespace App\Routing\Filter;

use Cake\Core\App;
use Cake\Event\Event;
use Cake\Routing\DispatcherFilter;
use Cake\Utility\Inflector;
use Cake\Core\Configure;
use ReflectionClass;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 * A dispatcher filter that builds the controller to dispatch
 * in the request.
 *
 * This filter resolves the request parameters into a controller
 * instance and attaches it to the event object.
 */
class SubnamespacedControllerFactoryFilter extends DispatcherFilter
{

    /**
     * La prioridad es asignada elevada para permitir a los otros filtros actuar primero.
     * Es asignada 51 para asegurarse que el este filtro sea llamado despues de
     * Cake\Routing\Filter\ControllerFactoryFilter que posee una prioridad de 50
     *
     * @var int
     */
    protected $_priority = 51;

    /**
     * Resulve el request un controller que ha sido asignado bajo un subnamespace
     * de App\Controller. Ejemplo:
     *
     * App\Controller\Cron\AlarmasCronCrontroller
     *
     * @param \Cake\Event\Event $event
     * @return void
     */
    public function beforeDispatch(Event $event)
    {
        $request = $event->data['request'];
        $response = $event->data['response'];
        $event->data['controller'] = $this->_getController($request, $response);
    }

    /**
     * Obtiene el controller con subnamespace
     *
     * @param \Cake\Network\Request $request
     * @param \Cake\Network\Response $response
     * @return \Cake\Controller\Controller|false
     */
    protected function _getController($request, $response)
    {
        $controller = null;
        $controllerFile = null;
        $namespace = 'Controller';
        if (!empty($request->params['controller'])) {
            $controller = $request->params['controller'];
        }

        // iteracione entre todos los archivos dentro del nombre de espacio App\Controller
        $iterator = new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(APP . "Controller")), "/\.php$/");
        foreach ($iterator as $file) {
            if ($controller && strpos($file->getFileName(), $controller) !== false) {
                $controllerFile = $file;
                break;
            }
        }
        if ($controllerFile) {
            $controller = str_replace(
                array(APP, ".php", "/"),
                array(Configure::read("App.namespace") . "\\", "", "\\"),
                $controllerFile->getPathName()
            );
        }

        $className = false;
        if ($controller) {
            $className = App::classname($controller, $namespace, 'Controller');
        }
        if (!$className) {
            return false;
        }
        $reflection = new ReflectionClass($className);
        if ($reflection->isAbstract() || $reflection->isInterface()) {
            return false;
        }
        return $reflection->newInstance($request, $response, $controller);
    }
}
