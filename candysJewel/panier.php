<?php
session_start();
require_once __DIR__ . "/donnees/BijouDAO.php";
require_once __DIR__ . "/header.php";

$panier = $_SESSION['panier'] ?? [];
$total = 0;
?>

<main class="page-panier">
    <section class="conteneur-panier">
        <h1>Mon panier</h1>

        <?php if (isset($_SESSION['message_panier'])): ?>
            <p class="message-stock-erreur">
                <?= htmlspecialchars($_SESSION['message_panier']) ?>
            </p>
            <?php unset($_SESSION['message_panier']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['message_panier_success'])): ?>
            <p class="message-stock-ok">
                <?= htmlspecialchars($_SESSION['message_panier_success']) ?>
            </p>
            <?php unset($_SESSION['message_panier_success']); ?>
        <?php endif; ?>

        <?php if (empty($panier)): ?>
            <p>Votre panier est vide.</p>
            <a href="categorie.php" class="btn-panier">Continuer les achats</a>
        <?php else: ?>
            <div class="liste-panier">
                <?php foreach ($panier as $index => $article): ?>
                    <?php
                        $sousTotal = (float)$article['prix'] * (int)$article['quantite'];
                        $total += $sousTotal;

                        $bijouPanier = BijouDAO::trouverParId((int)$article['bijou_id']);
                        $stockDisponible = 0;

                        if ($bijouPanier) {
                            $variantesPanier = $bijouPanier->obtenir('variantes') ?? [];
                            foreach ($variantesPanier as $variantePanier) {
                                if ((int)$variantePanier['taille_id'] === (int)$article['taille_id']) {
                                    $stockDisponible = (int)$variantePanier['stock'];
                                    break;
                                }
                            }
                        }
                    ?>
                    <div class="article-panier">
                        <div class="image-panier">
                            <?php if (!empty($article['image'])): ?>
                                <img src="<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['nom']) ?>">
                            <?php else: ?>
                                <div class="image-vide-panier">Aucune image</div>
                            <?php endif; ?>
                        </div>

                        <div class="infos-panier">
                            <h2><?= htmlspecialchars($article['nom']) ?></h2>
                            <p><strong>Taille :</strong> <?= htmlspecialchars($article['taille']) ?></p>
                            <p><strong>Prix unitaire :</strong> <?= number_format((float)$article['prix'], 2, ',', ' ') ?> $</p>
                            <p><strong>Sous-total :</strong> <?= number_format($sousTotal, 2, ',', ' ') ?> $</p>

                            <?php if ($stockDisponible <= 0): ?>
                                <p class="message-stock-erreur">Stock épuisé</p>
                            <?php elseif ($stockDisponible <= 3): ?>
                                <p class="message-stock-erreur"><?= $stockDisponible ?> seulement disponible(s)</p>
                            <?php endif; ?>

                            <form action="modifier_panier.php" method="post" class="form-panier">
                                <input type="hidden" name="index" value="<?= $index ?>">

                                <label for="quantite_<?= $index ?>">Quantité :</label>
                                <input
                                    type="number"
                                    name="quantite"
                                    id="quantite_<?= $index ?>"
                                    min="1"
                                    max="<?= max(1, $stockDisponible) ?>"
                                    value="<?= (int)$article['quantite'] ?>"
                                    required
                                >

                                <button type="submit" class="btn-panier">Mettre à jour</button>
                            </form>

                            <form action="supprimer_du_panier.php" method="post" class="form-panier">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                <button type="submit" class="btn-supprimer">Supprimer</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="resume-panier">
                <h3>Total : <?= number_format($total, 2, ',', ' ') ?> $</h3>

                <div class="actions-panier">
                    <a href="categorie.php" class="btn-panier">Continuer les achats</a>
                    <a href="commande.php" class="btn-panier">Passer la commande</a>
                </div>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . "/footer.php"; ?>
