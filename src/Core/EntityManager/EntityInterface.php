<?php
/* Création de l'entity interface qui sera implémentée par les classes d'entités */
namespace Core\EntityManager;

/**
 * Définit l'interface pour les classes d'entités
 */
interface EntityInterface
{
    public function getId();
    public function setId($id);
    public function getCreatedAt();
    public function setCreatedAt($createdAt);
    public function getUpdatedAt();
    public function setUpdatedAt($updatedAt);
    

}