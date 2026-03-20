<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: livraison.php");
    exit;
}

$panier = $_SESSION['panier'] ?? [];

if (empty($panier)) {
    header("Location: panier.php");
    exit;
}

if (!isset($_SESSION['utilisateur']) || empty($_SESSION['utilisateur']['id'])) {
    header("Location: connexion.php");
    exit;
}

require_once __DIR__ . '/modele/AdresseLivraison.php';
require_once __DIR__ . '/donnees/AdresseLivraisonDAO.php';
require_once __DIR__ . '/donnees/CommandeDAO.php';

$donnees = $_POST;
$donnees['utilisateur_id'] = (int) $_SESSION['utilisateur']['id'];

$adresseLivraison = new AdresseLivraison($donnees);

if (!$adresseLivraison->estValide()) {
    $_SESSION['erreurs_livraison'] = $adresseLivraison->erreurs;
    $_SESSION['ancienne_livraison'] = $_POST;
    header("Location: livraison.php");
    exit;
}

$adresseLivraisonId = AdresseLivraisonDAO::creer($adresseLivraison);

if (!$adresseLivraisonId) {
    die("Erreur lors de l'enregistrement de l'adresse de livraison.");
}

$commandeId = CommandeDAO::creerCommande(
    (int)$_SESSION['utilisateur']['id'],
    $panier,
    $adresseLivraisonId
);

if (!$commandeId) {
    die("Erreur lors de la création de la commande.");
}

$_SESSION['commande_en_cours'] = [
    'commande_id' => $commandeId,
    'articles' => $panier,
    'adresse_livraison_id' => $adresseLivraisonId,
    'date' => date('Y-m-d H:i:s')
];

header("Location: paiement.php");
exit;
