<?php
session_start();

// URL de redirection par défaut si aucun paramètre
$redirectUrl = isset($_GET['redirect']) ? $_GET['redirect'] : '/index.php';

// Si formulaire POST → déconnexion
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    header("Location: /index.php");
    exit();
}

require 'header.php';
?>

<main class="logout-container">
    <div class="logout-box">
        <h2><?= _("Se déconnecter") ?></h2>

        <p><?= _("Voulez-vous vraiment vous déconnecter ?") ?></p>

        <div class="logout-buttons">

            <form method="POST">
                <button type="submit" class="btn-logout-confirm">
                    <?= _("Oui") ?>
                </button>
            </form>

            <a href="<?= htmlspecialchars($redirectUrl) ?>" class="btn-logout-cancel">
                <?= _("Non") ?>
            </a>

        </div>
    </div>
</main>

<?php
require '/footer.php';
?>
