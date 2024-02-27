<?php

//Class comprennant des méthode static qui permet de verifier la validation des données
namespace App\Core\EntityManager;

use App\Core\EntityManager\EntityAttribute;


class EntityValidator
{
/**
     * Permet de récupérer les informations d'une champs de la table en question
     * @param EntityAttribute[] $fields
     * @param string $field
     * return EntityAttribute
     */
    public static function findField(array $fields,string $field): EntityAttribute
    {
        $index = array_search($field, array_column($fields, 'COLUMN_NAME'));
        if($index === false){
            throw new \Exception("Erreur d'attribution de champ : " . $field . " n'existe pas dans la table ");
        }
        return $fields[$index];
    }


    /**
     * Permet de verifier si le typage d'un champs est correct selon les informations de la table
     * @param EntityAttribute $field
     * @param $value
     * @return void
     */
    public static function validateType(EntityAttribute $field, $value = null): void
    {
        $typeOfValue = gettype($value);
        $compatibilityType = $field->getCompatibilityType();
        if($typeOfValue != $compatibilityType){
            throw new \Exception("Le champ " . $field->getName() . " doit être de type " . $compatibilityType.". Type reçu : " . $typeOfValue);
        }
    }


    /**
     * Permet de s'assurer qu'une données est bien formatée
     * selon les fields référencer en base de données : 
     *   - Si le champ existe dans la table
     *   - Si le champ est nullable
     *   - Si le champ est bien typé
     * @param EntityAttribute[] $attributeFields
     * @param string $field
     * @param $value
     * @return void
     */
    public static function checkField(array $attributeFields,string $field, $value = null): void
    {
        //on verfier que le champes existe dans nos $this-fields selon COLUMN_NAME si on oui on capture l'entrée dans un tableau
        $fieldToCheck = EntityValidator::findField($attributeFields,$field);
        if(!$fieldToCheck->isNullable() && $value == null){
            throw new \Exception("Le champ '" . $field . "' ne peut pas être null");
        }
        EntityValidator::validateType($fieldToCheck, $value);
    }

    /**
     * Permet de verifier si toutes les données d'un ensemble sont bien formatées
     * selon les fields référencer en base de données
     * @param EntityAttribute[] $attributeFields
     * @param array $data
     * @return void
     */
    public static function checkFields(array $attributeFields,array $data): void
    {
        foreach($data as $key => $value){
            EntityValidator::checkField($attributeFields,$key, $value);
        }
    }
}