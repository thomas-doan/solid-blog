<?php

namespace App\Core\Accessors;

use BadMethodCallException;
use ReflectionClass;
use ReflectionProperty;

/**
 * Génère les accesseurs magiques pour les propriétés publiques
 * S'apparente a une méthode virtuelle en C++ ou Java
 * @method __call (string $methodName, array $arguments) : mixed
 *  - valide l'existance des méthodes get et set pour chaque attribut de la classe
 * @method generateAccessor() : void 
 *  - genère les accesseurs pour chaque attribut de la classe
 * @method generateGetter(string $propertyName, string $capitalizedPropertyName) : void 
 *  - genère les getters pour chaque attribut de la classe
 * @method generateSetter(string $propertyName, string $capitalizedPropertyName) : void
 *  - genère les setters pour chaque attribut de la classe
 * 
 * @info : Cet traite doit être appellé dans la classe qui l'implémente
 *  - use AccessorGenerator;
 *  - Dans le constructeur de la classe qui l'implémente : $this->generateAccessor();
 * ------------------------------------------
 * Les méthodes get[nom de l'attribut] et set[nom de l'attribut] sont générées automatiquement
 * et peuvent être utilisées pour accéder aux attributs de la classe
 * ------------------------------------------
 * @info 
 *  - Réponds au principe de responsabilité unique
 *  - Réponds au design pattern Template method 
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
                    throw new BadMethodCallException ("Le type de l'attribut $propertyName n'est pas respecté. Type attendu : " . gettype($this->$propertyName) . " Type reçu : " . gettype($arguments[0]) );
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