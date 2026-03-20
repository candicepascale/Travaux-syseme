<?php
session_start();


require_once __DIR__ . '/donnees/CommandeDAO.php';



$panier = $_SESSION['panier'] ?? [];
$utilisateurConnecte = isset($_SESSION['utilisateur']) && !empty($_SESSION['utilisateur']['id']);


if (empty($panier)) {
    die("Panier vide");
}

if (!$utilisateurConnecte) {
    die("Utilisateur non connecté");
}

$utilisateurId = (int) $_SESSION['utilisateur']['id'];



$commandeId = CommandeDAO::creerCommande($utilisateurId, $panier);


if (!$commandeId) {
    die("Erreur lors de la création de la commande.");
}

$_SESSION['commande_en_cours'] = [
    'commande_id' => $commandeId,
    'articles' => $panier,
    'date' => date('Y-m-d H:i:s')
];

header("Location: paiement.php");
exit;
