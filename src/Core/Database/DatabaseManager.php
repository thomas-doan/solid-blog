<?php

namespace App\Core\Database;

use App\Core\QueryBuilder\QueryBuilderSQL;
use App\DevTools\EchoDebug;

/**
 * Cette classe permet de récupérer des informations sur la structure de la base de données
 * 
 */
class DatabaseManager implements DatabaseManagerInterface {


    public static function getInfoTable($table)
    {
        $query = new QueryBuilderSQL();

        $attributes = [
            "TABLE_SCHEMA" => $_ENV['DATABASE_DB'],
            "TABLE_NAME" => $table
        ];

        $query->setQuery('SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, COLUMN_DEFAULT, IS_NULLABLE,
        COLUMN_KEY, EXTRA
        FROM INFORMATION_SCHEMA.COLUMNS');

        $query->where($attributes);

        return $query->sendQuery();
    }

    public static function getRelationsTable($table)
    {
        $query = new QueryBuilderSQL();
        $attributes = [
            "TABLE_NAME" => $table,
            "CONSTRAINT_SCHEMA" => $_ENV['DATABASE_DB']
        ];

        $query->setQuery('SELECT 
        CONSTRAINT_NAME,
        UNIQUE_CONSTRAINT_NAME,
        REFERENCED_TABLE_NAME,
        TABLE_NAME
        FROM 
        INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS ');

        $query->where($attributes);

        $return = $query->sendQuery();
        return $return;
        
    }

    public static function isUnique($table, $field, $value)
    {
        $query = new QueryBuilderSQL();
        $query->setQuery('SELECT NOT EXISTS (
            SELECT 1
            FROM ' . $table . '
            WHERE ' . $field . ' = :value
        ) AS is_unique_value');

        $closure = $query->getClose();
        $closure[':value'] = $value;
        $query->setClose($closure);

        $response = $query->sendQuery(true);
        return $response['is_unique_value'] === 1;
    }
}