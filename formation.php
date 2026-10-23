<?php
/**
 * Page détail formation – affiche les informations complètes d'une formation.
 */
require_once __DIR__.'/includes/header.php'; // charge session, init, csrf, navigation
// Récupérer l'ID de la formation depuis l'URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    // ID invalide → redirection vers le catalogue
    header('Location: catalogue.php');
    exit;
}
// Récupérer les données de la formation
$formation = $bdd->selectFormationParId($id);
if (empty($formation)) {
    // Formation introuvable → redirection vers le catalogue
    header('Location: catalogue.php');
    exit;
}
$formation = $formation[0]; // le résultat est un tableau contenant une ligne
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($formation['intitule'] ?? $formation['nom_formation']) ?> – Détail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
<main class="flex-grow-1 container py-5">
    <h1 class="mb-4"><?= htmlspecialchars($formation['intitule'] ?? $formation['nom_formation']) ?></h1>
    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Filière :</strong> <?= htmlspecialchars($formation['nom_filiere']) ?></li>
        <li class="list-group-item"><strong>Niveau RNCP :</strong> <?= htmlspecialchars($formation['niveau_rncp']) ?></li>
        <li class="list-group-item"><strong>Durée (heures) :</strong> <?= htmlspecialchars($formation['duree']) ?></li>
        <li class="list-group-item"><strong>Description :</strong> <?= nl2br(htmlspecialchars($formation['description'] ?? '')) ?></li>
    </ul>
    <a href="catalogue.php" class="btn btn-outline-secondary">← Retour au catalogue</a>
</main>
<?php include __DIR__.'/includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
