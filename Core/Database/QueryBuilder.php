<?php

namespace App\Core\Database;

use App\Core\Database\Database;
use App\src\Model\Product;

class QueryBuilder
{
    private $db;
    private $conditions;
    private $relations;
    private $setManipulations;
    private $entity;

    public function __construct()
    {
        $this->db = new Database();
        $this->conditions = [];
        $this->relations = [];
        $this->setManipulations = [];
    }

    public function hasMany($class, $localKey, $foreignKey) {
        $entity = new $class();
        return ["tableName" => $entity->table, "localKey" => $localKey, "foreignKey" => $foreignKey, "entity" => $entity, "relationType" => "many"];
    }

    public function hasOne($class, $localKey, $foreignKey) {
        $entity = new $class();
        return ["tableName" => $entity->table, "localKey" => $localKey, "foreignKey" => $foreignKey, "entity" => $entity, "relationType" => "one"];
    }

    public function setEntity($entity): QueryBuilder {
        $this->entity = $entity;
        return $this;
    }

    public function insert($data){
        $this->db->createRecord($this->entity->table, $data);
    }

    public function insertGetId($data){
        return intval($this->db->createRecord($this->entity->table, $data, true)['id']);
    }

    public function update($data){
        return $this->db->updateRecord($this->entity->table, $this->conditions, $data);
    }

    public function delete(){
        return $this->db->deleteRecord($this->entity->table, $this->conditions);
    }

    public function orderByDesc($by){
        return $this->orderBy($by, "DESC");
    }

    public function orderBy($by, $method = ""){
        $this->setManipulations[] = "ORDER BY $by $method";
        return $this;
    }

    public function limit($limit){
        $this->setManipulations[] = "LIMIT $limit";
        return $this;
    }

    public function get(): array {
        return $this->queryDatabase();
    }

    public function first(){
        $this->limit(1);
        return $this->queryDatabase(true);
    }

    public function getRelations(){
        return $this->relations;
    }

    public function find($id){
        $this->conditions[] = ["condition" => ['id' => $id], "operator" => "="];
        return $this;
    }

    public function where($conditions, $value = null){
        if (is_assoc($conditions)){
            foreach ($conditions as $key => $_value){
                $this->conditions[] = ["condition" => [$key => $_value], "operator" => "="];
            }
            return $this;
        }

        if (!is_array($conditions) && isset($value)){
            $this->conditions[] = ["condition" => [$conditions => $value], "operator" => "="];
            return $this;
        }

        foreach($conditions as $condition){
            if (count($condition) == 3){
                $this->conditions[] = ["condition" => [$condition[0] => $condition[2]], "operator" => $condition[1]];
            }
        }

        return $this;
    }

    private function recursiveRelations($entity, $relations) {
        if (empty($relations)) {
            return [];
        }

        $result = [];

        foreach ($relations as $relation => $subRelations) {
            $relationResult = call_user_func([$entity, $relation]);
            $currentResult = [
                'tableName' => $relationResult['entity']->table,
                'localKey' => $relationResult['localKey'],
                'foreignKey' => $relationResult['foreignKey'],
                'relationName' => $relation,
                'relationType' => $relationResult['relationType'],
            ];

            // Check if 'subRelations' is not empty before adding it to the array
            if (!empty($subRelations)) {
                $currentResult['relations'] = $this->recursiveRelations($relationResult['entity'], $subRelations);
            }

            $result[] = $currentResult;
        }

        return $result;
    }

    public function with($relations){
        $relationsAsArray = $this->relationsToArray($relations);
        $this->relations = $this->recursiveRelations($this->entity, $relationsAsArray);
        return $this;
    }

    private function queryDatabase($first = false)
    {
        return $this->db->queryDatabase($this->entity->table,$this->conditions,$this->relations, $this->setManipulations, $first);
    }

    private function relationsToArray($inputString) {
        $result = [];
    
        $substrings = explode(' ', $inputString);
    
        foreach ($substrings as $substring) {
            $parts = explode('.', $substring);
            $temp = &$result;
    
            foreach ($parts as $part) {
                if (!isset($temp[$part])) {
                    $temp[$part] = [];
                }
                $temp = &$temp[$part];
            }
        }
    
        return $result;
    }
}