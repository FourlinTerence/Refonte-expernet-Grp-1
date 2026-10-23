<?php
/**
 * Formulaire de connexion pour ExperNet.
 * Le formulaire génère un token CSRF et le transmet via un champ caché.
 * Les données sont POSTées vers php/BDD/connexion.php.
 */
require_once __DIR__.'/csrf.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - ExperNet</title>
</head>
<body>
<h1>Se connecter</h1>
<form action="php/BDD/connexion.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <label>Email : <input type="email" name="email" required></label><br>
    <label>Mot de passe : <input type="password" name="password" required></label><br>
    <button type="submit">Connexion</button>
</form>
<p><a href="inscription_form.php">Pas encore de compte ? S'inscrire</a></p>
</body>
</html>
