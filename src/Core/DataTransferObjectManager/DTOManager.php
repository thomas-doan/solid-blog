<?php
//DTOManager est une classe qui permet de gérer les objets de transfert de données
namespace App\Core\DataTransferObjectManager;

use App\Class\Database;
use App\Core\Database\Database as DatabaseDatabase;
use App\Core\Database\DatabaseManager;
use App\Core\EntityManager\EntityManager;
use App\Core\EntityManager\EntityValidator;
use App\DevTools\EchoDebug;


// Dependency Inversion Principle
/**
 * Class DTOManager : permet de gérer les objets de transfert de données
 * Comment fonctionne cette classe ?
 * 1. Elle verifie que l'object reçus dans el constructu est bien une instance de DTOInterface
 * 2. Elle parcours alors ces propriétés et les décorateurs associés
 * 3. Elle traduit se décorateur par la méthode associées
 * 
 * ### Approche est paradygme
 * **Programmation Orientée Aspect (AOP)** : Les décorateurs permettent de séparer les préoccupations (concerns) dans le code en introduisant des aspects transversaux tels que la journalisation, la gestion des erreurs, la validation des données, etc. Cela permet de maintenir un code plus modulaire et plus propre.
 * 
 * **Modèle de Conception Décorateur** : Il s'agit d'un design pattern structurel qui permet d'ajouter des fonctionnalités à des objets de manière dynamique.
 */
class DTOManager
{
    /** @var DTOPilote[] */
    public $pilote= [];
    public $dataToTransfer = [];
    public EntityManager $entity;
    public DTODecorator $format;

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
            $currentPilote = new DTOPilote($name, $value);
            $this->pilote[$name] = $currentPilote;

            foreach ($annotations as $key => $value) {
                $annotations[$key] = null;
            }
            $this->pilote[$name]->methodes = $annotations;

            $lignesDocComment = explode("\n", $docComment);
            foreach ($lignesDocComment as $ligne) {
                if(strpos($ligne, '@') !== false && strpos($ligne, ':') !== false ){

                    [$attribute, $contrainte] = explode(":", $ligne);

                    $attribute = str_replace(['@', '*', ' '], '', $attribute);
                    $contrainte = str_replace(' ', '', $contrainte);

                    if(array_key_exists($attribute, $this->pilote[$name]->getMethodes())) {                           

                        $this->pilote[$name]->addMethode($attribute, $this->__sanityzeDecorator($contrainte));
                    }

                } 
            }

            EntityValidator::findField($this->entity->getFields(),$name);
        }


        //un fois que le pilotes est prêt, on appllique chaque fonction pour chaque propriété
        foreach ($this->pilote as $property => $element) {
            foreach ($element->getMethodes() as $method => $value) {
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
        $this->pilote[$attribute]->addHistory($methodName, $value);
    }

    private function __getHistoryMethod($attribute) {
        return $this->pilote[$attribute]->getHistorys();
    }


    /**
     * --- 
     * ### DECORATEUR
     * - **@isUnique** : Défini une valeur comme étant unique
     * --- 
     * ### Comportement
     * Oblige l'usage d'une valeur unique
     */
    private function isUnique($piloteProperty) {
        $response = $this->entity->isUniqueField($piloteProperty->getFieldName(),$piloteProperty->getValue());
        if($response === false) throw new \Exception("La valeur de ".$piloteProperty->getFieldName()." n'est pas unique"); 
    }

    /**
     * ---
     * ### DECORATEUR
     * - **@isMail** : Défini une valeur comme étant un mail
     * ---
     * ### Comportement
     * Oblige l'usage d'un mail
     */
    private function isMail($piloteProperty) {
        // Vérifier que la valeur est un mail
        $response = filter_var($piloteProperty->getValue(), FILTER_VALIDATE_EMAIL);
        if($response === false) throw new \Exception("La valeur de ".$piloteProperty->getFieldName()." n'est pas un mail");
    }

    /**
     * ---
     * ### DECORATEUR
     * - **@isPassword** : Défini une valeur comme étant un mot de passe
     * ---
     * ### Comportement
     * Oblige l'usage d'un password selon les recommandation de la CNIL : 
     * - 8 caractères minimum
     * - 1 majuscule
     * - 1 minuscule
     * - 1 chiffre
     * - 1 caractère spécial
     * ### Transformation
     * La valeur est hashé en sha256
     */
    private function isPassword($piloteProperty) {
        // Vérifier que la valeur est un mot de passe
        $response = preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}/', $piloteProperty->getValue());
        $this->__setHistoryMethod($piloteProperty->getFieldName(), "isPassword", $piloteProperty->getValue());
        if($response === false) throw new \Exception("La valeur de ".$piloteProperty->getFieldName()." est trop faible");
        //dans ce cas on hash le password directement
        $piloteProperty->setValue(hash('sha256',$piloteProperty->getValue()));
    }

    /**
     * ---
     * ### DECORATEUR
     * - **@minLength** : Défini une longueur minimale pour la valeur de la propriété
     * ---
     * ### Utilisation
     * ```php
     * <?php
     * /**
     * * @minLength : 8
     * * /
     * public string $password;
     * ```
     */
    private function minLength($piloteProperty, $contrainte) {
        $result = strlen($piloteProperty->getValue()) >= $contrainte;
        $this->__setHistoryMethod($piloteProperty->getFieldName(), "minLength", $contrainte);
        if($result === false) throw new \Exception("La valeur de ".$piloteProperty->getFieldName()." est inférieur à la longueur minimale de ".$contrainte);
    }

    /**
     * ---
     * ### DECORATEUR
     * - **@maxLength** : Défini une longueur maximale pour la valeur de la propriété
     * ---
     * ### Utilisation
     * ```php
     * /**
     * * @maxLength : 255
     * * /
     * public string $name;
     * ```
     * ---
     */
    private function maxLength($piloteProperty, $contrainte) {
        $result = strlen($piloteProperty->getValue()) <= $contrainte;
        $this->__setHistoryMethod($piloteProperty->getFieldName(), "maxLength", $contrainte);
        if($result === false) throw new \Exception("La valeur de ".$piloteProperty->getFieldName()." est supérieur à la longueur maximale de ".$contrainte);
    }

    /**
     * ---
     * ### DECORATEUR
     * - **@defineRegex** : Défini une regex pour la valeur de la propriété
     * ---
     * ### Utilisation
     * ```php
     * /**
     * * @defineRegex : /[^a-zA-Z0-9]/
     * * /
     * public string $name;
     * ```
     * ---
     */
    private function defineRegex($piloteProperty, $contraintes) {
        $contraintes = trim($contraintes);
        if ($contraintes[0] !== '/' || substr($contraintes, -1) !== '/') {
            throw new \Exception("Syntax Error : Decorator  defineRegex dans le champs".$piloteProperty->getFieldName()." ne correspond pas a une regex");
        }  

        $result = preg_match($contraintes, $piloteProperty->getValue());

        $this->__setHistoryMethod($piloteProperty->getFieldName(), "defineRegex", $contraintes);
        if ($result === 0) {
            throw new \Exception("La valeur de la propriété".$piloteProperty->getFieldName()." ne correspond pas à l'expression régulière attendue");
        }
    }

    /**
     * ---
     * ### DECORATEUR
     *  - **@updatedTime** : Met a jour automatiquement la valeur de la propriété en DateTime
     * ---
     */
    private function updatedTime(DTOPilote $piloteProperty) {
        $dateNow = new \DateTime();
        $piloteProperty->setValue($dateNow->format('Y-m-d H:i:s'));
        $this->__setHistoryMethod($piloteProperty->getFieldName(), "updatedTime", $dateNow->format('Y-m-d H:i:s'));            
    }

    /**
     * ---
     * ### DECORATEUR
     *  - **@createdTime** : Met a jour automatiquement la valeur de la propriété en DateTime
     * ---
     */
    private function createdTime($piloteProperty) {
        if($piloteProperty->getValue() === null) {
            $dateNow = new \DateTime();
            $piloteProperty->setValue($dateNow->format('Y-m-d H:i:s'));
            $this->__setHistoryMethod($piloteProperty->getFieldName(), "createdTime", $dateNow->format('Y-m-d H:i:s'));
        }
    }

    /**
     * ---
     * ### DECORATEUR
     *  - **@type** : Défini le type de la valeur de la propriété
     * ---
     */
    private function type($piloteProperty, $contrainte) {
        $typeOfValue = gettype($piloteProperty->getValue());
        //on verifie cependant si la piloteProperty n'a pas une fonction default qui permet de définir une valeur par défaut
        if(!array_key_exists("default", $piloteProperty["methodes"]) && $typeOfValue != $contrainte){
            throw new \Exception("Le champ " . $piloteProperty->getFieldName() . " doit être de type " . $contrainte.". Type reçu : " . $typeOfValue);
        }
    }

    /**
     * ---
     * ### DECORATEUR
     *  - **@default** : Défini une valeur par défaut pour la propriété
     * ---
     * ### Utilisation
     * ```php
     * /**
     * * @default : 18
     * * /
     * public int $age;
     */
    private function default($piloteProperty, $contrainte) {
        if($piloteProperty->getValue() === null) {
            $piloteProperty->setValue($contrainte);
            $this ->__setHistoryMethod($piloteProperty->getFieldName(), "default", $contrainte);
            $this->dataToTransfer[$piloteProperty->getFieldName()] = $contrainte;
        }
    }

    /**
     * ---
     * ### DECORATEUR
     * - **@primaryKey** : Défini la propriété comme étant la clé primaire de l'entité
     * ---
     */
    private function primaryKey($piloteProperty) {
        $this->entity->setPrimaryKey($piloteProperty->getFieldName());
    }

    /**
     * ---
     * ### DECORATEUR
     * - **@foreignKey** : Défini la propriété comme étant une clé étrangère de l'entité
     * ---
     * ### Utilisation
     * ```php
     * /**
     * * @foreignKey : User
     * * /
     * public int $user_id;
     */
    private function foreignKey($piloteProperty, $contrainte) {
        //EchoDebug::xDebug($piloteProperty->getFieldName(), $contrainte);
        $this->entity->setForeignKey($piloteProperty->getFieldName(), $contrainte);

        //on s'assure alors que la valeur d'entrée est bien de type DTOEntity
        if(!is_a($piloteProperty->getValue(), "App\Core\DataTransferObjectManager\DTOInterface")) {
            throw new \Exception("La valeur de ".$piloteProperty->getFieldName()." n'est pas une instance de DTOEntity");
        }
        //si la valeur est bien une instance de DTOEntity alors on process le DTOEntity
        

        echo $contrainte;
        if($piloteProperty->getValue() !== null) {

            $this->entity->setForeignKeyValue($piloteProperty->getFieldName(), $piloteProperty->getValue()->getId());
           
        }


    }
}
