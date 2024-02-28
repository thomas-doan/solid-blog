<?php

namespace App\Core\QueryBuilder;

interface QueryDirectorInterface
{
    /**
    * Permet de selectionner des éléments dans la base de données
    * @param array | null  $params
    * exemple : ['id', 'name']
    * @return $this
    */
    public function select ($params = null);

    /**
     * Permet de update une element dans la base de données
     * @param string $table table à modifier
     * @param array $params attributs à modifier
     * exemple : ['id' => 1, 'name' => 'toto']
     */
    public function update (string $table, array $params);

    /**
     * Permet de supprimer un élément dans la base de données
     * @param string $table
     * @return $this
     */
    public function delete (string $table);

    /**
     * Permet d'insérer un élément dans la base de données
     * @param string $table
     * @param array $params
     * exemple : ['id' => 1, 'name' => 'toto']
     * @return $this
     */
    public function insert (string $table, array $params);

    /**
     * Permet de selectionner une table dans la base de données
     * @param string $table
     * @return $this
     */
    public function from (string $table);

    /**
     * Permet d'interger une closure dans la requête SQL
     * @param array $condition
     * exemple : ['id' => 1, 'name' => 'toto']
     * @return $this
     */
    public function where (array $condition);

    /**
     * Permet de faire une jointure avec une autre table
     * @param string $table
     * @param array $foreignKeys
     * @return $this
     */
    public function join (string $Entity, array $foreignKeys);
    
    /**
     * Permet d'envoyer la requête sql à la base de données une fois préparée
     * @param bool $isOne
     */
    public function sendQuery($isOne = false);
}


