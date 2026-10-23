<?php
/**
 * Formulaire d'inscription pour ExperNet.
 * Ce formulaire génère un token CSRF, le stocke en session puis le transmet
 * via un champ caché. Les données sont envoyées en POST à php/BDD/inscription.php
 */
require_once __DIR__.'/csrf.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - ExperNet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
<main class="flex-grow-1 container py-5">
    <h1 class="mb-4">Créer un compte</h1>
    <form action="php/BDD/inscription.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Prénom</label>
            <input type="text" name="prenom" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="text" name="telephone" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Ville</label>
            <input type="text" name="ville" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Territoire</label>
            <select name="territoire" class="form-select">
                <option value="REUNION">Réunion</option>
                <option value="MAYOTTE">Mayotte</option>
            </select>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="handicape" id="handicapeCheck" value="1">
            <label class="form-check-label" for="handicapeCheck">Handicap</label>
        </div>
        <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirmer le mot de passe</label>
            <input type="password" name="password_confirm" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
    <p class="mt-3"><a href="connexion_form.php">Déjà inscrit ? Se connecter</a></p>
</main>
<?php include __DIR__.'/includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
