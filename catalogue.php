<?php
/**
 * Page catalogue – liste dynamique des formations
 */
require_once __DIR__.'/php/BDD/init.php';
require_once __DIR__.'/includes/header.php';

// Récupérer toutes les formations avec leur filière
$formations = $bdd->selectFormations();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue – ExperNet Campus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
<main class="flex-grow-1 container py-5">
    <h1 class="mb-4">Catalogue des formations</h1>
    <?php if (empty($formations)): ?>
        <p>Aucune formation disponible pour le moment.</p>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($formations as $formation): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($formation['intitule'] ?? $formation['nom_formation']) ?></h5>
                            <p class="card-text mb-1"><strong>Filière :</strong> <?= htmlspecialchars($formation['nom_filiere']) ?></p>
                            <p class="card-text mb-1"><strong>Niveau RNCP :</strong> <?= htmlspecialchars($formation['niveau_rncp']) ?></p>
                            <p class="card-text mb-3"><strong>Durée :</strong> <?= htmlspecialchars($formation['duree']) ?> heures</p>
                            <a href="formation.php?id=<?= (int)$formation['id_formation'] ?>" class="mt-auto btn btn-primary">Voir la fiche</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?php include __DIR__.'/includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
