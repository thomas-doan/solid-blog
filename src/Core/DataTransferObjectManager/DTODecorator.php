<?php

namespace App\Core\DataTransferObjectManager;

use App\Core\Database\DatabaseManager;
use App\Core\EntityManager\EntityManager;
use App\DevTools\EchoDebug;

/**
 * Permet d'interprété un object DTO et de le traduire 
 * en donnée utile pour l'QueryDirectory pour la construction de requête SQL
 */
class DTODecorator {
    private DTOManager $Mappeur;
    /** Attribute a envoyer dans le select du QueryBuilder */
    public $attributes;
    /** Tableau des attributs liées à la closure SQL*/
    public $table;
    /** Paramètres présents dans la closure */
    public $params;
    /** retour de la réponse */


    public function __construct(EntityManager $entityManager)
    {
        $this->Mappeur = new DTOManager($entityManager);
    }

    public function formatForInsert(DTOInterface $DTO)
    {
        $this->Mappeur->__process($DTO);
        $pilotes = $this->Mappeur->pilote;
        $this->table = $this->Mappeur->entity->primaryTable;
        $this->params = [];
        foreach ($pilotes as $piloteName => $element) {
            $this->params[$piloteName] = $element->getValue();
        }
        return $this;
    }

    public function formatForUpdate(DTOInterface $DTO)
    {
        $this->Mappeur->__process($DTO);
        $pilotes = $this->Mappeur->pilote;
        echoDebug::xDebug($pilotes);
        //echoDebug::xDebug(DatabaseManager::getInfoTable($this->Mappeur->entity->primaryTable));
        $this->table = $this->Mappeur->entity->primaryTable;
        $this->params = [];
        foreach ($pilotes as $piloteName => $element) {
            if ($element) {
                if(isset(array_keys($element->getMethodes())[0]) && array_keys($element->getMethodes())[0] === "foreignKey"){
                    //Vue que nous somme sur que $element->getValue() est un DTO nous pouvons lire c'est attribut en lisant la classe
                    $subRefelctor = new \ReflectionClass($element->getValue());
                    echoDebug::xDebug($subRefelctor->getProperties());
                    $subAttributes = [];
                    foreach ($subRefelctor->getProperties() as $property) {
                        $subAttributes[] = $property->getName();
                    }
                    $this->params["populate"] = [$element->getMethodes()["foreignKey"], $subAttributes];
                    
                } else {
                    $this->params[$piloteName] = $element->getValue();
                }
            }
            $this->params[$piloteName] = $element->getValue();
        }
        echoDebug::xDebug($this->params);
        exit();
        return $this;
    }

    
}