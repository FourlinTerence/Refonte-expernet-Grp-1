<?php
/**
 * ==========================================================================
 * INSCRIPTION CANDIDAT - EXPERNET CAMPUS
 * ==========================================================================
 * Fichier PHP appelé par le formulaire d'inscription (inscription.html).
 * Il crée le compte utilisateur en 3 étapes successives dans la BDD :
 *
 *   Étape 1 : Insertion dans la table utilisateur
 *   Étape 2 : Insertion dans la table authentification (mot de passe hashé)
 *   Étape 3 : Attribution du rôle "étudiant" (id_role = 1) dans utilisateur_role
 *
 * Puis redirection vers la page de connexion.
 * ==========================================================================
 */

session_start([
    'cookie_lifetime' => 3600,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

// Inclusion de la classe de connexion BDD
include('connexionBDD.php');

// -----------------------------------------------------------------------
// 1. Vérification du jeton CSRF
// -----------------------------------------------------------------------
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    session_unset();
    session_destroy();
    header("Location: ../../inscription.html?erreur=csrf");
    exit;
}

// -----------------------------------------------------------------------
// 2. Récupération et nettoyage des données du formulaire d'inscription
// -----------------------------------------------------------------------
$nom        = trim($_POST['nom']        ?? '');
$prenom     = trim($_POST['prenom']     ?? '');
$email      = trim($_POST['email']      ?? '');
$telephone  = trim($_POST['telephone']  ?? '');
$ville      = trim($_POST['ville']      ?? '');
$territoire = $_POST['territoire']      ?? 'REUNION';
$handicape  = isset($_POST['handicape']) ? true : false; // Cochée = true
$password   = $_POST['password']        ?? '';
$confirm    = $_POST['password_confirm'] ?? '';

// -----------------------------------------------------------------------
// 3. Validations côté serveur (sécurité même si le JS a déjà vérifié)
// -----------------------------------------------------------------------

// Vérifie que les champs obligatoires ne sont pas vides
if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
    header("Location: ../../inscription.html?erreur=champs_vides");
    exit;
}

// Vérifie que l'email a un format valide
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../../inscription.html?erreur=email_invalide");
    exit;
}

// Vérifie que les deux mots de passe correspondent
if ($password !== $confirm) {
    header("Location: ../../inscription.html?erreur=mdp_diff");
    exit;
}

// Vérifie la longueur minimale du mot de passe (8 caractères)
if (strlen($password) < 8) {
    header("Location: ../../inscription.html?erreur=mdp_court");
    exit;
}

// Vérifie que le territoire est bien une valeur attendue
if (!in_array($territoire, ['REUNION', 'MAYOTTE'])) {
    $territoire = 'REUNION';
}

// -----------------------------------------------------------------------
// 4. Hachage du mot de passe avec l'algorithme bcrypt
//    password_hash() utilise PASSWORD_DEFAULT (bcrypt) pour un hash sécurisé.
//    JAMAIS stocker le mot de passe en clair dans la BDD.
// -----------------------------------------------------------------------
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// -----------------------------------------------------------------------
// 5. Étape 1 : Insertion dans la table utilisateur
//    Retourne l'ID auto-incrémenté du nouvel utilisateur.
// -----------------------------------------------------------------------
$id_utilisateur = $bdd->insertionUtilisateur(
    $nom,
    $prenom,
    $email,
    $telephone,
    $ville,
    $handicape,
    $territoire
);

// Vérifie que l'insertion a réussi (retourne un entier si OK)
if (!is_numeric($id_utilisateur)) {
    // L'email est peut-être déjà utilisé (contrainte UNIQUE sur email)
    header("Location: ../../inscription.html?erreur=email_deja_utilise");
    exit;
}

// -----------------------------------------------------------------------
// 6. Étape 2 : Insertion du mot de passe dans la table authentification
// -----------------------------------------------------------------------
$bdd->insertionAuthentification($id_utilisateur, $password_hash);

// -----------------------------------------------------------------------
// 7. Étape 3 : Attribution du rôle "étudiant" (id_role = 1)
//    Les valeurs de rôles doivent exister dans la table role :
//      1 = étudiant / 2 = formateur / 3 = admin
// -----------------------------------------------------------------------
$bdd->insertionUtilisateurRole($id_utilisateur, 1);

// -----------------------------------------------------------------------
// 8. Redirection vers la page de connexion avec message de succès
// -----------------------------------------------------------------------
header("Location: ../../connexion.html?succes=inscription");
exit;

?>
