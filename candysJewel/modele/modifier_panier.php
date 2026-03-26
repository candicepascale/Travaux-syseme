<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/donnees/BijouDAO.php";

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
$quantite = filter_input(INPUT_POST, 'quantite', FILTER_VALIDATE_INT);

$index = ($index !== false && $index !== null) ? $index : -1;
$quantite = ($quantite !== false && $quantite !== null) ? $quantite : 1;

if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier']) || !isset($_SESSION['panier'][$index])) {
    header("Location: panier.php");
    exit;
}

$article = $_SESSION['panier'][$index];
$bijouId = (int)($article['bijou_id'] ?? 0);
$tailleId = (int)($article['taille_id'] ?? 0);

if ($bijouId <= 0 || $tailleId <= 0) {
    $_SESSION['message_panier'] = "Article invalide.";
    header("Location: panier.php");
    exit;
}

$bijou = BijouDAO::trouverParId($bijouId);

if (!$bijou) {
    $_SESSION['message_panier'] = "Article introuvable.";
    header("Location: panier.php");
    exit;
}

$variantes = $bijou->obtenir('variantes') ?? [];
$stockDisponible = 0;
$tailleLibelle = '';

foreach ($variantes as $variante) {
    if ((int)($variante['taille_id'] ?? 0) === $tailleId) {
        $stockDisponible = (int)($variante['stock'] ?? 0);
        $tailleLibelle = (string)($variante['libelle'] ?? '');
        break;
    }
}

if ($quantite <= 0) {
    unset($_SESSION['panier'][$index]);
    $_SESSION['panier'] = array_values($_SESSION['panier']);
    $_SESSION['message_panier_success'] = "Article supprimé du panier.";
    header("Location: panier.php");
    exit;
}

if ($stockDisponible <= 0) {
    unset($_SESSION['panier'][$index]);
    $_SESSION['panier'] = array_values($_SESSION['panier']);
    $_SESSION['message_panier'] = "Stock épuisé pour cette taille.";
    header("Location: panier.php");
    exit;
}

if ($quantite > $stockDisponible) {
    $_SESSION['message_panier'] = $stockDisponible . " seulement disponible(s) pour la taille " . $tailleLibelle . ".";
    $_SESSION['panier'][$index]['quantite'] = $stockDisponible;
    header("Location: panier.php");
    exit;
}

$_SESSION['panier'][$index]['quantite'] = $quantite;
$_SESSION['message_panier_success'] = "Panier mis à jour.";

header("Location: panier.php");
exit;
