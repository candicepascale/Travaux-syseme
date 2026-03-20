<?php
session_start();

require_once __DIR__ . "/donnees/BijouDAO.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: categorie.php");
    exit;
}

$bijouId = isset($_POST['bijou_id']) ? (int)$_POST['bijou_id'] : 0;
$tailleId = isset($_POST['taille_id']) ? (int)$_POST['taille_id'] : 0;
$quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 1;

if ($bijouId <= 0 || $tailleId <= 0 || $quantite <= 0) {
    $_SESSION['message_panier'] = "Données invalides.";
    header("Location: categorie.php");
    exit;
}

$bijou = BijouDAO::trouverParId($bijouId);

if (!$bijou) {
    $_SESSION['message_panier'] = "Bijou introuvable.";
    header("Location: categorie.php");
    exit;
}

$variantes = $bijou->obtenir('variantes') ?? [];
$images = $bijou->obtenir('images') ?? [];

$tailleSelectionnee = null;

foreach ($variantes as $variante) {
    if ((int)$variante['taille_id'] === $tailleId) {
        $tailleSelectionnee = $variante;
        break;
    }
}

if (!$tailleSelectionnee) {
    $_SESSION['message_panier'] = "Taille invalide.";
    header("Location: detail-bijou.php?id=" . $bijouId);
    exit;
}

$stockDisponible = (int)$tailleSelectionnee['stock'];

if ($stockDisponible <= 0) {
    $_SESSION['message_panier'] = "Stock épuisé pour cette taille.";
    header("Location: detail-bijou.php?id=" . $bijouId);
    exit;
}

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$quantiteDejaDansPanier = 0;

foreach ($_SESSION['panier'] as $article) {
    if (
        (int)$article['bijou_id'] === $bijouId &&
        (int)$article['taille_id'] === $tailleId
    ) {
        $quantiteDejaDansPanier = (int)$article['quantite'];
        break;
    }
}

$quantiteTotale = $quantiteDejaDansPanier + $quantite;

if ($quantiteTotale > $stockDisponible) {
    $_SESSION['message_panier'] = $stockDisponible . " seulement disponible(s) pour cette taille.";
    header("Location: detail-bijou.php?id=" . $bijouId);
    exit;
}

$imagePrincipale = !empty($images) ? $images[0]['chemin_image'] : '';

$articleExiste = false;

foreach ($_SESSION['panier'] as &$article) {
    if (
        (int)$article['bijou_id'] === $bijouId &&
        (int)$article['taille_id'] === $tailleId
    ) {
        $article['quantite'] += $quantite;
        $articleExiste = true;
        break;
    }
}
unset($article);

if (!$articleExiste) {
    $_SESSION['panier'][] = [
        'bijou_id' => $bijouId,
        'taille_id' => $tailleId,
        'nom' => $bijou->obtenir('nom'),
        'prix' => (float)$bijou->obtenir('prix'),
        'taille' => $tailleSelectionnee['libelle'],
        'quantite' => $quantite,
        'image' => $imagePrincipale
    ];
}

$_SESSION['message_panier_success'] = "Article ajouté au panier.";
header("Location: panier.php");
exit;
