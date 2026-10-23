<?php
/**
 * Tableau de bord formateur - ExperNet
 * Affiche les promotions suivies et leurs formations.
 */
require_once __DIR__.'/includes/header.php'; // charge session, init, csrf, navigation
// Vérifier l'authentification et le rôle (formateur ou admin)
if (empty($_SESSION['id_utilisateur']) || !in_array($_SESSION['role'], ['formateur', 'admin'])) {
    header('Location: connexion_form.php');
    exit;
}
// Récupérer les promotions du formateur (ou toutes si admin)
$promotions = $bdd->selectPromotions();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Formateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
<main class="flex-grow-1 container py-5">
    <h1 class="mb-4">Bienvenue, <?= htmlspecialchars($_SESSION['prenom']) ?></h1>
    <h2 class="mb-3">Promotions suivies</h2>
    <?php if (empty($promotions)): ?>
        <p>Aucune promotion à afficher.</p>
    <?php else: ?>
        <div class="row g-4">
        <?php foreach ($promotions as $promo): ?>
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Promotion ID <?= (int)$promo['id_promotion'] ?></h5>
                        <p class="card-text mb-1"><strong>Formation :</strong> <?= htmlspecialchars($promo['formation_intitule'] ?? $promo['intitule'] ?? '') ?></p>
                        <p class="card-text mb-1"><strong>Campus :</strong> <?= htmlspecialchars($promo['campus_nom'] ?? '') ?></p>
                        <p class="card-text mb-1"><strong>Année :</strong> <?= htmlspecialchars($promo['annee'] ?? '') ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <p class="mt-4"><a href="php/BDD/deconnexion.php" class="btn btn-outline-danger">Déconnexion</a></p>
</main>
<?php include __DIR__.'/includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
