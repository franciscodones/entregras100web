<?php

namespace App\Database;

use Cake\Database\Connection as CakeConnection;

class Connection extends CakeConnection {

    /**
     * Executes a query using $params for interpolating values and $types as a hint for each
     * those params.
     *
     * @param string $query SQL to be executed and interpolated with $params
     * @param array $params list or associative array of params to be interpolated in $query as values
     * @param array $types list or associative array of types to be used for casting values in query
     * @return \Cake\Database\StatementInterface executed statement
     */
    public function execute($query, array $params = [], array $types = [])
    {
        $statement = $this->prepare($query);
        if (!empty($params)) {
            $statement->bind($params, $types);
        }
        $statement->execute();

        return $statement;
    }

    /**
     * Executes a SQL statement and returns the Statement object as result.
     *
     * @param string $sql The SQL query to execute.
     * @return \Cake\Database\StatementInterface
     */
    public function query($query, array $params = [], array $types = [])
    {
        // antes de ejecutar el statement
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
}
