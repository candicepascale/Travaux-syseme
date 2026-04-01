<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/modele/Utilisateur.php";
require_once __DIR__ . "/donnees/UtilisateurDAO.php";

// Initialisation objet utilisateur vide
$utilisateur = new Utilisateur([
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'motDePasse' => ''
]);

$messageErreurGlobal = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfTokenFormulaire = $_POST['csrf_token'] ?? '';
    $csrfTokenSession = $_SESSION['csrf_token'] ?? '';

    if (
        empty($csrfTokenFormulaire) ||
        empty($csrfTokenSession) ||
        !hash_equals($csrfTokenSession, $csrfTokenFormulaire)
    ) {
        $messageErreurGlobal = "Requête invalide. Veuillez réessayer.";
    } else {
        $utilisateur = new Utilisateur([
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'motDePasse' => $_POST['motDePasse'] ?? ''
        ]);

        $confirmationMotDePasse = $_POST['confirmationMotDePasse'] ?? null;

        $validation_ok = $utilisateur->validerInscription($confirmationMotDePasse);

        if ($validation_ok) {
            $inscriptionOK = UtilisateurDAO::inscrire(
                $utilisateur,
                $confirmationMotDePasse
            );

            if ($inscriptionOK) {
                $_SESSION['message_succes'] = "Inscription réussie. Vous pouvez maintenant vous connecter.";
                header("Location: connexion.php");
                exit;
            } else {
                $messageErreurGlobal = "Une erreur est survenue lors de l'inscription.";
            }
        }
    }
}

require_once __DIR__ . "/header.php";
?>

<?php if (!empty($messageErreurGlobal)): ?>
    <div class="erreurs">
        <p class="erreur">❌ <?= htmlspecialchars($messageErreurGlobal, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
<?php endif; ?>

<?php if (!empty($utilisateur->erreurs)): ?>
    <div class="erreurs">
        <?php foreach ($utilisateur->erreurs as $champ => $message): ?>
            <p class="erreur">❌ <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<section id="formulaire-profil">
    <h2>Formulaire d'inscription</h2>

    <form method="POST">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
        >

        <label>Nom</label><br>
        <input
            type="text"
            name="nom"
            value="<?= htmlspecialchars($utilisateur->obtenir('nom') ?? '', ENT_QUOTES, 'UTF-8') ?>"
        >
        <br><br>

        <label>Prénom</label><br>
        <input
            type="text"
            name="prenom"
            value="<?= htmlspecialchars($utilisateur->obtenir('prenom') ?? '', ENT_QUOTES, 'UTF-8') ?>"
        >
        <br><br>

        <label>Email</label><br>
        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($utilisateur->obtenir('email') ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
