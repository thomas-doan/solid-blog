<?php
/* Création du QueryBuilder qui permettra de faire des requêtes SQL à la base données */
namespace App\Core\QueryBuilder;

use App\Core\Accessors\AccessorGenerator;
use App\Core\Database\Database;
use App\DevTools\EchoDebug;

/**
 * Cette classe permet de faire des requêtes SQL à la base de données
 * elle instancie la classe Database pour gérer la connexion à la base de données et la méthode query pour faire des requêtes SQL sécurisé
 */
class QueryDirector implements QueryDirectorInterface{
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

    public function sendQuery($isOnly = false)
     {
        $req = $this->db->query($this->query, $this->close, $isOnly);
        $this->query = '';
        $this->close = [];
        return $req;
     }

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

    
    public function delete (string $table)
    {
        $this->query = 'DELETE FROM ' . $table;
        return $this;
    }

    
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

    public function join (string $Entity, array $foreignKeys)
    {
        //on fake un join en faisaint un select sur la table en question
        $this->query = 'SELECT * FROM ' . $Entity;
        $this->where($foreignKeys);
        return $this;
    }
}
