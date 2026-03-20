<?php
require_once __DIR__ . "/header.php";

$categories = [
    [
        "id" => 1,
        "nom" => "Bagues",
        "description" => "Des bagues élégantes pour sublimer chaque main avec finesse.",
        "image" => "images/bague1.jpg"
    ],
    [
        "id" => 2,
        "nom" => "Bracelets",
        "description" => "Des bracelets délicats et tendance pour toutes les occasions.",
        "image" => "images/bracelet.jpg"
    ],
    [
        "id" => 3,
        "nom" => "Boucles d’oreilles",
        "description" => "Des créations lumineuses pour compléter votre style.",
        "image" => "images/boucle1.jpg"
    ],
    [
        "id" => 4,
        "nom" => "Colliers",
        "description" => "Des colliers raffinés pour ajouter une touche d’éclat.",
        "image" => "images/collier1.jpg"
    ]
];
?>

<main class="page-categories">
    <section class="hero-categorie">
        <div class="hero-categorie-contenu">
            <p class="sur-titre-categorie">Jewelry by PC</p>
            <h1>Nos catégories</h1>
            <p>
                Explorez nos collections et choisissez l’univers qui correspond
                à votre style.
            </p>
        </div>
    </section>

    <section class="conteneur-categorie">
        <div class="grille-bijoux">
            <?php foreach ($categories as $categorie): ?>
                <article class="carte-bijou">
                    <div class="media-carte-bijou">
                        <img
                            src="<?= htmlspecialchars($categorie['image']) ?>"
                            alt="<?= htmlspecialchars($categorie['nom']) ?>"
                            class="image-carte-bijou"
                        >
                    </div>

                    <div class="contenu-carte-bijou">
                        <h2><?= htmlspecialchars($categorie['nom']) ?></h2>
                        <p class="description-carte">
                            <?= htmlspecialchars($categorie['description']) ?>
                        </p>

                        <div class="actions-carte-bijou">
                            <a href="boutique.php?id=<?= (int)$categorie['id'] ?>" class="btn-detail">
                                Voir les <?= strtolower(htmlspecialchars($categorie['nom'])) ?>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . "/footer.php"; ?>
