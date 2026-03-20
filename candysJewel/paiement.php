<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/accesseur/Configuration.php';
require_once __DIR__ . '/donnees/CommandeDAO.php';

if (
    !isset($_SESSION['commande_en_cours']) ||
    empty($_SESSION['commande_en_cours']['commande_id'])
) {
    header("Location: panier.php");
    exit;
}

$commandeId = (int) $_SESSION['commande_en_cours']['commande_id'];
$commande = CommandeDAO::trouverParId($commandeId);

if (!$commande) {
    die("Commande introuvable.");
}

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$lignes = $commande->obtenirLignes();
$lineItems = [];

foreach ($lignes as $ligne) {
    $lineItems[] = [
        'price_data' => [
            'currency' => 'cad',
            'unit_amount' => (int) round(((float)$ligne['prix_unitaire']) * 100),
            'product_data' => [
                'name' => $ligne['nom_bijou'] . ' - Taille ' . ($ligne['libelle_taille'] ?? 'N/A'),
            ],
        ],
        'quantity' => (int) $ligne['quantite'],
    ];
}

try {
    $session = \Stripe\Checkout\Session::create([
        'mode' => 'payment',
        'client_reference_id' => (string) $commandeId,
        'metadata' => [
            'commande_id' => (string) $commandeId,
            'utilisateur_id' => (string) $commande->obtenir('utilisateur_id'),
        ],
        'line_items' => $lineItems,
        'success_url' => BASE_URL . '/succes.php?commande_id=' . $commandeId,
        'cancel_url'  => BASE_URL . '/annulation.php?commande_id=' . $commandeId,
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
    die("Erreur Stripe : " . $e->getMessage());
}
