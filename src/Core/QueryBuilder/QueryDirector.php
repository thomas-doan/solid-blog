<?php

namespace App\Core\QueryBuilder;

use App\Core\QueryBuilder\Interfaces\QueryBuilderInterface;

/**
 * Chaîne de direction pour la construction de requêtes SQL
 */

 Class QueryDirector
 {
     private QueryBuilderInterface $builder;

     public function __construct(QueryBuilderInterface $builder)
     {
         $this->builder = $builder;
     }

     public function insert($table, $params)
     {
         $this->builder->insert($table, $params);
     }

    public function findOne($table, $params, $primaryKey)
    {
        $this->builder->select($params)->from($table)->where([$primaryKey => $params[$primaryKey]]);
    }

    public function findAll($table, $params)
    {
        $this->builder->select($params)->from($table);
    }

    public function find($table, $params)
    {
        $this->builder->select($params)->from($table)->where($params);
    }

    public function update($table, $params, $primaryKey)
    {
        $this->builder->update($table, $params)->where([$primaryKey => $params[$primaryKey]]);
    }

    public function delete($table, $params)
    {
        $this->builder->delete($table)->where($params);
    }

    public function sendQuery($isOnly = false)
    {
        return $this->builder->sendQuery($isOnly);
    }
}
