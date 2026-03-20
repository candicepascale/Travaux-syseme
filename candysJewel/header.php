<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Jewelry by PC - Boutique en ligne de bijoux élégants et tendance.">

    <link rel="shortcut icon" href="images/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="style/style.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    >

    <script src="js/agrandissement.js" defer></script>
    <script src="js/decompte.js" defer></script>
    <script src="js/caroussel.js" defer></script>
    <script src="js/compte.js" defer></script>
    <script src="js/validation.js" defer></script>
    <script src="js/defautText.js" defer></script>
    <script src="js/acordeon.js" defer></script>
    <script src="js/lightbox-min.js" defer></script>
    <script src="js/bijou.js" defer></script>
    <script src="js/script.js" defer></script>

    <title>Jewelry by PC</title>
</head>

<body>
    <header class="header-site">
        <div class="topbar-site">

            <div class="topbar-gauche">
                <span class="navbar-toggle" id="js-navbar-toggle">
                    <div class="bar1"></div>
                    <div class="bar2"></div>
                    <div class="bar3"></div>
                </span>
            </div>

            <a href="index.php" class="logo-site">Jewelry by PC</a>

            <div class="actions-site">

                <a href="panier.php" class="action-icone" title="Panier">
                    <i class="fa-solid fa-bag-shopping"></i>
                </a>

                <?php if (isset($_SESSION['utilisateur'])): ?>
                    <?php
                    $prenom = $_SESSION['utilisateur']['prenom'] ?? '';
                    $initiale = strtoupper(substr($prenom, 0, 1));
                    ?>

                    <a href="compte.php" class="avatar-utilisateur" title="Mon compte">
                        <?= htmlspecialchars($initiale) ?>
                    </a>

                    <a href="deconnexion.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="action-icone" title="Déconnexion">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                <?php else: ?>
                    <a href="connexion.php" class="action-icone" title="Connexion">
                        <i class="fa-solid fa-user"></i>
                    </a>
                <?php endif; ?>

            </div>
        </div>

        <nav class="nav-site">
            <ul class="menu" id="js-menu">
                <li><a href="index.php">Accueil</a></li>

                <li class="menu-avec-sousmenu">
                    <a href="categorie.php">Catalogue</a>
                    <ul class="sous-menu">
                        <li><a href="categorie.php">Toutes les catégories</a></li>
                        <li><a href="boutique.php?id=1">Bagues</a></li>
                        <li><a href="boutique.php?id=2">Bracelets</a></li>
                        <li><a href="boutique.php?id=3">Boucles d’oreilles</a></li>
                        <li><a href="boutique.php?id=4">Colliers</a></li>
                    </ul>
                </li>

                <li><a href="nouveautes.php">Nouveautés</a></li>
                <li><a href="mission.php">Notre univers</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="faq.php">FAQ</a></li>
            </ul>
        </nav>
    </header>

    <div class="banniere-site">
        <img
            src="images/headers.jpg"
            alt="Bannière de bijoux Jewelry by PC"
            class="header"
            title="une image de bijoux"
        >
    </div>
