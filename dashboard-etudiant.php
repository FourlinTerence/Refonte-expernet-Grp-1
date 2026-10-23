<?php
/**
 * Tableau de bord étudiant - ExperNet
 * Affiche les formations disponibles et propose le bouton d'émargement du jour.
 */
require_once __DIR__.'/includes/header.php'; // charge session, init, csrf, navigation
// Vérifier l'authentification et le rôle
if (empty($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'etudiant') {
    header('Location: connexion_form.php');
    exit;
}
// Récupérer les formations
$formations = $bdd->selectFormations();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
<main class="flex-grow-1 container py-5">
    <h1 class="mb-4">Bienvenue, <?= htmlspecialchars($_SESSION['prenom']) ?></h1>
    <h2 class="mb-3">Formations disponibles</h2>
    <?php if (empty($formations)): ?>
        <p>Aucune formation disponible.</p>
    <?php else: ?>
        <ul class="list-group">
        <?php foreach ($formations as $formation): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= htmlspecialchars($formation['intitule'] ?? $formation['nom_formation']) ?></strong>
                    - Niveau <?= htmlspecialchars($formation['niveau_rncp']) ?> (<?= htmlspecialchars($formation['nom_filiere']) ?>)
                </div>
                <form action="php/BDD/emargement.php" method="POST" class="m-0">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="id_formation" value="<?= (int)$formation['id_formation'] ?>">
                    <button type="submit" class="btn btn-sm btn-success">Présence du jour</button>
                </form>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <p class="mt-4"><a href="php/BDD/deconnexion.php" class="btn btn-outline-danger">Déconnexion</a></p>
</main>
<?php include __DIR__.'/includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
