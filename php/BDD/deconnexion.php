<?php
/**
 * ==========================================================================
 * DÉCONNEXION - EXPERNET CAMPUS
 * ==========================================================================
 * Fichier PHP appelé lorsque l'utilisateur clique sur "Déconnexion".
 * Il détruit proprement la session en cours puis redirige vers la connexion.
 *
 * Inspiré de sessionKill.php du projet BlogCookery_Island.
 * ==========================================================================
 */

session_start();

// Supprime toutes les variables de session stockées
session_unset();

// Détruit la session côté serveur
session_destroy();

// Redirige vers la page de connexion
header("Location: ../../connexion.html?succes=deconnexion");
exit;

?>
