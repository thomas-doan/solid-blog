<?php

namespace App\Core\Accessors;

use BadMethodCallException;
use ReflectionClass;
use ReflectionProperty;

/**
 * Génère les accesseurs magiques pour les propriétés publiques
 * 
 */
trait AccessorGenerator
{
    public function __call($methodName, $arguments)
    {
        error_reporting(E_ALL & ~E_DEPRECATED);
        // Détecter les getters et setters à partir du nom de la méthode
        $propertyName = lcfirst(substr($methodName, 3));
        $isGetter = strpos($methodName, 'get') === 0;
        $isSetter = strpos($methodName, 'set') === 0;

        // S'assurer que la propriété existe
        if (property_exists($this, $propertyName)) {
            // Appeler la propriété directement pour les getters
            if ($isGetter) {
                error_reporting(E_ALL);
                return $this->$propertyName;
            }

            // Modifier la propriété pour les setters
            if ($isSetter) {
                // on s'assure que le type de l'attribut soit respecté
                if (gettype($arguments[0]) !== gettype($this->$propertyName)) {
                    throw new BadMethodCallException ("Le type de l'attribut $propertyName n'est pas respecté");
                }
                $this->$propertyName = $arguments[0];
                error_reporting(E_ALL);
                return;
            }
        }
        error_reporting(E_ALL);

        // Méthode non trouvée
        throw new BadMethodCallException("Méthode $methodName non existante");
    }

    public function generateAccessor()
    { 
        error_reporting(E_ALL & ~E_DEPRECATED);
        //on récupère le nom de chaque attribute de la classe
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $capitalizedPropertyName = ucfirst($propertyName);
            $this->generateGetter($propertyName, $capitalizedPropertyName);
            $this->generateSetter($propertyName, $capitalizedPropertyName);
        }

        error_reporting(E_ALL);
    }

    public function generateGetter($propertyName, $capitalizedPropertyName)
    {
        $this->{'get'.$capitalizedPropertyName} = function () use ($propertyName) {
            return $this->$propertyName;
        };
    }

    public function generateSetter($propertyName, $capitalizedPropertyName)
    {
        $this->{'set'.$capitalizedPropertyName} = function ($value) use ($propertyName) {
            $this->{$propertyName} = $value;
        };
    }
}