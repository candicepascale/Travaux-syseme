<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!isset($_SESSION['utilisateur']) || empty($_SESSION['utilisateur']['id'])) {
    header("Location: connexion.php");
    exit;
}

require_once __DIR__ . '/donnees/CommandeDAO.php';
require_once __DIR__ . '/header.php';

$utilisateurId = (int) $_SESSION['utilisateur']['id'];
$commandes = CommandeDAO::listerParUtilisateur($utilisateurId);

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

<main class="page-commandes-utilisateur">
    <section class="conteneur-commandes-utilisateur">
        <h1>Mes commandes</h1>

        <?php if (empty($commandes)): ?>
            <div class="etat-vide-categorie">
                <h2>Aucune commande pour le moment</h2>
                <p>Vous n’avez encore passé aucune commande.</p>
                <a href="categorie.php" class="btn-detail">Découvrir la boutique</a>
            </div>
        <?php else: ?>
            <div class="liste-commandes-utilisateur">
                <?php foreach ($commandes as $commande): ?>
                    <article class="carte-commande-utilisateur">
                        <div class="ligne-commande-utilisateur">
                            <span class="label-commande">Commande</span>
                            <strong>#<?= (int)$commande->obtenir('id') ?></strong>
                        </div>

                        <div class="ligne-commande-utilisateur">
                            <span class="label-commande">Date</span>
                            <span><?= htmlspecialchars($commande->obtenir('date_creation')) ?></span>
                        </div>

                        <div class="ligne-commande-utilisateur">
                            <span class="label-commande">Total</span>
                            <strong><?= number_format((float)$commande->obtenir('montant_total'), 2, ',', ' ') ?> $</strong>
                        </div>

                        <div class="ligne-commande-utilisateur">
                            <span class="label-commande">Statut</span>
                            <span class="badge-statut badge-<?= htmlspecialchars($commande->obtenir('statut')) ?>">
                                <?= traduireStatut($commande->obtenir('statut')) ?>
                            </span>
                        </div>

                        <div class="actions-carte-bijou">
                            <a href="detail-commande.php?id=<?= (int)$commande->obtenir('id') ?>" class="btn-detail">
                                Voir la commande
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
