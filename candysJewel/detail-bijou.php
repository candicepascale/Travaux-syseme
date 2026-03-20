<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/donnees/BijouDAO.php";
require_once __DIR__ . "/header.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$bijou = BijouDAO::trouverParId($id);
?>

<main class="page-detail-bijou">
    <?php if (!$bijou): ?>
        <section class="fiche-bijou">
            <h2>Bijou introuvable</h2>
            <p>Le bijou demandé n’existe pas ou n’est plus disponible.</p>
            <a href="categorie.php" class="btn-detail">Retour à la boutique</a>
        </section>
    <?php else: ?>
        <?php $images = $bijou->obtenir('images') ?? []; ?>
        <?php $variantes = $bijou->obtenir('variantes') ?? []; ?>

        <?php
        $aAuMoinsUneVarianteEnStock = false;
        foreach ($variantes as $variante) {
            if ((int)$variante['stock'] > 0) {
                $aAuMoinsUneVarianteEnStock = true;
                break;
            }
        }
        ?>

        <section class="fiche-bijou">
            <div class="detail-bijou-colonnes">

                <div class="detail-bijou-gauche">
                    <h2><?= htmlspecialchars($bijou->obtenir('nom')) ?></h2>

                    <div class="galerie-bijou">
                        <?php if (!empty($images)): ?>
                            <?php foreach ($images as $image): ?>
                                <img
                                    src="<?= htmlspecialchars($image['chemin_image']) ?>"
                                    alt="<?= htmlspecialchars($image['texte_alternatif'] ?: $bijou->obtenir('nom')) ?>"
                                    class="image-detail-bijou"
                                >
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Aucune image disponible pour ce bijou.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="detail-bijou-droite">
                    <div class="infos-bijou">
                        <p><strong>Description :</strong> <?= htmlspecialchars($bijou->obtenir('description')) ?></p>
                        <p><strong>Prix :</strong> <?= number_format((float)$bijou->obtenir('prix'), 2, ',', ' ') ?> $</p>
                        <p><strong>Matériau :</strong> <?= htmlspecialchars($bijou->obtenir('materiau')) ?></p>

                        <?php if (!empty($bijou->obtenir('pierre'))): ?>
                            <p><strong>Pierre :</strong> <?= htmlspecialchars($bijou->obtenir('pierre')) ?></p>
                        <?php endif; ?>

                        <p><strong>Poids :</strong> <?= htmlspecialchars($bijou->obtenir('poids')) ?> g</p>
                    </div>

                    <div class="variantes-bijou">
                        <h3>Tailles disponibles</h3>

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

                        <?php if (!$aAuMoinsUneVarianteEnStock): ?>
                            <p class="message-stock-erreur">Stock épuisé</p>
                        <?php elseif (!empty($variantes)): ?>
                            <form action="ajouter_au_panier.php" method="post" id="form-ajout-panier">
                                <input type="hidden" name="bijou_id" value="<?= (int)$bijou->obtenir('id') ?>">

                                <label for="taille_id">Choisir une taille :</label><br>
                                <select name="taille_id" id="taille_id" required>
                                    <option value="">-- Sélectionner --</option>

                                    <?php foreach ($variantes as $variante): ?>
                                        <?php $stock = (int)$variante['stock']; ?>

                                        <?php if ($stock > 0): ?>
                                            <option
                                                value="<?= (int)$variante['taille_id'] ?>"
                                                data-stock="<?= $stock ?>"
                                            >
                                                <?= htmlspecialchars($variante['libelle']) ?>
                                                -
                                                <?php if ($stock <= 3): ?>
                                                    <?= $stock ?> seulement disponible(s)
                                                <?php else: ?>
                                                    En stock (<?= $stock ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php else: ?>
                                            <option value="<?= (int)$variante['taille_id'] ?>" data-stock="0" disabled>
                                                <?= htmlspecialchars($variante['libelle']) ?> - Stock épuisé
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>

                                <br><br>

                                <p id="message-stock-detail" class="message-stock-info"></p>

                                <label for="quantite">Quantité :</label><br>
                                <input
                                    type="number"
                                    name="quantite"
                                    id="quantite"
                                    min="1"
                                    value="1"
                                    required
                                >

                                <br><br>

                                <button type="submit" class="btn-submit" id="btn-ajouter-panier">
                                    Ajouter au panier
                                </button>
                            </form>

                            <script>
                                const selectTaille = document.getElementById('taille_id');
                                const inputQuantite = document.getElementById('quantite');
                                const messageStock = document.getElementById('message-stock-detail');
                                const boutonAjout = document.getElementById('btn-ajouter-panier');

                                function mettreAJourStock() {
                                    const optionSelectionnee = selectTaille.options[selectTaille.selectedIndex];

                                    if (!optionSelectionnee || !optionSelectionnee.dataset.stock) {
                                        messageStock.textContent = '';
                                        inputQuantite.removeAttribute('max');
                                        inputQuantite.value = 1;
                                        boutonAjout.disabled = false;
                                        return;
                                    }

                                    const stock = parseInt(optionSelectionnee.dataset.stock, 10);

                                    if (stock <= 0) {
                                        messageStock.textContent = 'Stock épuisé';
                                        messageStock.className = 'message-stock-erreur';
                                        inputQuantite.value = 1;
                                        inputQuantite.max = 1;
                                        boutonAjout.disabled = true;
                                        return;
                                    }

                                    inputQuantite.max = stock;

                                    if (parseInt(inputQuantite.value, 10) > stock) {
                                        inputQuantite.value = stock;
                                    }

                                    if (stock <= 3) {
                                        messageStock.textContent = stock + ' seulement disponible(s)';
                                        messageStock.className = 'message-stock-erreur';
                                    } else {
                                        messageStock.textContent = 'En stock : ' + stock;
                                        messageStock.className = 'message-stock-ok';
                                    }

                                    boutonAjout.disabled = false;
                                }

                                selectTaille.addEventListener('change', mettreAJourStock);

                                inputQuantite.addEventListener('input', function () {
                                    const optionSelectionnee = selectTaille.options[selectTaille.selectedIndex];
                                    if (!optionSelectionnee || !optionSelectionnee.dataset.stock) {
                                        return;
                                    }

                                    const stock = parseInt(optionSelectionnee.dataset.stock, 10);
                                    let valeur = parseInt(inputQuantite.value, 10);

                                    if (isNaN(valeur) || valeur < 1) {
                                        valeur = 1;
                                    }

                                    if (valeur > stock) {
                                        valeur = stock;
                                    }

                                    inputQuantite.value = valeur;
                                });
                            </script>
                        <?php else: ?>
                            <p>Aucune taille ou variante disponible pour ce bijou.</p>
                        <?php endif; ?>
                    </div>

                    <div class="retour-boutique">
                        <a href="categorie.php" class="bouton-annuler">Retour à la boutique</a>
                    </div>
                </div>

            </div>
        </section>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . "/footer.php"; ?>
