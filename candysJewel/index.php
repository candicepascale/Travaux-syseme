<?php
include 'header.php';
?>

<main class="page-accueil">

    <section class="hero-accueil">
        <div class="hero-contenu">
            <p class="sur-titre">Bijoux élégants • Femme • Homme • Enfant</p>
            <h2>Bienvenue chez Jewelry by PC</h2>
            <p>
                Découvrez une collection de bijoux élégants, raffinés et tendances
                pour femme, homme et enfant. Offrez-vous l’éclat que vous méritez.
            </p>
            <div class="hero-boutons">
                <a href="categorie.php" class="picture">Magasiner maintenant</a>
                <a href="nouveautes.php" class="picture">Découvrir les nouveautés</a>
            </div>
        </div>
    </section>

    <?php if (isset($_SESSION['utilisateur'])): ?>
        <section class="bienvenue-client">
            <p>
                Bonjour <?= htmlspecialchars($_SESSION['utilisateur']['prenom']) ?>,
                heureux de vous revoir sur Jewelry by PC.
            </p>
        </section>
    <?php endif; ?>

    <section class="section-intro">
        <h2>Accueil</h2>
    </section>

    <div class="evenement">

        <div class="objet">
            <a href="images/bijeven.jpg" target="_blank" rel="noopener noreferrer">
                <img src="images/bijeven.jpg" alt="Grande solde de bijoux" class="header21" title="Grande solde de bijoux" id="even">
            </a>
            <h3>Grande solde de bijoux</h3>

            <section>
                <p>Un événement à ne surtout pas rater.</p>
                <p>Profitez de nos offres spéciales sur plusieurs bijoux sélectionnés.</p>
                <p>Plus que :</p>
            </section>

            <div id="decompte" class="decompte1"></div>

            <p>
                <a href="categorie.php" class="picture">Voir les promotions</a>
            </p>
        </div>

        <div class="objet">
            <h3>Une variété incroyable</h3>

            <article>
                <a href="images/even1.jpg" target="_blank" rel="noopener noreferrer">
                    <img src="images/even1.jpg" alt="Vitrine de bijoux" class="header21" title="Vitrine de bijoux" id="even0">
                </a>

                <p>
                    Nous vous proposons plusieurs catégories de bijoux :
                    bagues, bracelets, boucles d’oreilles, colliers et bijoux personnalisés.
                </p>

                <p>
                    Que vous soyez un homme, une femme ou que vous cherchiez un bijou
                    pour un enfant, vous trouverez certainement votre bonheur.
                </p>

                <p>
                    <a href="categorie.php" class="picture">Voir les catégories</a>
                </p>
            </article>
        </div>

        <div class="objet">
            <h3>Restez connectés</h3>

            <p>
                Recevez par mail nos nouveautés, promotions et inspirations bijoux.
            </p>

            <form action="index.php" method="post" class="form-infolettre">
                <fieldset>
                    <legend>Infolettre</legend>

                    <div class="input-container">
                        <label for="email">Adresse mail</label>
                        <input type="email" name="email" id="email" class="email" placeholder="Adresse mail">
                    </div>

                    <div class="input-container">
                        <input type="submit" value="Envoyer">
                    </div>
                </fieldset>
            </form>
        </div>

    </div>

    <section class="categories-accueil">
        <h2>Nos catégories</h2>

        <div class="evenement">
            <div class="objet">
                <h3>Bagues</h3>
                <p>Des bagues élégantes pour sublimer chaque main.</p>
                <a href="boutique.php?id=1" class="picture">Voir les bagues</a>
            </div>

            <div class="objet">
                <h3>Bracelets</h3>
                <p>Des bracelets délicats et tendance pour toutes les occasions.</p>
                <a href="boutique.php?id=2" class="picture">Voir les bracelets</a>
            </div>

            <div class="objet">
                <h3>Boucles d’oreilles</h3>
                <p>Des créations lumineuses pour compléter votre style.</p>
                <a href="boutique.php?id=3" class="picture">Voir les boucles d’oreilles</a>
            </div>

            <div class="objet">
                <h3>Colliers</h3>
                <p>Des colliers raffinés pour ajouter une touche d’éclat.</p>
                <a href="boutique.php?id=4" class="picture">Voir les colliers</a>
            </div>
        </div>
    </section>

    <section class="avantages-boutique">
        <h2>Pourquoi choisir Jewelry by PC ?</h2>

        <div class="evenement">
            <div class="objet">
                <h3>Bijoux pour tous</h3>
                <p>Des collections pour femme, homme et enfant.</p>
            </div>

            <div class="objet">
                <h3>Style et élégance</h3>
                <p>Des bijoux choisis pour leur finesse et leur beauté.</p>
            </div>

            <div class="objet">
                <h3>Achat simplifié</h3>
                <p>Un parcours clair pour découvrir, choisir et commander.</p>
            </div>
        </div>
    </section>

    <section class="galerie-accueil">
        <h2>Inspiration bijoux</h2>

        <div class="conteneur-carrousel">
            <div class="conteneur-images">
                <img src="images/img1.jpg" alt="Bijou 1" class="actif">
                <img src="images/img2.jpg" alt="Bijou 2">
                <img src="images/img3.jpg" alt="Bijou 3">
                <img src="images/img4.jpg" alt="Bijou 4">
                <img src="images/img5.jpg" alt="Bijou 5">
            </div>

            <div class="commandes">
                <button class="gauche" type="button">
                    <img src="images/left.svg" alt="Image précédente">
                </button>
                <button class="droite" type="button">
                    <img src="images/right.svg" alt="Image suivante">
                </button>
            </div>

            <div class="cercles">
                <button data-clic="1" class="cercle actif-cercle" type="button"></button>
                <button data-clic="2" class="cercle" type="button"></button>
                <button data-clic="3" class="cercle" type="button"></button>
                <button data-clic="4" class="cercle" type="button"></button>
                <button data-clic="5" class="cercle" type="button"></button>
            </div>
        </div>
    </section>

</main>

<?php include 'footer.php'; ?>
