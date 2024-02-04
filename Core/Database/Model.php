<?php
namespace App\Core\Database;

use App\Core\Database\QueryBuilder;

class Model
{
    public static function __callStatic($method, $arguments) {
        $entity = get_called_class();
        $queryBuilder = new QueryBuilder();
        return $queryBuilder->setEntity(new $entity)->$method(...$arguments);
    }

    public function __call($method, $arguments) {
        $entity = get_called_class();
        $queryBuilder = new QueryBuilder();
        return $queryBuilder->setEntity(new $entity)->$method(...$arguments);
    }
}