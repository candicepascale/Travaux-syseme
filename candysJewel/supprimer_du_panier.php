<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: panier.php");
    exit;
}

$csrfTokenFormulaire = $_POST['csrf_token'] ?? '';
$csrfTokenSession = $_SESSION['csrf_token'] ?? '';

if (
    empty($csrfTokenFormulaire) ||
    empty($csrfTokenSession) ||
    !hash_equals($csrfTokenSession, $csrfTokenFormulaire)
) {
    $_SESSION['message_panier'] = "Requête invalide.";
    header("Location: panier.php");
    exit;
}

$index = filter_input(INPUT_POST, 'index', FILTER_VALIDATE_INT);
$index = ($index !== false && $index !== null) ? $index : -1;

if (isset($_SESSION['panier']) && is_array($_SESSION['panier']) && isset($_SESSION['panier'][$index])) {
    unset($_SESSION['panier'][$index]);
    $_SESSION['panier'] = array_values($_SESSION['panier']);
    $_SESSION['message_panier_success'] = "Article supprimé du panier.";
}

header("Location: panier.php");
exit;
