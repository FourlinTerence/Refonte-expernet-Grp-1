<?php
/**
 * ==========================================================================
 * ÉMARGEMENT - EXPERNET CAMPUS
 * ==========================================================================
 * Fichier PHP appelé quand l'étudiant clique sur "Signer la présence du jour"
 * dans son dashboard.
 *
 * Flux :
 *   1. Vérifie que l'utilisateur est connecté (session active)
 *   2. Récupère l'id_inscription depuis la session
 *   3. Enregistre l'émargement du jour en BDD
 *   4. Redirige vers le dashboard étudiant
 * ==========================================================================
 */

session_start([
    'cookie_lifetime' => 3600,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

// -----------------------------------------------------------------------
// 1. Vérification de la session (sécurité : page réservée aux connectés)
// -----------------------------------------------------------------------
if (empty($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header("Location: ../../connexion.html?erreur=non_connecte");
    exit;
}

// Vérifie que le rôle est bien "étudiant"
if ($_SESSION['role'] !== 'etudiant') {
    header("Location: ../../dashboard-formateur.php");
    exit;
}

// Inclusion de la classe de connexion BDD
include('connexionBDD.php');

// -----------------------------------------------------------------------
// 2. Récupération de l'id_inscription depuis la session
//    Cet identifiant est chargé lors de la connexion de l'étudiant.
// -----------------------------------------------------------------------
$id_inscription = $_SESSION['id_inscription'] ?? null;

if (!$id_inscription) {
    header("Location: ../../dashboard-etudiant.php?erreur=pas_inscription");
    exit;
}

// -----------------------------------------------------------------------
// 3. Enregistrement de l'émargement du jour
// -----------------------------------------------------------------------
$date_jour = date('Y-m-d'); // Format MySQL : AAAA-MM-JJ

$resultat = $bdd->insertionEmargement($id_inscription, $date_jour);

// -----------------------------------------------------------------------
// 4. Redirection vers le dashboard avec confirmation
// -----------------------------------------------------------------------
header("Location: ../../dashboard-etudiant.php?succes=emargement");
exit;

?>
