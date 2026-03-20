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

require_once __DIR__ . '/modele/AdresseLivraison.php';
require_once __DIR__ . '/header.php';

$adresseLivraison = new AdresseLivraison([
    'nom' => $_SESSION['utilisateur']['nom'] ?? '',
    'prenom' => $_SESSION['utilisateur']['prenom'] ?? '',
    'email' => $_SESSION['utilisateur']['email'] ?? ''
]);
?>

<main class="page-livraison">
    <section class="conteneur-livraison">
        <h1>Adresse de livraison</h1>

        <form action="traiter_livraison.php" method="post" class="form-livraison">
            <label>Nom</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($adresseLivraison->obtenir('nom')) ?>" required>

            <label>Prénom</label>
            <input type="text" name="prenom" value="<?= htmlspecialchars($adresseLivraison->obtenir('prenom')) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($adresseLivraison->obtenir('email')) ?>" required>

            <label>Téléphone</label>
            <input type="text" name="telephone" required>

            <label>Adresse</label>
            <input type="text" name="adresse" required>

            <label>Appartement</label>
            <input type="text" name="appartement">

            <label>Ville</label>
            <input type="text" name="ville" required>

            <label>Province</label>
            <input type="text" name="province" required>

            <label>Code postal</label>
            <input type="text" name="code_postal" required>

            <label>Pays</label>
            <input type="text" name="pays" value="Canada" required>

            <button type="submit" class="btn-commande">Continuer</button>
        </form>
    </section>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
