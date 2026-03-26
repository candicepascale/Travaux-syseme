<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/accesseur/Configuration.php';
require_once __DIR__ . '/donnees/CommandeDAO.php';

if (!isset($_SESSION['utilisateur']) || empty($_SESSION['utilisateur']['id'])) {
    header("Location: connexion.php");
    exit;
}

if (
    !isset($_SESSION['commande_en_cours']) ||
    empty($_SESSION['commande_en_cours']['commande_id'])
) {
    header("Location: panier.php");
    exit;
}

$commandeId = (int) $_SESSION['commande_en_cours']['commande_id'];
$utilisateurConnecteId = (int) $_SESSION['utilisateur']['id'];

if ($commandeId <= 0) {
    header("Location: panier.php");
    exit;
}

$commande = CommandeDAO::trouverParId($commandeId);

if (!$commande) {
    error_log("Commande introuvable dans paiement.php pour commande_id={$commandeId}");
    $_SESSION['message_paiement'] = "Commande introuvable.";
    header("Location: panier.php");
    exit;
}

$utilisateurCommandeId = (int) $commande->obtenir('utilisateur_id');

if ($utilisateurCommandeId !== $utilisateurConnecteId) {
    error_log("Tentative d'accès non autorisé à la commande {$commandeId} par utilisateur {$utilisateurConnecteId}");
    $_SESSION['message_paiement'] = "Accès non autorisé à cette commande.";
    header("Location: panier.php");
    exit;
}

$lignes = $commande->obtenirLignes();

if (empty($lignes) || !is_array($lignes)) {
    error_log("Aucune ligne de commande valide pour commande_id={$commandeId}");
    $_SESSION['message_paiement'] = "Impossible de traiter le paiement de cette commande.";
    header("Location: panier.php");
    exit;
}

$lineItems = [];

foreach ($lignes as $ligne) {
    $prixUnitaire = isset($ligne['prix_unitaire']) ? (float) $ligne['prix_unitaire'] : 0;
    $quantite = isset($ligne['quantite']) ? (int) $ligne['quantite'] : 0;
    $nomBijou = trim((string) ($ligne['nom_bijou'] ?? 'Bijou'));
    $libelleTaille = trim((string) ($ligne['libelle_taille'] ?? 'N/A'));

    if ($prixUnitaire <= 0 || $quantite <= 0) {
        error_log("Ligne de commande invalide pour commande_id={$commandeId}");
        continue;
    }

    $montantUnitaireCents = (int) round($prixUnitaire * 100);

    if ($montantUnitaireCents <= 0) {
        error_log("Montant Stripe invalide pour commande_id={$commandeId}");
        continue;
    }

    $lineItems[] = [
        'price_data' => [
            'currency' => 'cad',
            'unit_amount' => $montantUnitaireCents,
            'product_data' => [
                'name' => $nomBijou . ' - Taille ' . $libelleTaille,
            ],
        ],
        'quantity' => $quantite,
    ];
}

if (empty($lineItems)) {
    error_log("Aucun line_item valide généré pour commande_id={$commandeId}");
    $_SESSION['message_paiement'] = "Impossible de générer le paiement pour cette commande.";
    header("Location: panier.php");
    exit;
}

try {
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    $session = \Stripe\Checkout\Session::create([
        'mode' => 'payment',
        'client_reference_id' => (string) $commandeId,
        'metadata' => [
            'commande_id' => (string) $commandeId,
            'utilisateur_id' => (string) $utilisateurCommandeId,
        ],
        'line_items' => $lineItems,
        'success_url' => BASE_URL . '/succes.php?commande_id=' . urlencode((string)$commandeId),
        'cancel_url'  => BASE_URL . '/annulation.php?commande_id=' . urlencode((string)$commandeId),
    ]);

    CommandeDAO::mettreAJourStatut(
        $commandeId,
        'en_attente',
        $session->id,
        null
    );

    header("Location: " . $session->url, true, 303);
    exit;

} catch (Exception $e) {
    die("Erreur Stripe réelle : " . $e->getMessage());
}
