<?php
/**
 * Génère le token CSRF si nécessaire et le stocke en session.
 */
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>