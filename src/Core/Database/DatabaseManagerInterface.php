<?php

namespace App\Core\Database;


interface DatabaseManagerInterface
{
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
    public static function getInfoTable($table);

    /**
     * Permet de récupérer les relations étrangères d'une table
     * @param string $table
     * @return array informations de retour de la table
     * @CONSTRAINT_NAME => string (Nom de la contrainte),
     * @UNIQUE_CONSTRAINT_NAME => string (Nom de la contrainte unique),
     * @REFERENCED_TABLE_NAME => string (Nom de la table référencée),
     * @TABLE_NAME => string (Nom de la table)
     */
    public static function getRelationsTable($table);

    /**
     * Verfie si un field est bien unique
     * @param string $table
     * @param string $field
     * @param string $value
     * @return bool
     */
    public static function isUnique($table, $field, $value);
}