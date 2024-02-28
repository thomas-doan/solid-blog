<?php
/* Création de la classe EntityManager */

namespace App\Core\EntityManager;

use App\Core\Accessors\AccessorGenerator;
use App\Core\Database\DatabaseManager;
use App\Core\QueryBuilder\QueryDirector;
use App\Core\EntityManager\EntityAttribute;
use App\Core\EntityManager\EntityAttributeInterface;
use App\Core\EntityManager\EntityValidator as EntityValidator;

// Open-Closed Principle
// Liskov Substitution Principle - 3ème partie
class EntityManager extends QueryDirector 
{
    use AccessorGenerator;
    public string $primaryKey = 'id';
    public string $primaryTable ;
    /** @var EntityAttributeInterface[] */
    public $fields = [];
    public array $relations = [];
    public array $result = [];
    /** Conserve l'historique des requête de l'entité */
    public array $historyQuery = [];

    public function __construct(string $primaryTable)
    {
        $this->generateAccessor();
        $this->primaryTable = $primaryTable;
        parent::__construct();

        foreach (DatabaseManager::getInfoTable($primaryTable) as $field) {
            $this->fields[] = new EntityAttribute($field);
        }
        
        $this->relations = DatabaseManager::getRelationsTable($primaryTable);

    }

    // public function getFields(): array
    // {
    //     return $this->fields;
    // }

    // public function setFields(array $fields): void
    // {
    //     $this->fields = $fields;
    // }

    // public function getRelations(): array
    // {
    //     return $this->relations;
    // }

    // public function setRelations(array $relations): void
    // {
    //     $this->relations = $relations;
    // }

    // public function getResult(): array
    // {
    //     return $this->result;
    // }

    // public function setResult(array $result): void
    // {
    //     $this->result = $result;
    // }

    public function getHistoryQuery(): array
    {
        return $this->historyQuery;
    }

    public function saveQuery (string $functionName, string | array $query, array $params): void
    {
        $this->historyQuery[] = ['function'=> $functionName,'query' => $query, 'params' => $params];
    }

    public function getLastQuery(): array
    {
        return end($this->historyQuery);
    }

    /**
     * Permet de retourné un seul résultat
     * @param $id array | int
     * @return array
     * @exemple findOne(1) | Succes ! Renvoie le post avec l'id 1
     * @exemple findOne(["title" => "test",'*']) | Succes ! Renvoie le post avec le titre test et tous les attributs
     * @exemple findOne(["title" => "test", id]) | Succes ! Renvoie le post avec le titre test et seulement l'id
     * @exemple findOne("test") | Fail ! L'id doit être un entier
     * @exemple findOne(["title", "id"]) | Fail ! Une valeur de référence doit être éférencée afin de retourner un résultat unique
     * @important Si les filtres sont trop larges, seulement le premier résultat sera retourné
     */
    public function findOne(array | int $id): void
    {
        /** Détermine   */
        $isUniqueResponse = true;

        if (is_array($id)) {
            $attributes = [];
            $hasKeyString = false;
            $hasFullAttributes = false;
            foreach ($id as $key => $value) {
                if(is_int($key)){
                    $attributes[] = $value;
                    if($value == '*'){
                        echo "test";
                        $hasFullAttributes = true;
                    }
                } else {
                    EntityValidator::checkField($this->fields, $key, $value);
                    echo "in else : " . $key . " " . $value;
                    $hasKeyString = true;
                    $attributes[] = $key;
                }
            }
            if($hasKeyString === false){
                throw new \Exception("Une valeur de référence doit être référencée afin de retourner un résultat unique");
            }
            if($hasFullAttributes === true){
                $attributes = ['*'];
            }
            $this->select($attributes);
            $this->from($this->primaryTable);
            $this->where($id);
        } else {
            if (is_int($id)) {
                $this->select();
                $this->from($this->primaryTable);
                $this->where([$this->primaryKey => $id]);
                $isUniqueResponse = true;
            } else {
                throw new \Exception("L'id doit être un entier");
            }
        }
        $this->saveQuery('findOne', $this->getQuery(), $this->getClose());
        $this->result = $this->sendQuery($isUniqueResponse);
    }



    /**
     * Permet de retourner plusieurs résultats
     * @param $params array
     * @return array
     * @exemple find() | Succes ! Renvoie tous les posts
     * @exemple find(1) | Succes ! Renvoie le post avec l'id 1
     * @exemple find(["title" => "test",'*']) | Succes ! Renvoie tout les posts avec le titre test et tous les attributs
     * @exemple find(["title" => "test", id]) | Succes ! Renvoie tout les posts avec le titre test et seulement l'id
     */
    public function find(array $params = null): void
    { 
        /**
         * Défini si la requête doit comprendre des relations de tables
         * @var bool | array
         */
        $needPopulate = false;

        if (empty($params)) {
            $this->select();
            $this->from($this->primaryTable);
        } else {
            $attributes = [];
            $hasFullAttributes = false;

            foreach ($params as $key => $value) {
                if(is_int($key)){
                    $attributes[] = $value;
                    if($value == '*'){
                        $hasFullAttributes = true;
                    }
                } else {
                    if($key == 'populate'){
                        if(is_array($value)){
                            $tableRelationName = $value[0];
                            $populateParams = $value[1];
                            if(is_string($tableRelationName)){
                                if(is_array($populateParams)){

                                    $needPopulate = [
                                        'table' => $tableRelationName,
                                        'params' => $populateParams
                                    ];

                                    unset($params[$key]);
                                
                                } else {
                                    throw new \Exception("La valeur de populate doit être un tableau");
                                }
                            } else {
                                throw new \Exception("La première valeur de populate doit être une chaine de caractère représentant le nom de la table de la relation");
                            }

                            
                        } else {
                            throw new \Exception("La valeur de populate doit être un tableau");
                        }
                    } else { $attributes[] = $key; }
                }
            }

            if($hasFullAttributes == true){
                $attributes = ['*'];
            }

            $this->select($attributes);
            $this->from($this->primaryTable);
            $this->where($params);
        }

        $this->saveQuery('find', $this->getQuery(), $this->getClose());
        $this->result = $this->sendQuery();
        if($needPopulate !== false){
            $this->populate($needPopulate['table'], $needPopulate['params']);
        }
    }

    /**
     * Permet de sauvegarder un élément dans la base de données
     * @param $params array
     * @exemple save(["title" => "test", "content" => "test", "user_id" => 1, "category_id" => 1]) | Succes ! Sauvegarde le post avec le titre test, le contenu test, l'id de l'utilisateur 1 et l'id de la catégorie 1
     */
    public function save(array $params): void
    {
        //on verfie que les champs sont bien formater
        EntityValidator::checkFields($this->fields, $params);
        $this->insert($this->primaryTable, $params);
        $this->saveQuery('save', $this->getQuery(), $this->getClose());
        $this->sendQuery();
    }

    /**
     * Permet de mettre à jour un élément dans la base de données
     * @param $reference int | array
     * @param $params array
     * @exemple update(1, ["title" => "test", "content" => "test"]) | Succes ! Met à jour le post avec l'id 1 avec le titre test et le contenu test
     * @exemple update(["title" => "test"], ["title" => "OtherTexte", "content" => "test"]) | Succes ! Met à jour le post avec le titre test avec le titre OtherTexte et le contenu test
     */
    public function modify(int $id, array $params): void
    {
        $this->update($this->primaryTable, $params);
        if(is_int($id)){
            $this->where([$this->primaryKey => $id]);
        } else {
            EntityValidator::checkFields($this->fields, $id);
            $this->where($id);
        }
        $this->saveQuery('modify', $this->getQuery(), $this->getClose());
        $this->sendQuery();
    }

    /**
     * Permet de récupérer et asociée une relation à l'objet
     * @param string $relationTableName Nom de la table de la relation
     * @param array $params Paramètres de la requête
     * @return void
     */
    public function populate(string $relationTableName, array $params): void
    {
        $referenceFounded = false;
        foreach ($this->relations as $key => $value) {
            if($value['REFERENCED_TABLE_NAME'] == $relationTableName){
                $referenceFounded = $this->relations[$key];
            }
        };

        if($referenceFounded === false){
            throw new \Exception("La relation " . $relationTableName . " n'existe pas");
            return;
        }

        $lastFunction = $this->getLastQuery()['function'];
        
        switch ($lastFunction) {
            case 'find':
                //on verfie que le champs dans result existe selon CONSTRAINT_NAME de referenceFounded
                if(isset($this->result[0][$referenceFounded['CONSTRAINT_NAME']])){
                    $multipleHistoryQuery = [
                        'function' => "populateMany",
                        'query' => [],
                        'close' => []
                    ];

                    foreach ($this->result as $keyTable => $valueTable) {

                        $this->join($referenceFounded['REFERENCED_TABLE_NAME'],["id" => $valueTable[$referenceFounded['CONSTRAINT_NAME']]]);

                        $multipleHistoryQuery['query'][] = $this->getQuery();
                        $multipleHistoryQuery['close'][] = $this->getClose();

                        $subResult = $this->sendQuery();
                        
                        if(count($subResult) > 1){
                            $this->result[$keyTable][$referenceFounded['REFERENCED_TABLE_NAME']] = $subResult;            
                        } else {
                            $this->result[$keyTable][$referenceFounded['REFERENCED_TABLE_NAME']] = $subResult[0];
                        }

                        unset($this->result[$keyTable][$referenceFounded['CONSTRAINT_NAME']]);

                    }
                    $this->saveQuery($multipleHistoryQuery['function'], $multipleHistoryQuery['query'], $multipleHistoryQuery['close']);

                } else {
                    throw new \Exception("Le champ " . $referenceFounded['CONSTRAINT_NAME'] . " n'existe pas dans le résultat");
                }
                break;
            case 'findOne':
                    
                    if(isset($this->result[$referenceFounded['CONSTRAINT_NAME']])){
                        $this->join($referenceFounded['REFERENCED_TABLE_NAME'],["id" => $this->result[$referenceFounded['CONSTRAINT_NAME']]]);

                        $this->saveQuery('populate', $this->getQuery(), $this->getClose());

                        $subResult = $this->sendQuery();
                        
                        if(count($subResult) > 1){
                            $this->result[$referenceFounded['REFERENCED_TABLE_NAME']] = $subResult;            
                        } else {
                            $this->result[$referenceFounded['REFERENCED_TABLE_NAME']] = $subResult[0];
                        }

                        unset($this->result[$referenceFounded['CONSTRAINT_NAME']]);

                    } else {
                        throw new \Exception("Le champ" . $referenceFounded['CONSTRAINT_NAME'] . " doit être renseigné pour la méthode findOne");
                    }
                break;
            default:
                throw new \Exception("La méthode " . $lastFunction . " n'est pas supportée pour la méthode populate");
                break;
        }

    }

    /**
     * Verifie si le filed est unique
     * @param $value string
     * @param $field string
     * @return bool
     */
    public function isUniqueField(string $field, string $value): bool
    {
        return $this->isUnique($this->primaryTable, $field, $value);
    }


}



    