<?php

App::uses('ConnectionManager', 'Model');

class PyansaConnectionManager extends ConnectionManager{

    /**
     * Sobreescribe la funcion `_init` para poder cambiar el nombre de las clases de las
     * bases de datos declaradas en Config/database.php
     */
    protected static function _init() {
        parent::_init();

        // modifica los datasource de las base de datos ya existentes
        // cambiando el datasource de "Database/Mysql" a "PyansaMysql"
        $dbDeclaradas = get_object_vars(static::$config);
        foreach ($dbDeclaradas as $key => $value) {
            $configObj = static::$config->$key;
            $configObj["datasource"] = preg_replace("/(.*)\/(.*)/", "Pyansa$2", $value["datasource"]);
            static::$config->$key = $configObj;
        }
    }

    /**
     * Sobreescribe la funcion `getDataSource` poder llamar la funcion `_init` en el contexto de esta clase
     * ya que la funcion ConnectionManager::getDataSource utiliza la palabra `self` en vez de `static` para
     * poder cambiar de contexto estatico. (ver http://php.net/manual/en/language.oop5.late-static-bindings.php)
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function getDataSource($name) {
        if (empty(static::$_init)) {
            static::_init();
        }

        return parent::getDataSource($name);
    }

    /**
     * Sobreescribe la funcion `create` poder llamar la funcion `_init` en el contexto de esta clase
     * ya que la funcion ConnectionManager::getDataSource utiliza la palabra `self` en vez de `static` para
     * poder cambiar de contexto estatico. (ver http://php.net/manual/en/language.oop5.late-static-bindings.php)
     * @param  string $name
     * @param  array  $config
     * @return DataSource
     */
    public static function create($name = '', $config = array()) {
        if (empty(static::$_init)) {
            static::_init();
        }

        // modifica los datasource de las base de datos ya existentes
        // cambiando el datasource de "Database/Mysql" a "PyansaMysql"
        $config["datasource"] = preg_replace("/(.*)\/(.*)/", "Pyansa$2", $config["datasource"]);

        if (empty($name) || empty($config) || array_key_exists($name, static::$_connectionsEnum)) {
            return null;
        }
        static::$config->{$name} = $config;
        static::$_connectionsEnum[$name] = static::_connectionData($config);
        $return = static::getDataSource($name);
        return $return;
    }
}
