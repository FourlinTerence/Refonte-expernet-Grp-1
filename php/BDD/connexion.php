<?php
/**
 * ==========================================================================
 * CONNEXION UTILISATEUR - EXPERNET CAMPUS
 * ==========================================================================
 * Fichier PHP appelé par le formulaire de connexion (connexion.html).
 * Il vérifie l'email, le mot de passe et crée la session utilisateur.
 *
 * Flux :
 *   1. Démarrage de la session
 *   2. Vérification du jeton CSRF (sécurité anti-falsification de requête)
 *   3. Récupération de l'utilisateur en BDD par son email
 *   4. Vérification du mot de passe via password_verify()
 *   5. Vérification que le compte est actif
 *   6. Lecture du rôle (étudiant / formateur / admin)
 *   7. Stockage des données en session et redirection vers le bon dashboard
 * ==========================================================================
 */

session_start([
    'cookie_lifetime' => 3600,    // La session reste active 1 heure
    'cookie_httponly' => true,    // Protection XSS : cookie inaccessible en JS
    'cookie_samesite' => 'Strict' // Protection CSRF sur les cookies de session
]);

// Inclusion de la classe de connexion BDD
include('connexionBDD.php');

// -----------------------------------------------------------------------
// 1. Vérification du jeton CSRF
//    Le formulaire doit inclure un champ caché csrf_token correspondant
//    à la valeur stockée en session lors du chargement de la page.
// -----------------------------------------------------------------------
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    session_unset();
    session_destroy();
    header("Location: ../../connexion.html?erreur=csrf");
    exit;
}

// -----------------------------------------------------------------------
// 2. Récupération et nettoyage des données du formulaire
// -----------------------------------------------------------------------
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validation basique : les champs ne doivent pas être vides
if (empty($email) || empty($password)) {
    header("Location: ../../connexion.html?erreur=champs_vides");
    exit;
}

// -----------------------------------------------------------------------
// 3. Recherche de l'utilisateur en base de données via son email
// -----------------------------------------------------------------------
$utilisateur = $bdd->selectUtilisateurParEmail($email);

// Si aucun utilisateur trouvé avec cet email
if (empty($utilisateur)) {
    header("Location: ../../connexion.html?erreur=identifiants_invalides");
    exit;
}

$utilisateur = $utilisateur[0]; // On prend le premier (et unique) résultat

// -----------------------------------------------------------------------
// 4. Vérification que le compte est actif
// -----------------------------------------------------------------------
if (!$utilisateur['actif']) {
    header("Location: ../../connexion.html?erreur=compte_inactif");
    exit;
}

// -----------------------------------------------------------------------
// 5. Vérification du mot de passe
//    password_verify() compare le mot de passe saisi avec le hash stocké
//    dans la table authentification (séparée de la table utilisateur).
// -----------------------------------------------------------------------
$auth = $bdd->selectAuthentification($utilisateur['id_utilisateur']);

if (empty($auth) || !password_verify($password, $auth[0]['password_hash'])) {
    header("Location: ../../connexion.html?erreur=identifiants_invalides");
    exit;
}

// -----------------------------------------------------------------------
// 6. Récupération du ou des rôles de l'utilisateur
//    Stocke le premier rôle trouvé (étudiant, formateur, admin...)
// -----------------------------------------------------------------------
$roles = $bdd->selectRolesUtilisateur($utilisateur['id_utilisateur']);
$role  = !empty($roles) ? strtolower($roles[0]['libelle']) : 'etudiant';

// -----------------------------------------------------------------------
// 7. Création de la session utilisateur
//    Uniquement les données nécessaires pour l'affichage et le routage.
// -----------------------------------------------------------------------
$_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
$_SESSION['nom']            = $utilisateur['nom'];
$_SESSION['prenom']         = $utilisateur['prenom'];
$_SESSION['email']          = $utilisateur['email'];
$_SESSION['role']           = $role;
$_SESSION['connecte']       = true;

// -----------------------------------------------------------------------
// 8. Redirection vers le tableau de bord correspondant au rôle
// -----------------------------------------------------------------------
if ($role === 'formateur' || $role === 'admin') {
    header("Location: ../../dashboard-formateur.php");
} else {
    header("Location: ../../dashboard-etudiant.php");
}
exit;

?>
