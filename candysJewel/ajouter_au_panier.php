<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/donnees/BijouDAO.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: categorie.php");
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
    header("Location: categorie.php");
    exit;
}

$bijouId = filter_input(INPUT_POST, 'bijou_id', FILTER_VALIDATE_INT);
$tailleId = filter_input(INPUT_POST, 'taille_id', FILTER_VALIDATE_INT);
$quantite = filter_input(INPUT_POST, 'quantite', FILTER_VALIDATE_INT);

$bijouId = $bijouId ?: 0;
$tailleId = $tailleId ?: 0;
$quantite = $quantite ?: 1;

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
    if ((int)($variante['taille_id'] ?? 0) === $tailleId) {
        $tailleSelectionnee = $variante;
        break;
    }
}

if (!$tailleSelectionnee) {
    $_SESSION['message_panier'] = "Taille invalide.";
    header("Location: detail-bijou.php?id=" . urlencode((string)$bijouId));
    exit;
}

$stockDisponible = (int)($tailleSelectionnee['stock'] ?? 0);

if ($stockDisponible <= 0) {
    $_SESSION['message_panier'] = "Stock épuisé pour cette taille.";
    header("Location: detail-bijou.php?id=" . urlencode((string)$bijouId));
    exit;
}

if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$quantiteDejaDansPanier = 0;

foreach ($_SESSION['panier'] as $article) {
    if (
        (int)($article['bijou_id'] ?? 0) === $bijouId &&
        (int)($article['taille_id'] ?? 0) === $tailleId
    ) {
        $quantiteDejaDansPanier = (int)($article['quantite'] ?? 0);
        break;
    }
}

$quantiteTotale = $quantiteDejaDansPanier + $quantite;

if ($quantiteTotale > $stockDisponible) {
    $_SESSION['message_panier'] = $stockDisponible . " seulement disponible(s) pour cette taille.";
    header("Location: detail-bijou.php?id=" . urlencode((string)$bijouId));
    exit;
}

$imagePrincipale = !empty($images) && !empty($images[0]['chemin_image'])
    ? $images[0]['chemin_image']
    : '';

$articleExiste = false;

foreach ($_SESSION['panier'] as &$article) {
    if (
        (int)($article['bijou_id'] ?? 0) === $bijouId &&
        (int)($article['taille_id'] ?? 0) === $tailleId
    ) {
        $article['quantite'] = (int)($article['quantite'] ?? 0) + $quantite;
        $articleExiste = true;
        break;
    }
}
unset($article);

if (!$articleExiste) {
    $_SESSION['panier'][] = [
        'bijou_id' => $bijouId,
        'taille_id' => $tailleId,
        'nom' => (string)$bijou->obtenir('nom'),
        'prix' => (float)$bijou->obtenir('prix'),
        'taille' => (string)($tailleSelectionnee['libelle'] ?? ''),
        'quantite' => $quantite,
        'image' => $imagePrincipale
    ];
}

$_SESSION['message_panier_success'] = "Article ajouté au panier.";
header("Location: panier.php");
exit;
