<?php
session_start();

require_once __DIR__ . "/donnees/BijouDAO.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: panier.php");
    exit;
}

$index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
$quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 1;

if (!isset($_SESSION['panier']) || !isset($_SESSION['panier'][$index])) {
    header("Location: panier.php");
    exit;
}

$article = $_SESSION['panier'][$index];
$bijouId = (int)$article['bijou_id'];
$tailleId = (int)$article['taille_id'];

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
    if ((int)$variante['taille_id'] === $tailleId) {
        $stockDisponible = (int)$variante['stock'];
        $tailleLibelle = $variante['libelle'];
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
