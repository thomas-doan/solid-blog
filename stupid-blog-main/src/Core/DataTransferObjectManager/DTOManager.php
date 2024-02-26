<?php
//DTOManager est une classe qui permet de gérer les objets de transfert de données
namespace App\Core\DataTransferObjectManager;

use App\Class\Database;
use App\Core\Database\Database as DatabaseDatabase;
use App\Core\EntityManager\EntityManager;
use App\Core\EntityManager\EntityValidator;
use App\DevTools\EchoDebug;

use function PHPSTORM_META\map;

/**
 * Class DTOManager : permet de gérer les objets de transfert de données
 * Comment fonctionne cette classe ?
 * 1. Elle verifie que l'object reçus dans el constructu est bien une instance de DTOInterface
 * 2. Elle parcours alors ces propriétés et les décorateurs associés
 * 3. Elle traduit se décorateur par la méthode associées
 * @package App\Core\DataTransferObjectManager
 * @version 1.0
 */
class DTOManager
{
    /** @var array $pilote : tableau qui contiendra les propriétés et les méthodes associées */
    public $pilote= [];
    public $dataToTransfer = [];
    private $entity;

    public function __construct(EntityManager $entity)
    {
        $this->entity = $entity;
    }

    public function __process(DTOInterface $entity) {
        $reflector = new \ReflectionClass($entity);
        $ThisReflector = new \ReflectionClass($this);

        $hownMethods = $ThisReflector->getMethods();
        $properties = $reflector->getProperties();
        $matches = [];

        foreach ($hownMethods as $method) {
            //on ne récupère par les méthodes interne qui commence par __
            if (substr($method->name, 0, 2) != '__') {
                $matches[] = $method->name;
            }
        }
        // pour chaque propriété de l'entité, on les prépare pour rentrée dans data
        foreach ($properties as $property) {
            // on récupère le nom de la propriété
            $name = $property->getName();
            $value = $property->getValue($entity);
            $docComment = $property->getDocComment();

            $annotations = $this->__parseAnnotations($docComment,$matches);
            $this->pilote[$name]["field"] = $name;
            $this->pilote[$name]["origineValue"] = $value;
            $this->pilote[$name]["value"] = $value;
            foreach ($annotations as $key => $value) {
                $annotations[$key] = null;
            }
            $this->pilote[$name]["methodes"] = $annotations;

            $lignesDocComment = explode("\n", $docComment);
            foreach ($lignesDocComment as $ligne) {
                if(strpos($ligne, '@') !== false && strpos($ligne, ':') !== false ){

                    [$attribute, $contrainte] = explode(":", $ligne);

                    $attribute = str_replace(['@', '*', ' '], '', $attribute);
                    $contrainte = str_replace(' ', '', $contrainte);

                    if(array_key_exists($attribute, $this->pilote[$name]["methodes"])) {                           
                        $this->pilote[$name]["methodes"][$attribute] = $this->__sanityzeDecorator($contrainte);
                    }

                } 
            }

            EntityValidator::findField($this->entity->getFields(),$name);
        }

        echo "Pilote : ";
        echo '<pre>';
        print_r($this->pilote);
        echo '</pre>';

        //un fois que le pilotes est prêt, on appllique chaque fonction pour chaque propriété
        foreach ($this->pilote as $property => $element) {
            foreach ($element["methodes"] as $method => $value) {
                if($value !== null){
                    $this->$method($element, $value);
                } else $this->$method($element);
            }
        }
    }

    private function __sanityzeDecorator($contrainte){

        if(is_numeric($contrainte)) {
            return (int) $contrainte;
        } elseif (strpos($contrainte, '/') !== false) {

            echo "contrainte regex :". $contrainte;
            //si il contient "/" alors c'est une regex on le traduit en regex
            return $contrainte;
        }
        //si il contient "[" et "]" alors c'est un tableau
        elseif(strpos($contrainte, '[') !== false && strpos($contrainte, ']') !== false) {
            //on verifie combien de fois il y a le caractère "[" pour déterminer le nombre de dimension du tableau
            $dimentionnalNumber = substr_count($contrainte, '[');
            $arrayStuctural = [];

            if($dimentionnalNumber > 1) {
                //il faut faire attention au array multidimensionnel
                $dimentionnalArray = explode('[', $contrainte);
                //on supprime le premier index qui est vide
                array_shift($dimentionnalArray);

                //on enleve seulement le dernier ] du dernier index
                $posLastBracket = strrpos($dimentionnalArray[count($dimentionnalArray) - 1], ']');
                $dimentionnalArray[count($dimentionnalArray) - 1] = substr($dimentionnalArray[count($dimentionnalArray) - 1], 0, $posLastBracket);
                
                $arrayStuctural =  explode(',', str_replace(['[', ']'], '', $dimentionnalArray[0]));

                array_shift($dimentionnalArray);
                //on remonte le tableau dimentionnalArray en string complet pour exploité la récursivité
                $dimentionnaleArrayString = '';
                foreach ($dimentionnalArray as $key => $value) {
                    $dimentionnaleArrayString .= "[".$value;
                }
                //on rajoute au dernier index vide le string complet
                $arrayStuctural[count($arrayStuctural) - 1] = $dimentionnaleArrayString;
            } else {
                $arrayStuctural =  explode(',', str_replace(['[', ']'], '', $contrainte));
            }
                
                $newArray = [];
                //on verfie si c'est un tableau de clé valeur
                foreach ($arrayStuctural as $key => $value) {
                    //si la value contient encore un on s'anityse se tableau
                    if(strpos($value, '[') !== false && strpos($value, ']') !== false) {
                        $newArray[] = $this->__sanityzeDecorator($value);
                    } elseif(strpos($value, '=>') !== false) {
                        [$newKey, $newValue] = explode('=>', $value);
                        $newArray[$newKey] = $this->__sanityzeDecorator($newValue);
                    } else {
                        //$newArray[] = $value;
                        $newArray[] = $this->__sanityzeDecorator($value);
                    }
                }
                return  $newArray;
        } else {
            //si la valeur est comprise entre ' ou " alors on le transforme en string et on enleve les quotes
            if(strpos($contrainte, "'") !== false || strpos($contrainte, '"') !== false) {
                return  str_replace(['"', "'"], '', $contrainte);
            } else {
                return  $contrainte;
            }
        }
    }

    private function __parseAnnotations($docComment,$matchesDecorator) {
        $annotations = array();

        preg_match_all('/@(\w+)/', $docComment, $matches);
        foreach ($matches[1] as $annotation) {
            //on ne récupère que les annotations qui sont dans notre tableau de matches
            if (!in_array($annotation, $matchesDecorator)) {
                continue;
            } 
            $annotations[$annotation] = true;
        }
        return $annotations;
    }

    private function __setHistoryMethod($attribute, $methodName = null, $value = null) {
        $this->dataToTransfer[$methodName] = $value;
        $this->pilote[$attribute]["history"] = [
            "method" => $methodName,
            "value" => $value]
        ;
    }

    private function __getHistoryMethod($attribute) {
        return $this->pilote[$attribute]["history"];
    }


    private function isUnique($piloteProperty) {
        $response = $this->entity->isUniqueField($piloteProperty["field"], $piloteProperty["value"]);
        if($response === false) throw new \Exception("La valeur de ".$piloteProperty["field"]." n'est pas unique"); 
    }

    private function isMail($piloteProperty) {
        // Vérifier que la valeur est un mail
        $response = filter_var($piloteProperty["value"], FILTER_VALIDATE_EMAIL);
        if($response === false) throw new \Exception("La valeur de ".$piloteProperty["field"]." n'est pas un mail");
    }

    private function isPassword($piloteProperty) {
        // Vérifier que la valeur est un mot de passe
        $response = preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}/', $piloteProperty["value"]);
        $this->__setHistoryMethod($piloteProperty["field"], "isPassword", $piloteProperty["value"]);
        if($response === false) throw new \Exception("La valeur de ".$piloteProperty["field"]." est trop faible");
    }

    private function minLength($piloteProperty, $contrainte) {
        $result = strlen($piloteProperty["value"]) >= $contrainte;
        if($result === false) throw new \Exception("La valeur de ".$piloteProperty["field"]." est inférieur à la longueur minimale de ".$contrainte);
    }

    private function maxLength($piloteProperty, $contrainte) {
        $result = strlen($piloteProperty["value"]) <= $contrainte;
        if($result === false) throw new \Exception("La valeur de ".$piloteProperty["field"]." est supérieur à la longueur maximale de ".$contrainte);
    }

    private function defineRegex($piloteProperty, $contraintes) {
    $contraintes = trim($contraintes);
    if ($contraintes[0] !== '/' || substr($contraintes, -1) !== '/') {
        throw new \Exception("Syntax Error : Decorator  defineRegex dans le champs".$piloteProperty["field"]." ne correspond pas a une regex");
    }  

    $result = preg_match($contraintes, $piloteProperty["value"]);

    if ($result === 0) {
        throw new \Exception("La valeur de la propriété".$piloteProperty["field"]." ne correspond pas à l'expression régulière attendue");
    }
    }

    private function type($piloteProperty, $contrainte) {
        $typeOfValue = gettype($piloteProperty["value"]);
        //on verifie cependant si la piloteProperty n'a pas une fonction default qui permet de définir une valeur par défaut
        if(!array_key_exists("default", $piloteProperty["methodes"]) && $typeOfValue != $contrainte){
            throw new \Exception("Le champ " . $piloteProperty["field"] . " doit être de type " . $contrainte.". Type reçu : " . $typeOfValue);
        }
    }

    private function default($piloteProperty, $contrainte) {
        if($piloteProperty["value"] === null) {
            $piloteProperty["value"] = $contrainte;
            $this ->__setHistoryMethod($piloteProperty["field"], "default", $contrainte);
            $this->dataToTransfer[$piloteProperty["field"]] = $contrainte;
        }
    }

    private function tableau($piloteProperty) {
        // Vérifier que la valeur est dans un tableau
        echo "Rune methode : tableau";
    }

    private function tableauKeyValue($piloteProperty) {
        // Vérifier que la valeur est dans un tableau avec des clés
        echo "Rune methode : tableauKeyValue";
    }

}
