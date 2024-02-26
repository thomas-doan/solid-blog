<?php

namespace App\Core\EntityManager;

// Principe de ségrégation des interfaces

class EntityAttribute implements EntityAttributeInterface{

    public string $COLUMN_NAME;
    public string $DATA_TYPE;
    public int | null $CHARACTER_MAXIMUM_LENGTH;
    public string | null $COLUMN_DEFAULT;
    public string $IS_NULLABLE;
    public string | null $COLUMN_KEY;
    public string | null $EXTRA;

    public function __construct(array $data)
    {
        $this->COLUMN_NAME = $data['COLUMN_NAME'];
        $this->DATA_TYPE = $data['DATA_TYPE'];
        $this->CHARACTER_MAXIMUM_LENGTH = $data['CHARACTER_MAXIMUM_LENGTH'];
        $this->COLUMN_DEFAULT = $data['COLUMN_DEFAULT'];
        $this->IS_NULLABLE = $data['IS_NULLABLE'];
        $this->COLUMN_KEY = $data['COLUMN_KEY'];
        $this->EXTRA = $data['EXTRA'];
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __isset($name)
    {
        return isset($this->$name);
    }

    public function __toString()
    {
        return $this->COLUMN_NAME;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
    
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public function getName (): string
    {
        return $this->COLUMN_NAME;
    }

    public function getType (): string
    {
        return $this->DATA_TYPE;
    }

    public function getCompatibilityType (): string
    {
        switch ($this->DATA_TYPE) {
            case 'int':
                return 'integer';
                break;
            case 'varchar':
                return 'string';
                break;
            case 'text':
                return 'string';
                break;
            case 'datetime':
                return 'string';
                break;
            case 'date':
                return 'string';
                break;
            default:
                return 'string';
                break;
        }
    }

    public function getMaxLength (): int
    {
        return $this->CHARACTER_MAXIMUM_LENGTH;
    }

    public function isNullable (): bool
    {
        return $this->IS_NULLABLE === 'YES';
    }

    public function isPrimary (): bool
    {
        return $this->COLUMN_KEY === 'PRI';
    }

    public function isAutoIncrement (): bool
    {
        return $this->EXTRA === 'auto_increment';
    }

    public function isUnique (): bool
    {
        return $this->COLUMN_KEY === 'UNI';
    }

    public function isMultiple (): bool
    {
        return $this->COLUMN_KEY === 'MUL';
    }

    public function isForeign (): bool
    {
        return $this->COLUMN_KEY === 'MUL';
    }

    public function isDefault (): bool
    {
        return $this->COLUMN_DEFAULT !== null;
    }

}