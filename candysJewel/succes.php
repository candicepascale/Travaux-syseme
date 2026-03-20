<?php
session_start();

require_once __DIR__ . '/donnees/CommandeDAO.php';
require_once __DIR__ . '/header.php';

$commandeId = isset($_GET['commande_id']) ? (int)$_GET['commande_id'] : 0;

if ($commandeId <= 0) {
    exit("commande_id manquant");
}

$commande = CommandeDAO::trouverParId($commandeId);

if (!$commande) {
    exit("Commande introuvable");
}

if ($commande->obtenir('statut') === 'payee') {
    unset($_SESSION['panier']);
    unset($_SESSION['commande_en_cours']);
}
?>

<main class="page-paiement">
    <section class="conteneur-commande">
        <h1>Paiement terminé</h1>
        <p><strong>Commande :</strong> #<?= (int)$commande->obtenir('id') ?></p>
        <p><strong>Statut :</strong> <?= htmlspecialchars($commande->obtenir('statut')) ?></p>

        <?php if ($commande->obtenir('statut') === 'payee'): ?>
            <p>Merci, votre paiement a été confirmé.</p>
        <?php else: ?>
            <p>Le paiement a été lancé, mais la confirmation serveur n’est pas encore terminée.</p>
        <?php endif; ?>

        <a href="index.php" class="btn-commande">Retour à l’accueil</a>
    </section>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
