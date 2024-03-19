# solid-blog

Ce projet à pour but de refactoriser le code afin d'améliorer sa lisibilité et scalabilité.
Afin de amélioré ces différents axes, le code va être facotriser selon les principes SOLID.

Afin de facilité la relecture rechercher les mot clef suivants :

- Single Responsibility Principle

- Open-Closed Principle

- Liskov Substitution Principle

- Interface Segregation Principle

- Dependency Inversion Principle

 ---

## Prochaine étape

 Appliquer les design pattern suivant :

 ---

### pattern Template method

Présent à travers `accessorGenerator`. Toutes les classe qui necessitent  des accesseurs (getters, setters). Peuvent utiliser le trait en question pour les initiés automatiquement.
Cette approche permet de ne pas dupliquer du code inutilement tout en permettant de simplifier la lecture de ce dernerier.

#### fonctionnement 

Génère les accesseurs magiques pour les propriétés publiques
S'apparente a une méthode virtuelle en C++ ou Java
- **method __call** (string $methodName, array $arguments) : mixed
 valide l'existance des méthodes get et set pour chaque attribut de la classe
- **method generateAccessor()** : void 
 genère les accesseurs pour chaque attribut de la classe
- **method generateGetter**(string $propertyName, string $capitalizedPropertyName) : void 
genère les getters pour chaque attribut de la classe
- **generateSetter**(string $propertyName, string $capitalizedPropertyName) : void
 genère les setters pour chaque attribut de la classe

#### Usage 

Cet traite doit être appellé dans la classe qui l'implémente
 - use AccessorGenerator;
 - Dans le constructeur de la classe qui l'implémente : $this->generateAccessor();

Les méthodes get[nom de l'attribut] et set[nom de l'attribut] sont générées automatiquement
et peuvent être utilisées pour accéder aux attributs de la classe

 - Réponds au principe de responsabilité unique
 - Réponds au design pattern Template method 

---
## Usage de Façade

### Façade DTOManager

La façade est responsable de traiter les DTO en s'assurant que toute structure de DTO dépendant d'une même interface puisse instruire le reste du service sur les comportements attendus. Cela permet une gestion uniforme des différents types de DTO.

### Façade EntityManager | Création de Domain Language Model (DLM)

#### Domain Language Model (DLM) 

Le Domain Language Model (DLM) est une solution inspirée de la librairie queryString, bien qu'elle soit indépendante de celle-ci. Son objectif est de permettre une structuration harmonieuse des requêtes à envoyer dans un environnement PHP. Bien qu'elle ne vise pas à être aussi exhaustive, elle offre une manière rapide et efficace de structurer une variable PHP en un élément simple pouvant être converti en requête complexe.

---
## Paradygme DTO, Programmation Orientée Aspect (AOP)

### DTOManager
Le DTOManager est chargé de gérer les DTOs entrants. Il analyse la structure de chaque DTO et vérifie sa relation avec l'entité correspondante en base de données. Il construit des pilotes pour chaque attribut du DTO, où un memento est associé à l'avancement de l'état de l'attribut, permettant de conserver son état.

#### Approche est paradygme

 - **Programmation Orientée Aspect (AOP)** : Les décorateurs permettent de séparer les préoccupations (concerns) dans le code en introduisant des aspects transversaux tels que **la journalisation**, **la gestion des erreurs**, **la validation des données**, etc. Cela permet de maintenir un code plus modulaire et plus propre.

 ***Ajout possible** : Un iterateur permettrais une meilleur approche sur l'execution de DTOManager en permetant de construire un parcours de validation qui permetrais une structure plus large de l'usage de l'AOP au sein du système.*

 - **Modèle de Conception Décorateur** : Il s'agit d'un design pattern structurel qui permet d'ajouter des fonctionnalités à des objets de manière dynamique.

---

## Usage des décorateur 

Les DTO utilisent des décorateurs pour introduire des aspects transversaux tels que la journalisation, la gestion des erreurs, la validation des données, etc. Cela permet de maintenir un code plus modulaire et plus propre en séparant les préoccupations (concerns). Cette approche s'inspire de la Programmation Orientée Aspect (AOP), où les décorateurs jouent un rôle crucial dans la séparation des différentes préoccupations dans le code.

---
## Usage de builder

###  QueryBuilder

Le QueryBuilder simplifie la syntaxe pour la gestion des requêtes, en permettant notamment la gestion des relations sous forme d'objets à retourner. Contrairement à une approche classique avec Doctrine, notre service ne gère pas directement la base de données, mais la structure de la base de données définit le comportement du service.

### QueryDirector

Le queryDirector quant à lui permet de définir l'ordre d'usage du querybBuilder qui ne comrpend que la partie métier

---
## Repository Pattern

L'enetityManager se caractèrise par sa fonction et sa structure par une approchre Repository.

Ce pendant en l'état actuel sa responsabilité comme tel auraigt nécessité une distinction entre l'usage de la Façade, de Repositoy et de la traduction du DLM. L'approche futur aurait donc pousser à diviser a des classe distinct chacune de ces fonction.

---
## Factory

Dans une approche de scalabilité le builder est pensée pour pouvoir intégrée un factory.

