<?php
session_start();

require_once __DIR__ . "/modele/Utilisateur.php";
require_once __DIR__ . "/donnees/UtilisateurDAO.php";
require_once __DIR__ . "/header.php";

// Initialisation objet utilisateur vide
$utilisateur = new Utilisateur([
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'motDePasse' => ''
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération des données
    $utilisateur = new Utilisateur([
        'nom' => $_POST['nom'] ?? '',
        'prenom' => $_POST['prenom'] ?? '',
        'email' => $_POST['email'] ?? '',
        'motDePasse' => $_POST['motDePasse'] ?? ''
    ]);

    $validation_ok = $utilisateur->validerInscription($_POST['confirmationMotDePasse'] ?? null);

    if ($validation_ok) {
        $inscriptionOK = UtilisateurDAO::inscrire(
            $utilisateur,
            $_POST['confirmationMotDePasse'] ?? null
        );

        if ($inscriptionOK) {
            $_SESSION['message_succes'] = "Inscription réussie. Vous pouvez maintenant vous connecter.";
            header("Location: connexion.php");
            exit;
        }
    }
}
?>

<?php if (!empty($utilisateur->erreurs)): ?>
    <div class="erreurs">
        <?php foreach ($utilisateur->erreurs as $champ => $message): ?>
            <p class="erreur">❌ <?= htmlspecialchars($message) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<section id="formulaire-profil">
    <h2>Formulaire d'inscription</h2>

    <form method="POST">

        <label>Nom</label><br>
        <input
            type="text"
            name="nom"
            value="<?= htmlspecialchars($utilisateur->obtenir('nom') ?? '') ?>"
        >
        <br><br>

        <label>Prénom</label><br>
        <input
            type="text"
            name="prenom"
            value="<?= htmlspecialchars($utilisateur->obtenir('prenom') ?? '') ?>"
        >
        <br><br>

        <label>Email</label><br>
        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($utilisateur->obtenir('email') ?? '') ?>"
        >
        <br><br>

        <label>Mot de passe</label><br>
        <input type="password" name="motDePasse">
        <br><br>

        <label>Confirmer mot de passe</label><br>
        <input type="password" name="confirmationMotDePasse">
        <br><br>

        <div class="btn-container">
            <button type="submit" class="btn-submit">S’inscrire</button>
            <a href="index.php" class="bouton-annuler">Annuler</a>
        </div>

        <p>Déjà un compte ?</p>
        <a href="connexion.php" class="bouton-annuler">Se connecter</a>

    </form>
</section>

<?php require_once __DIR__ . "/footer.php"; ?>
