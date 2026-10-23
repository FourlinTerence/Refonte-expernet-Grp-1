<?php
/**
 * Header/navigation shared across pages.
 * Affiche les liens en fonction de la session utilisateur.
 */
session_start();
require_once __DIR__.'/../php/BDD/init.php';
require_once __DIR__.'/../csrf.php';
?>
<header class="bg-brand-gray border-bottom border-top-brand-blue">
    <nav class="navbar navbar-expand-lg py-2">
        <div class="container">
            <a class="navbar-brand py-1" href="index.php">
                <img src="img/logo.png" alt="Expernet Campus" height="46" class="d-inline-block align-text-top">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                    aria-controls="mainNavbar" aria-expanded="false" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
                <ul class="navbar-nav align-items-lg-center gap-lg-4 fs-6 font-montserrat">
                    <li class="nav-item"><a class="nav-link text-dark fw-semibold" href="catalogue.php">Catalogue</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-semibold" href="#campus">Campus</a></li>
                    <?php if (isset($_SESSION['id_utilisateur'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-brand-blue fw-bold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= htmlspecialchars($_SESSION['email']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item py-2" href="dashboard-etudiant.php">Mon tableau de bord</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2" href="php/BDD/deconnexion.php">Se déconnecter</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-brand-blue fw-bold" href="connexion_form.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Connexion
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item py-2 fw-semibold" href="connexion_form.php">Page de connexion</a></li>
                                <li><a class="dropdown-item py-2 fw-semibold text-brand-orange" href="inscription_form.php">Créer un compte</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
