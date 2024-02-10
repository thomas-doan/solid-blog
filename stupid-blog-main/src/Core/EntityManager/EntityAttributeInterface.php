<?php

namespace App\Core\EntityManager;

interface EntityAttributeInterface
{
    /**
     * Constructeur de l'entité attribut
     *
     * @param array $data Un tableau contenant les attributs de l'entité
     *                    [
     *                        'COLUMN_NAME' => string (Nom de la colonne),
     *                        'DATA_TYPE' => string (Type de la colonne),
     *                        'CHARACTER_MAXIMUM_LENGTH' => int (Taille de la colonne),
     *                        'COLUMN_DEFAULT' => string (Valeur par défaut de la colonne),
     *                        'IS_NULLABLE' => string (La colonne peut-elle être nulle, 1 = oui, 0 = non),
     *                        'COLUMN_KEY' => string (Type de clé, PRI = clé primaire, MUL = clé multiple),
     *                        'EXTRA' => string (Informations supplémentaires, ex : auto_increment)
     *                    ]
     */
    public function __construct(array $data);

    /**
     * Permet de récupérer la valeur d'un attribut
     *
     * @param string $name Le nom de l'attribut
     * @return mixed La valeur de l'attribut
     */
    public function __get($name);

    /**
     * Permet de vérifier si un attribut existe
     *
     * @param string $name Le nom de l'attribut
     * @return bool Vrai si l'attribut existe, faux sinon
     */
    public function __isset($name);

    /**
     * Permet de convertir l'entité attribut en chaîne de caractères
     *
     * @return string La chaîne de caractères représentant l'entité attribut
     */
    public function __toString();

    /**
     * Permet de convertir l'entité attribut en tableau
     *
     * @return array Le tableau représentant l'entité attribut
     */
    public function toArray();

    /**
     * Permet de convertir l'entité attribut en JSON
     *
     * @return string La chaîne de caractères JSON représentant l'entité attribut
     */
    public function toJson();

    /**
     * Permet de récupérer le nom de l'attribut
     *
     * @return string Le nom de l'attribut
     */
    public function getName(): string;
    
    /**
     * Permet de récupérer le type de l'attribut
     *
     * @return string Le type de l'attribut
     */
    public function getType(): string;

    /**
     * Permet de récupérer le type de l'attribut compatible avec PHP
     *
     * @return string Le type de l'attribut compatible avec PHP
     */
    public function getCompatibilityType(): string;

    /**
     * Permet de récupérer la taille maximale de l'attribut
     *
     * @return int La taille maximale de l'attribut
     */
    public function getMaxLength(): int;

    /**
     * Permet de savoir si l'attribut peut être nul
     *
     * @return bool Vrai si l'attribut peut être nul, faux sinon
     */
    public function isNullable(): bool;

    /**
     * Permet de savoir si l'attribut est une clé primaire
     *
     * @return bool Vrai si l'attribut est une clé primaire, faux sinon
     */
    public function isPrimary(): bool;

    /**
     * Permet de savoir si l'attribut est une clé étrangère
     *
     * @return bool Vrai si l'attribut est une clé étrangère, faux sinon
     */
    public function isAutoIncrement(): bool;

    /**
     * Permet de savoir si l'attribut est unique
     *
     * @return bool Vrai si l'attribut est unique, faux sinon
     */
    public function isUnique(): bool;

    /**
     * Permet de savoir si l'attribut est une clé multiple
     *
     * @return bool Vrai si l'attribut est une clé multiple, faux sinon
     */
    public function isMultiple(): bool;

    /**
     * Permet de savoir si l'attribut est une clé étrangère
     *
     * @return bool Vrai si l'attribut est une clé étrangère, faux sinon
     */
    public function isForeign(): bool;

    /**
     * Permet de savoir si l'attribut est par défaut
     *
     * @return bool Vrai si l'attribut est par défaut, faux sinon
     */
    public function isDefault(): bool;
    
}