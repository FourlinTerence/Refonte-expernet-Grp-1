<?php
/**
 * Initialise la connexion PDO et expose l'objet $bdd.
 * Utilise les constantes définies dans config.php.
 */
require_once __DIR__.'/config.php';
require_once __DIR__.'/connexionBDD.php';

// Instanciation de la classe de connexion
$bdd = new ConnexionBDD(DB_NAME, DB_PASS, DB_USER, DB_HOST);
?>