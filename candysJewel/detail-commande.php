<?php
session_start();

if (!isset($_SESSION['utilisateur']) || empty($_SESSION['utilisateur']['id'])) {
    header("Location: connexion.php");
    exit;
}

require_once __DIR__ . '/donnees/CommandeDAO.php';
require_once __DIR__ . '/donnees/AdresseLivraisonDAO.php';
require_once __DIR__ . '/header.php';

$commandeId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($commandeId <= 0) {
    header("Location: mes-commandes.php");
    exit;
}

$commande = CommandeDAO::trouverParId($commandeId);

if (!$commande || (int)$commande->obtenir('utilisateur_id') !== (int)$_SESSION['utilisateur']['id']) {
    header("Location: mes-commandes.php");
    exit;
}

$adresse = null;
$adresseId = $commande->obtenir('adresse_livraison_id');

if (!empty($adresseId)) {
    $adresse = AdresseLivraisonDAO::trouverParId((int)$adresseId);
}

function traduireStatut(string $statut): string
{
    return match ($statut) {
        'payee' => 'Payée',
        'failed' => 'Échouée',
        'en_attente' => 'En attente',
        default => ucfirst($statut),
    };
}
?>

<main class="page-commande">
    <section class="conteneur-commande">
        <h1>Détail de la commande #<?= (int)$commande->obtenir('id') ?></h1>

        <div class="bloc-commande">
            <p><strong>Date :</strong> <?= htmlspecialchars($commande->obtenir('date_creation')) ?></p>
            <p><strong>Statut :</strong> <?= htmlspecialchars(traduireStatut($commande->obtenir('statut'))) ?></p>
            <p><strong>Total :</strong> <?= number_format((float)$commande->obtenir('montant_total'), 2, ',', ' ') ?> $</p>
        </div>

        <div class="bloc-commande">
            <h2>Articles</h2>

            <div class="liste-commande">
                <?php foreach ($commande->obtenirLignes() as $ligne): ?>
                    <div class="article-commande">
                        <p><strong>Bijou :</strong> <?= htmlspecialchars($ligne['nom_bijou']) ?></p>
                        <p><strong>Taille :</strong> <?= htmlspecialchars($ligne['libelle_taille'] ?? '-') ?></p>
                        <p><strong>Quantité :</strong> <?= (int)$ligne['quantite'] ?></p>
                        <p><strong>Prix unitaire :</strong> <?= number_format((float)$ligne['prix_unitaire'], 2, ',', ' ') ?> $</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($adresse): ?>
            <div class="bloc-commande">
                <h2>Adresse de livraison</h2>
                <p><?= htmlspecialchars($adresse->obtenir('prenom')) ?> <?= htmlspecialchars($adresse->obtenir('nom')) ?></p>
                <p><?= htmlspecialchars($adresse->obtenir('adresse')) ?></p>

                <?php if (!empty($adresse->obtenir('appartement'))): ?>
                    <p><?= htmlspecialchars($adresse->obtenir('appartement')) ?></p>
                <?php endif; ?>

                <p>
                    <?= htmlspecialchars($adresse->obtenir('ville')) ?>,
                    <?= htmlspecialchars($adresse->obtenir('province')) ?>,
                    <?= htmlspecialchars($adresse->obtenir('code_postal')) ?>
                </p>
                <p><?= htmlspecialchars($adresse->obtenir('pays')) ?></p>
                <p><?= htmlspecialchars($adresse->obtenir('telephone')) ?></p>
                <p><?= htmlspecialchars($adresse->obtenir('email')) ?></p>
            </div>
        <?php endif; ?>

        <div class="actions-carte-bijou">
            <a href="mes-commandes.php" class="btn-detail">Retour à mes commandes</a>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
