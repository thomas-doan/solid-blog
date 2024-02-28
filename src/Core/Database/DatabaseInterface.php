<?php
/** Définit l'interface pour la base de données */
namespace App\Core\Database;

/**
 * Permet d'ouvrir une transaction
 * et de préparer une requête SQL
 */
interface DatabaseInterface
{
    /**
     * Permet de faire une requête SQL
     * @param $statement string Requête SQL
     * @param $attributes array Tableau des attributs
     * @param $one bool Retourne un seul résultat
     */
    public function query($statement, $attributes = null, $one = false);

    /**
     * Constructeur de la classe
     * Connexion à la base de données
     */
    public function __construct();
}

