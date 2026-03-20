<?php
session_start();

require_once __DIR__ . "/modele/Utilisateur.php";
require_once __DIR__ . "/donnees/UtilisateurDAO.php";

$utilisateur = new Utilisateur($_POST ?? []);
$messageErreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($utilisateur->validerConnexion()) {
        $connexionValide = UtilisateurDAO::verifierConnexion($utilisateur);

        if ($connexionValide) {
            session_regenerate_id(true);

            $_SESSION["utilisateur"] = [
                "id" => $utilisateur->obtenir("id"),
                "email" => $utilisateur->obtenir("email"),
                "nom" => $utilisateur->obtenir("nom"),
                "prenom" => $utilisateur->obtenir("prenom")
            ];

            header("Location: index.php");
            exit;
        } else {
            $messageErreur = "Email ou mot de passe incorrect.";
        }
    }
}

require_once __DIR__ . "/header.php";
?>

<main class="page-connexion">
    <section class="conteneur-connexion">
        <h1>Connexion</h1>

        <form method="POST" class="form-connexion" novalidate>
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars($utilisateur->obtenir('email') ?? '') ?>"
                required
            >
            <?php if (!empty($utilisateur->erreurs['email'])): ?>
                <p class="message-erreur">
                    <?= htmlspecialchars($utilisateur->erreurs['email']) ?>
                </p>
            <?php endif; ?>

            <label for="motDePasse">Mot de passe</label>
            <input
                type="password"
                id="motDePasse"
                name="motDePasse"
                required
            >
            <?php if (!empty($utilisateur->erreurs['motDePasse'])): ?>
                <p class="message-erreur">
                    <?= htmlspecialchars($utilisateur->erreurs['motDePasse']) ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($messageErreur)): ?>
                <p class="message-erreur">
                    <?= htmlspecialchars($messageErreur) ?>
                </p>
            <?php endif; ?>

            <div class="actions-connexion">
                <button type="submit" class="btn-connexion">Se connecter</button>
                <a href="inscription.php" class="btn-secondaire">Créer un compte</a>
            </div>
        </form>
    </section>
</main>

<?php require_once __DIR__ . "/footer.php"; ?>
