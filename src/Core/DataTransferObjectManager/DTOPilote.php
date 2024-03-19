<?php

namespace App\Core\DataTransferObjectManager;

/**
 * Mementos pour les attribut de transfer de données
 */
class DTOPilote {
    public string $fieldName; 
    public $origneValue;
    public $value;
    public array $methodes;
    public $history;

    public function __construct(string $fieldName, $value)
    {
        $this->fieldName = $fieldName;
        $this->origneValue = $value;
        $this->value = $value;
    }

    public function addMethode($methodeName, $value)
    {
        $this->methodes[$methodeName] = $value;
    }

    public function getMethode($methodeName)
    {
        return $this->methodes[$methodeName];
    }

    public function getMethodes()
    {
        return $this->methodes;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getOrigneValue()
    {
        return $this->origneValue;
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function addHistory($methodeName, $value)
    {
        $this->history[$methodeName] = $value;
    }

    public function getHistory($methodeName)
    {
        return $this->history[$methodeName];
    }

    public function getHistorys()
    {
        return $this->history;
    }

}