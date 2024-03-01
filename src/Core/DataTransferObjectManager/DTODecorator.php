<?php

namespace App\Core\DataTransferObjectManager;

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
        $this->table = $this->Mappeur->entity->primaryTable;
        $this->params = [];
        foreach ($pilotes as $piloteName => $element) {
            $this->params[$piloteName] = $element->getValue();
        }
        return $this;
    }
}