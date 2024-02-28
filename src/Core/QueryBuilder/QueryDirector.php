<?php
/* Création du QueryBuilder qui permettra de faire des requêtes SQL à la base données */
namespace App\Core\QueryBuilder;

use App\Core\Accessors\AccessorGenerator;
use App\Core\Database\Database;
use App\DevTools\EchoDebug;

interface QueryDirectorInterface
{
    public function select ($params = null);
    public function update (string $table, array $params);
    public function delete (string $table);
    public function insert (string $table, array $params);
    public function from (string $table);
    public function where (array $condition);
    public function join (string $Entity, array $foreignKeys);
    public function sendQuery($isOne = false);
}

/**
 * Cette classe permet de faire des requêtes SQL à la base de données
 * elle instancie la classe Database pour gérer la connexion à la base de données et la méthode query pour faire des requêtes SQL sécurisé
 */
class QueryDirector  {
    use AccessorGenerator;
    /** Connection à la base de données */
    private $db;
    /** Requête SQL */
    private $query = '';
    /** Paramètres de la requête SQL dans le select */
    private $select = [];
    /** Tableau des attributs liées à la closure SQL*/
    private $close = [];
    /** Tableau des jointures avec d'autres entités */
    private $join = [];
    /** table des attributs */
    private $table = '';



    public function __construct()
    {
        $this->generateAccessor();
        $this->db = new Database();
    }


    /**
     * Permet d'envoyer la requête sql à la base de données une fois préparée
     * @param bool $isOne
     */
    public function sendQuery($isOne = false)
     {
        $req = $this->db->query($this->query, $this->close, $isOne);
        $this->query = '';
        $this->close = [];
        return $req;
     }

     /**
      * Permet de selectionner des éléments dans la base de données
        * @param array | null  $params
        * exemple : ['id', 'name']
        * @return $this
      */
    public function select ($params = null )
    {
        if (empty($params)) {
            $this->query = 'SELECT *';
        } else {
            $this->query = 'SELECT ';
            $this->query .= implode(', ', $params);
        }

        return $this;
    }

    /**
     * Permet de update une element dans la base de données
     * @param string $table table à modifier
     * @param array $params attributs à modifier
     * exemple : ['id' => 1, 'name' => 'toto']
     */
    public function update (string $table, array $params)
    {
        $this->table = $table;
        $this->query = 'UPDATE ' . $this->table . ' SET ';
        $completedQuery = '';
        foreach($params as $key => $value){
            $completedQuery .= $key . ' = :' . $key . ', ';
            $this->close[':'.$key] = $value;
        }
        $this->query .= rtrim($completedQuery, ', ');
        return $this;
    }

    /**
     * Permet de supprimer un élément dans la base de données
     * @param string $table
     * @return $this
     */
    public function delete (string $table)
    {
        $this->query = 'DELETE FROM ' . $table;
        return $this;
    }

    /**
     * Permet d'insérer un élément dans la base de données
     * @param string $table
     * @param array $params
     * exemple : ['id' => 1, 'name' => 'toto']
     * @return $this
     */
    public function insert (string $table, array $params)
    {
        $this->table = $table;
        $this->query = 'INSERT INTO ' . $this->table . ' (';
        $completedQuery = '';
        foreach($params as $key => $value){
            $completedQuery .= $key . ', ';
            $this->close[':'.$key] = $value;
        }
        $this->query .= rtrim($completedQuery, ', ') . ') VALUES (';
        $completedQuery = '';
        foreach($params as $key => $value){
            $completedQuery .= ':' . $key . ', ';
        }
        $this->query .= rtrim($completedQuery, ', ') . ')';
        return $this;
    }



    public function from (string $table)
    {
        $this->query .= ' FROM ' . $table;

        return $this;
    }

    /**
     * Permet d'interger une closure dans la requête SQL
     * @param array $condition
     * exemple : ['id' => 1, 'name' => 'toto']
     * @return $this
     */
    public function where (array $condition)
    {
        $completedQuery = [];
        foreach($condition as $key => $value){
            if(is_string($key)){
                $completedQuery[] = $key . ' = :' . $key;
                $this->close[':'.$key] = $value;
            } else {
                if($value === '*'){
                    unset($condition[$key]);
                } else {
                    throw new \Exception('Erreur dans la requête SQL, pour fonctionner la closure doit avoir une référence. Exemple : [\'id\' => 1, \'name\' => \'toto\'].<br>
                    Référence non trouvée ou est un index : ' . $key);
                }
            }
        }

        $this->query .= ' WHERE ' . implode(' AND ', $completedQuery);
        return $this;
    }

    /**
     * Permet de faire une jointure avec une autre table
     * @param string $table
     * @param array $foreignKeys
     * @return $this
     */
    public function join (string $Entity, array $foreignKeys)
    {
        //on fake un join en faisaint un select sur la table en question
        $this->query = 'SELECT * FROM ' . $Entity;
        $this->where($foreignKeys);
        return $this;
    }
}
