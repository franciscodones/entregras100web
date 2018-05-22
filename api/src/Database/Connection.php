<?php

namespace App\Database;

use Cake\Database\Connection as CakeConnection;

class Connection extends CakeConnection {

    /**
     * Sobreescribe la funcion `execute` para evitar conflictos con la funcion `query`
     *
     * @param string $query SQL to be executed and interpolated with $params
     * @param array $params list or associative array of params to be interpolated in $query as values
     * @param array $types list or associative array of types to be used for casting values in query
     * @return \Cake\Database\StatementInterface executed statement
     */
    public function execute($query, array $params = [], array $types = []) {
        $statement = $this->prepare($query);
        if (!empty($params)) {
            $statement->bind($params, $types);
        }
        $statement->execute();

        return $statement;
    }

    /**
     * Sobreescribe la funcion `query` para utilizarla de manera generica.
     *
     * @param string $sql The SQL query to execute.
     * @return \Cake\Database\StatementInterface
     */
    public function query($query, array $params = [], array $types = []) {
        // obtiene los tipos de datos de los parametros;
        $defaultTypes = $this->getParamsTypes($params);
        foreach ($defaultTypes as $key => &$value) {
            if (array_key_exists($key, $types)) {
                $value = $types[$key];
            }
        }
        unset($value);
        $types = $defaultTypes;

        $statement = $this->execute($query, $params, $types);
        if (preg_match("/^SELECT|CALL/i", $query) && $statement->columnCount() > 0) {
            // si es una sentencia SELECT se retorna un array con los registros
            // si es una sentencia CALL que retorne registros se verifica que exista al menos una columna
            return $statement->fetchAll("assoc");
        } else if (preg_match("/^(?:INSERT|UPDATE|DELETE)/i", $query)) {
            // si es una sentencia INSERT, UPDATE o DELETE se retorna el numero de registros afectados
            return $statement->rowCount();
        }

        return $statement;
    }

    /**
     * Retorna un array con los tipos de datos de los parametros
     * @param  array  $params
     * @return array
     */
    public function getParamsTypes(array $params = []) {
        $types = array();

        foreach ($params as $key => $value) {
            $type = "string";
            if (is_int($value)) {
                $type = "integer";
            } else if (is_float($value) || is_double($value)) {
                $type = "float";
            }else if (is_bool($value)) {
                $type = "boolean";
            } else if (is_a($value, "DateTime")) {
                $type = "datetime";
            } else {
                $type = "string";
            }
            $types[$key] = $type;
        }

        return $types;
    }

    /**
     * Retorna el id del ultimo registro insertado
     * @return number
     */
    public function lastInsertId() {
        return $this->driver()->lastInsertId();
    }
}
