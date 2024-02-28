<?php

namespace App\Core\Database;

use App\Core\QueryBuilder\QueryDirector;
use App\DevTools\EchoDebug;

/**
 * Cette classe permet de récupérer des informations sur la structure de la base de données
 * 
 */
class DatabaseManager{

    /**
     * Permet de récupérer les informations liées à une table
     * @param string $table
     * @return array informations de retour de la table 
     * @COLUMN_NAME => string (Nom de la colonne),
     * @DATA_TYPE => string (Type de la colonne),
     * @CHARACTER_MAXIMUM_LENGTH => int (Taille de la colonne),
     * @COLUMN_DEFAULT => string (Valeur par défaut de la colonne),
     * @IS_NULLABLE => string (La colonne peut-elle être nulle, 1 = oui, 0 = non),
     * @COLUMN_KEY => string (Type de clé, PRI = clé primaire, MUL = clé multiple),
     * @EXTRA => string (Extra information, ex : auto_increment),
     * ]
     */
    public static function getInfoTable($table)
    {
        $query = new QueryDirector();

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

    /**
     * Permet de récupérer les relations étrangères d'une table
     * @param string $table
     * @return array informations de retour de la table
     * @CONSTRAINT_NAME => string (Nom de la contrainte),
     * @UNIQUE_CONSTRAINT_NAME => string (Nom de la contrainte unique),
     * @REFERENCED_TABLE_NAME => string (Nom de la table référencée),
     * @TABLE_NAME => string (Nom de la table)
     */
    public static function getRelationsTable($table)
    {
        $query = new QueryDirector();
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

        return $query->sendQuery();
    }

    /**
     * Verfie si un field est bien unique
     * @param string $table
     * @param string $field
     * @param string $value
     * @return bool
     */
    public static function isUnique($table, $field, $value)
    {
        $query = new QueryDirector();
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