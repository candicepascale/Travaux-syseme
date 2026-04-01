<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$redirectUrl = $_GET['redirect'] ?? 'index.php';

if (!is_string($redirectUrl) || $redirectUrl === '') {
    $redirectUrl = 'index.php';
}

/*
|--------------------------------------------------------------------------
| Sécuriser la redirection : autoriser seulement des chemins internes simples
|--------------------------------------------------------------------------
*/
if (
    str_starts_with($redirectUrl, 'http://') ||
    str_starts_with($redirectUrl, 'https://') ||
    str_contains($redirectUrl, "\r") ||
    str_contains($redirectUrl, "\n")
) {
    $redirectUrl = 'index.php';
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $csrfTokenFormulaire = $_POST['csrf_token'] ?? '';
    $csrfTokenSession = $_SESSION['csrf_token'] ?? '';

    if (
        empty($csrfTokenFormulaire) ||
        empty($csrfTokenSession) ||
        !hash_equals($csrfTokenSession, $csrfTokenFormulaire)
    ) {
        $_SESSION['message_deconnexion'] = "Requête invalide.";
        header("Location: deconnexion.php");
        exit;
    }

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

    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/header.php';
?>

<main class="logout-container">
    <div class="logout-box">
        <h2><?= _("Se déconnecter") ?></h2>

        <p><?= _("Voulez-vous vraiment vous déconnecter ?") ?></p>

        <?php if (!empty($_SESSION['message_deconnexion'])): ?>
            <p class="message-erreur">
                <?= htmlspecialchars($_SESSION['message_deconnexion'], ENT_QUOTES, 'UTF-8') ?>
            </p>
            <?php unset($_SESSION['message_deconnexion']); ?>
        <?php endif; ?>

        <div class="logout-buttons">
            <form method="POST">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >

                <button type="submit" class="btn-logout-confirm">
                    <?= _("Oui") ?>
                </button>
            </form>

            <a href="<?= htmlspecialchars($redirectUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn-logout-cancel">
                <?= _("Non") ?>
            </a>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/footer.php';
?>
