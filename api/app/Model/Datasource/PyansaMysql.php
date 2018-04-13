<?php

App::uses('Mysql', 'Model/Datasource/Database');

/**
 * Clase creada para sobreescribir los metodos de la clase `Datasource/Mysql`
 */
class PyansaMysql extends Mysql{

    /**
     * Sobreescribe la funcion `_execute` para poder vincular los parametros debidamente
     * de acuerdo a su tipo de dato
     * @param string $sql
     * @param array $params
     * @param array $prepareOptions
     * @return mixed
     * @throws PDOException
     */
    public function _execute($sql, $params = array(), $prepareOptions = array()) {
        $sql = trim($sql);
        if (preg_match('/^(?:CREATE|ALTER|DROP)\s+(?:TABLE|INDEX)/i', $sql)) {
            $statements = array_filter(explode(';', $sql));
            if (count($statements) > 1) {
                $result = array_map(array($this, '_execute'), $statements);
                return array_search(false, $result) === false;
            }
        }
        try {
            $query = $this->_connection->prepare($sql, $prepareOptions);
            $query = $this->bindParams($query, $params);
            $query->setFetchMode(PDO::FETCH_LAZY);
            if (!$query->execute()) {
                $this->_results = $query;
                $query->closeCursor();
                return false;
            }
            if (!$query->columnCount()) {
                $query->closeCursor();
                if (!$query->rowCount()) {
                    return true;
                }
            }
            return $query;
        } catch (PDOException $e) {
            if (isset($query->queryString)) {
                $e->queryString = $query->queryString;
            } else {
                $e->queryString = $sql;
            }
            throw $e;
        }
    }

    /**
     * Vincula los parametros de la sentencia preparada con su debido tipo de dato
     * @param  PDOStatement $statement
     * @param  array  $params
     * @return PDOStatement
     */
    private function bindParams($statement, $params = array()) {
        foreach ($params as $key => $value) {
            $type = PDO::PARAM_STR;

            if (is_bool($value)) {
                $type = PDO::PARAM_BOOL;
            } else if (is_null($value)) {
                $type = PDO::PARAM_NULL;
            } else if (is_int($value)) {
                $type = PDO::PARAM_INT;
            } else {
                $type = PDO::PARAM_STR;
            }
            $statement->bindValue(
                is_numeric($key) ? (intval($key) + 1) : $key,
                $value,
                $type
            );
        }

        return $statement;
    }
}
