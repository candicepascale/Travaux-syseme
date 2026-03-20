<?php
require_once __DIR__ . "/donnees/BijouDAO.php";
require_once __DIR__ . "/header.php";

$bijoux = BijouDAO::listerBijoux();

/**
 * Corrige les textes mal encodés ou contenant des entités HTML.
 */
function nettoyerTexte(?string $texte): string
{
    if ($texte === null) {
        return '';
    }

    $texte = html_entity_decode($texte, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $corrige = @mb_convert_encoding($texte, 'UTF-8', 'UTF-8');

    return $corrige ?: $texte;
}

/**
 * Retourne le nom de la catégorie à partir de son id.
 */
function obtenirNomCategorie(int $categorieId): string
{
    $categories = [
        1 => "Bagues",
        2 => "Bracelets",
        3 => "Boucles d’oreilles",
        4 => "Colliers"
    ];

    return $categories[$categorieId] ?? "Bijoux";
}

/**
 * Analyse le stock des variantes pour afficher
 * “Stock épuisé” ou “x seulement disponible(s)”.
 */
function analyserStock(array $variantes): array
{
    $enStock = false;
    $stockFaible = null;

    foreach ($variantes as $variante) {
        $stock = (int)($variante['stock'] ?? 0);

        if ($stock > 0) {
            $enStock = true;

            if ($stock <= 3) {
                if ($stockFaible === null || $stock < $stockFaible) {
                    $stockFaible = $stock;
                }
            }
        }
    }

    return [
        'enStock' => $enStock,
        'stockFaible' => $stockFaible
    ];
}
?>

<main class="page-nouveautes">
    <section class="hero-categorie hero-nouveautes">
        <div class="hero-categorie-contenu">
            <p class="sur-titre-categorie">Jewelry by PC</p>
            <h1>Nouveautés</h1>
            <p>
                Découvrez les derniers bijoux ajoutés à notre collection,
                sélectionnés pour apporter éclat, finesse et élégance à votre style.
            </p>
        </div>
    </section>

    <section class="conteneur-categorie">
        <?php if (empty($bijoux)): ?>
            <div class="etat-vide-categorie">
                <h2>Aucune nouveauté disponible</h2>
                <p>Les nouveaux bijoux arriveront bientôt.</p>
                <a href="categorie.php" class="btn-detail">Retour aux catégories</a>
            </div>
        <?php else: ?>
            <div class="grille-bijoux">
                <?php foreach ($bijoux as $bijou): ?>
                    <?php
                    $images = $bijou->obtenir('images') ?? [];
                    $variantes = $bijou->obtenir('variantes') ?? [];

                    $imagePrincipale = !empty($images) ? $images[0]['chemin_image'] : null;

                    $nom = nettoyerTexte($bijou->obtenir('nom'));
                    $description = nettoyerTexte($bijou->obtenir('description'));
                    $categorieNom = obtenirNomCategorie((int)$bijou->obtenir('categorie_id'));

                    $etatStock = analyserStock($variantes);
                    $enStock = $etatStock['enStock'];
                    $stockFaible = $etatStock['stockFaible'];
                    ?>

                    <article class="carte-bijou carte-bijou-simple">
                        <div class="media-carte-bijou">
                            <?php if ($imagePrincipale): ?>
                                <img
                                    src="<?= htmlspecialchars($imagePrincipale) ?>"
                                    alt="<?= htmlspecialchars($nom) ?>"
                                    class="image-carte-bijou"
                                >
                            <?php else: ?>
                                <div class="image-vide">Aucune image</div>
                            <?php endif; ?>
                        </div>

                        <div class="contenu-carte-bijou">
                            <p class="badge-categorie-bijou">
                                <?= htmlspecialchars($categorieNom) ?>
                            </p>

                            <h2><?= htmlspecialchars($nom) ?></h2>

                            <p class="description-carte">
                                <?= htmlspecialchars(mb_strimwidth($description, 0, 120, '...')) ?>
                            </p>

                            <p class="prix-bijou">
                                <?= number_format((float)$bijou->obtenir('prix'), 2, ',', ' ') ?> $
                            </p>

                            <?php if (!$enStock): ?>
                                <p class="message-stock-erreur">Stock épuisé</p>
                            <?php elseif ($stockFaible !== null): ?>
                                <p class="message-stock-erreur"><?= $stockFaible ?> seulement disponible(s)</p>
                            <?php else: ?>
                                <p class="message-stock-ok">Nouveau en stock</p>
                            <?php endif; ?>

                            <div class="actions-carte-bijou">
                                <a href="detail-bijou.php?id=<?= (int)$bijou->obtenir('id') ?>" class="btn-detail">
                                    Voir le détail
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="retour-categorie-bas">
                <a href="categorie.php" class="btn-detail">Retour aux catégories</a>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . "/footer.php"; ?>
