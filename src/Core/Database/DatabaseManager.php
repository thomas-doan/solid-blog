<?php

namespace App\Core\Database;

use App\Core\QueryBuilder\QueryDirector;
use App\DevTools\EchoDebug;

/**
 * Cette classe permet de récupérer des informations sur la structure de la base de données
 * 
 */
class DatabaseManager extends QueryDirector{

    private DatabaseInterface $db;

    public function __construct()
    {
       //on intancie le constucteur parent
         parent::__construct();
    }

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
    public function getInfoTable($table)
    {
        $attributes = [
            "TABLE_SCHEMA" => $_ENV['DATABASE_DB'],
            "TABLE_NAME" => $table
        ];

        $this->setQuery('SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, COLUMN_DEFAULT, IS_NULLABLE,
        COLUMN_KEY, EXTRA
        FROM INFORMATION_SCHEMA.COLUMNS');

        EchoDebug::xDebug($this->getQuery());

        $this->where($attributes);

        return $this->sendQuery();
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
    public function getRelationsTable($table)
    {
        $attributes = [
            "TABLE_NAME" => $table,
            "CONSTRAINT_SCHEMA" => $_ENV['DATABASE_DB']
        ];

        $this->setQuery('SELECT 
        CONSTRAINT_NAME,
        UNIQUE_CONSTRAINT_NAME,
        REFERENCED_TABLE_NAME,
        TABLE_NAME
        FROM 
        INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS ');

        $this->where($attributes);

        return $this->sendQuery();
    }

    /**
     * Verfie si un field est bien unique
     * @param string $table
     * @param string $field
     * @param string $value
     * @return bool
     */
    public function isUnique($table, $field, $value)
    {
        $this->setQuery('SELECT NOT EXISTS (
            SELECT 1
            FROM ' . $table . '
            WHERE ' . $field . ' = :value
        ) AS is_unique_value');

        $closure = $this->getClose();
        $closure[':value'] = $value;
        $this->setClose($closure);

        $response = $this->sendQuery(true);
        return $response['is_unique_value'] === 1;
    }
}