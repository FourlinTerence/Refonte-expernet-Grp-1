<?php
/**
 * Page d'accueil dynamique – ExperNet
 * Utilise le header partagé via include/header.php
 */
require_once __DIR__.'/includes/header.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Expernet Campus - Accueil</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
<main class="flex-grow-1">
    <!-- Hero Section -->
    <section class="py-5 text-center my-md-4">
        <div class="container py-lg-4">
            <h1 class="display-5 fw-bold mb-3">Trouvez votre future<br><span class="text-brand-blue">formation</span></h1>
            <p class="fs-5 text-muted mb-4 font-montserrat">
                Un campus numérique dédié à l'alternance <span class="badge badge-brand-orange ms-2 align-middle fs-6">La Réunion &amp; Mayotte</span>
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="catalogue.php" class="btn btn-brand-primary px-5 py-3 fs-6 rounded-0 font-montserrat fw-semibold shadow-sm">Voir le catalogue</a>
                <a href="inscription_form.php" class="btn btn-brand-orange px-5 py-3 fs-6 rounded-0 font-montserrat fw-semibold shadow-sm">S'inscrire / Candidater</a>
            </div>
        </div>
    </section>

    <!-- Cards – niveaux de formation -->
    <section class="bg-brand-gray py-5">
        <div class="container py-3">
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="bg-white p-4 h-100 shadow-sm text-center d-flex flex-column justify-content-between border-top-brand-blue">
                        <span class="badge badge-brand-blue mb-2">Niveau 5</span>
                        <h2 class="h4 fw-bold mb-3 text-brand-blue">BAC +2</h2>
                        <p class="text-secondary small mb-0">Démarrez votre parcours dans le numérique avec des formations de niveau 5 : Technicien Systèmes Réseaux, Graphiste Multimédia &amp; IA.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white p-4 h-100 shadow-sm text-center d-flex flex-column justify-content-between border-top-brand-orange">
                        <span class="badge badge-brand-orange mb-2">Alternance</span>
                        <h2 class="h4 fw-bold mb-3 text-brand-orange">BAC +3/4</h2>
                        <p class="text-secondary small mb-0">Spécialisez‑vous avec des titres certifiés de niveau 6 : Concepteur Développeur d'Applications (CDA), Administrateur Systèmes et Réseaux (ASR).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white p-4 h-100 shadow-sm text-center d-flex flex-column justify-content-between border-top-brand-blue">
                        <span class="badge badge-brand-blue mb-2">Niveau 7</span>
                        <h2 class="h4 fw-bold mb-3 text-brand-blue">BAC +5</h2>
                        <p class="text-secondary small mb-0">Devenez expert de haut niveau avec nos cursus Bac+5 certifiés : Hacking &amp; Cybersécurité, Directeur des Projets IT, Stratégie Digitale.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistiques clés -->
    <section class="py-5" id="campus">
        <div class="container py-3">
            <div class="row text-center gy-4 font-montserrat">
                <div class="col-md-4"><p class="display-6 fw-bold mb-1 text-brand-blue">500+</p><p class="fw-semibold text-secondary mb-0">étudiants formés</p></div>
                <div class="col-md-4"><p class="display-6 fw-bold mb-1 text-brand-blue">90%</p><p class="fw-semibold text-secondary mb-0">de réussite aux examens</p></div>
                <div class="col-md-4"><p class="display-6 fw-bold mb-1 text-brand-orange">85%</p><p class="fw-semibold text-secondary mb-0">d'employabilité directe</p></div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__.'/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
