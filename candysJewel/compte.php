<?php
session_start();

// empêcher accès si pas connecté
if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

require_once "header.php";
?>

<h2>Mon compte</h2>

<p>
Bienvenue
<strong>
<?= htmlspecialchars($_SESSION['utilisateur']['prenom']) ?>
<?= htmlspecialchars($_SESSION['utilisateur']['nom']) ?>
</strong>
</p>

<p>Email : <?= htmlspecialchars($_SESSION['utilisateur']['email']) ?></p>

<hr>

<ul>
<li><a href="panier.php">Voir mon panier</a></li>
<li><a href="mes-commandes.php">Mes commandes</a></li>
<li><a href="deconnexion.php">Se déconnecter</a></li>
</ul>

<?php require_once "footer.php"; ?>
