<?php

namespace App\Core\Database;

class Database
{

    private $host;
    private $username;
    private $password;
    private $databaseName;
    private $conn;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];
        $this->databaseName = $_ENV['DB_NAME'];
        $this->conn = null;
    }


    public function connect()
    {
        $this->conn = new \mysqli($this->host, $this->username, $this->password, $this->databaseName);
    }

    public function close()
    {
        $this->conn->close();
    }

    public function updateRecord($table, $conditions, $data, $persist = true){
        if ($persist) { $this->connect(); }

        $sqlColumns = "SET ";
        $keys = array_keys($data);
        $lastKey = end($keys);
        foreach($data as $name => $value){
            if ($name == $lastKey) {
                $sqlColumns .= "$name = \"$value\"";
                break;
            }
            $sqlColumns .= "$name = '$value', ";
        }

        $query = "UPDATE $table $sqlColumns" . ($conditions ? " WHERE " . $this->buildWhereClause($conditions) : "");
        $result = $this->executeQuery($query);
        if ($persist) { $this->close(); }
        return $result;
    }

    public function deleteRecord($table, $conditions, $persist = true){
        if ($persist) { $this->connect(); }
        $query = "DELETE FROM $table". ($conditions ? " WHERE " . $this->buildWhereClause($conditions) : "");
        $result = $this->executeQuery($query);
        if ($persist) { $this->close(); }
        return $result;
    }

    public function queryDatabase($table, $conditions, $relations, $setManipulations,$first, $persist = true) {
        if ($persist) { $this->connect(); }
        $query = "SELECT * FROM $table" . ($conditions ? " WHERE " . $this->buildWhereClause($conditions) : "");
        foreach($setManipulations as $clause){ $query .= " $clause"; }
        $result = $this->executeQuery($query);
        if ($first){
            $mainResults = $this->fetchMainResults($result, $relations, $persist);
            if(isset($mainResults[0])){
                return $mainResults[0];
            }
            return (object) [];
        }
        return $this->fetchMainResults($result, $relations, $persist);
    }

    private function buildWhereClause($conditions) {
        return implode(" AND ", array_map(function($condition) {
            $key = key($condition['condition']);
            $value = $condition['condition'][$key];
            $operator = isset($condition['operator']) ? $condition['operator'] : "=";

            // Validate the operator to prevent SQL injection
            $allowedOperators = ['=', '<', '>', '<=', '>=', '!='];
            $operator = in_array($operator, $allowedOperators) ? $operator : '=';

            return "$key $operator '{$this->conn->real_escape_string($value)}'";
        }, $conditions));
    }

    public function executeQuery($query) {
        $result = $this->conn->query($query) or die("Query failed: " . $this->conn->error);
        return $result;
    }


    private function fetchMainResults($result, $relations, $persist) {
        $mainResults = [];
        while ($row = $result->fetch_assoc()) {
            $mainResults[] = (object)$this->fetchRelations($row, $relations);
        }

        if ($persist) { $this->close(); }
        return $mainResults;
    }

    private function fetchRelations($row, $relations) {
        foreach ($relations as $relation) {
            $tableName = $relation['tableName'];
            $relationName = $relation['relationName'];
            $relationType = $relation['relationType'];
            $localKey = $relation['localKey'];
            $foreignKey = $relation['foreignKey'];
            $relatedQuery = "SELECT * FROM $tableName WHERE $foreignKey = " . $row[$localKey];
            $relatedResult = $this->executeQuery($relatedQuery);
            $result = $this->fetchRelatedRows($relatedResult, $relation);
            if($relationType == "one"){
                $row[$relationName] = $result[0] ?? (object)[];
            }else{
                $row[$relationName] = $result;
            }
        }
        return (object)$row;
    }

    private function fetchRelatedRows($relatedResult, $relation) {
        $relatedRows = [];
        while ($relatedRow = $relatedResult->fetch_assoc()) {
            $relatedRows[] = isset($relation['relations']) && is_array($relation['relations']) ? $this->fetchRelations($relatedRow, $relation['relations']) : (object) $relatedRow;
        }
        return $relatedRows;
    }

    public function createRecord($tableName, $data, $getId = false, $persist = true)
    {
        if ($persist) {
            $this->connect();
        }

        if ($this->conn === null) {
            die("Query failed: " . $this->conn->error);
        }

        $tableName = $this->conn->real_escape_string($tableName);

        $columns = array_map([$this->conn, 'real_escape_string'], array_keys($data));
        $values = array_map([$this->conn, 'real_escape_string'], array_values($data));

        $columnList = implode(', ', $columns);
        $valueList = "'" . implode("', '", $values) . "'";

        $query = "INSERT INTO `$tableName` ($columnList) VALUES ($valueList)";

        $result = $this->conn->query($query);

        if ($result === false) {
            die("Query failed: " . $this->conn->error);
        }

        if ($getId){
            $query = "SELECT LAST_INSERT_ID() AS id;";
            $result = $this->conn->query($query);

            if ($result === false) {
                die("Query failed: " . $this->conn->error);
            }

            $data = null;
            while ($row = $result->fetch_assoc()) {
                $data = $row;
            }
            if ($persist) {
                $this->close();
            }
            return $data;

        }
        if ($persist) {
            $this->close();
        }
    }

}