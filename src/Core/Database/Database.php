<?php


namespace App\Core\Database;




class Database implements DatabaseInterface
{
    private $db;
    private $entity;

    public function __construct()
    {

        /* Par défault on utilise les variable .env pour la connexion à la base de données */
        $this->db = new \PDO('mysql:host='.$_ENV['HOST_DB'].';dbname='.$_ENV['DATABASE_DB'], $_ENV['USER_DB'], $_ENV['PASSWORD_DB'], array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));

    }

    public function query($statement, $attributes = [], $isOnly = false){

        if($attributes){
            $req = $this->db->prepare($statement);
            $req->execute($attributes);
        } else {
            //On traite notre $statement pour éviter les injections SQL
            $req = $this->db->query($statement);
        }
        $req->setFetchMode(\PDO::FETCH_ASSOC);
        if($isOnly){
            $data = $req->fetch();
        } else {
            $data = $req->fetchAll();
        }

        return $data;
    }
}
?>