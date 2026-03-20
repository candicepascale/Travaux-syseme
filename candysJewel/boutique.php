<?php
require_once __DIR__ . "/donnees/BijouDAO.php";
require_once __DIR__ . "/header.php";

$categorieId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$categories = [
    1 => "Bagues",
    2 => "Bracelets",
    3 => "Boucles d’oreilles",
    4 => "Colliers"
];

if ($categorieId <= 0 || !isset($categories[$categorieId])) {
    header("Location: categorie.php");
    exit;
}

$titreCategorie = $categories[$categorieId];
$bijoux = BijouDAO::listerBijouxParCategorie($categorieId);

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
?>

<main class="page-categorie">
    <section class="hero-categorie">
        <div class="hero-categorie-contenu">
            <p class="sur-titre-categorie">Collection Jewelry by PC</p>
            <h1><?= htmlspecialchars($titreCategorie) ?></h1>
            <p>
                Découvrez notre sélection de <?= strtolower($titreCategorie) ?>,
                pensée pour sublimer votre style avec élégance.
            </p>
        </div>
    </section>

    <section class="conteneur-categorie">
        <?php if (empty($bijoux)): ?>
            <div class="etat-vide-categorie">
                <h2>Aucun bijou disponible</h2>
                <p>Cette catégorie ne contient aucun article pour le moment.</p>
                <a href="categorie.php" class="btn-detail">Retour aux catégories</a>
            </div>
        <?php else: ?>
            <div class="grille-bijoux">
                <?php foreach ($bijoux as $bijou): ?>
                    <?php
                    $images = $bijou->obtenir('images') ?? [];
                    $imagePrincipale = !empty($images) ? $images[0]['chemin_image'] : null;

                    $nom = nettoyerTexte($bijou->obtenir('nom'));

                    $variantes = $bijou->obtenir('variantes') ?? [];

                    $stockFaible = null;
                    $enStock = false;

                    foreach ($variantes as $variante) {
                        $stock = (int)$variante['stock'];

                        if ($stock > 0) {
                            $enStock = true;

                            if ($stock <= 3) {
                                if ($stockFaible === null || $stock < $stockFaible) {
                                    $stockFaible = $stock;
                                }
                            }
                        }
                    }
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
                            <h2><?= htmlspecialchars($nom) ?></h2>

                            <p class="prix-bijou">
                                <?= number_format((float)$bijou->obtenir('prix'), 2, ',', ' ') ?> $
                            </p>

                            <?php if (!$enStock): ?>
                                <p class="message-stock-erreur">Stock épuisé</p>
                            <?php elseif ($stockFaible !== null): ?>
                                <p class="message-stock-erreur"><?= $stockFaible ?> seulement disponible(s)</p>
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
