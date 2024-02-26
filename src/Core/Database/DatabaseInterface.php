<?php
/** Définit l'interface pour la base de données */
namespace App\Core\Database;

interface DatabaseInterface
{
    public function query($statement, $attributes = null, $one = false);
    public function __construct();
}

