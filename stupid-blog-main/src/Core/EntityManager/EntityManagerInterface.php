<?php
/* EntityManagerInterface.php */

namespace Core\EntityManager;

/* Définit l'interface pour les classes de gestion des entités */
interface EntityManagerInterface
{
    public function save(EntityInterface $entity);
    public function create($entity);
    public function update($entity);
    public function delete($entity);
    public function find($entity, $id);
    public function findAll($entity);
}




