<?php
session_start();

$panier = $_SESSION['panier'] ?? [];

if (empty($panier)) {
    header("Location: panier.php");
    exit;
}

if (!isset($_SESSION['utilisateur']) || empty($_SESSION['utilisateur']['id'])) {
    header("Location: connexion.php");
    exit;
}

require_once __DIR__ . "/header.php";

$total = 0;
?>

<main class="page-commande">
    <section class="conteneur-commande">
        <h1>Validation de la commande</h1>

        <div class="bloc-commande">
            <h2>Récapitulatif</h2>

            <div class="liste-commande">
                <?php foreach ($panier as $article): ?>
                    <?php
                        $sousTotal = (float)$article['prix'] * (int)$article['quantite'];
                        $total += $sousTotal;
                    ?>
                    <div class="article-commande">
                        <p><strong>Bijou :</strong> <?= htmlspecialchars($article['nom']) ?></p>
                        <p><strong>Taille :</strong> <?= htmlspecialchars($article['taille']) ?></p>
                        <p><strong>Quantité :</strong> <?= (int)$article['quantite'] ?></p>
                        <p><strong>Prix unitaire :</strong> <?= number_format((float)$article['prix'], 2, ',', ' ') ?> $</p>
                        <p><strong>Sous-total :</strong> <?= number_format($sousTotal, 2, ',', ' ') ?> $</p>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="total-commande">
                <h3>Total à payer : <?= number_format($total, 2, ',', ' ') ?> $</h3>
            </div>

            <form action="livraison.php" method="post">
                <button type="submit" class="btn-commande">Confirmer la commande</button>
            </form>
        </div>
    </section>
</main>

<?php require_once __DIR__ . "/footer.php"; ?>
