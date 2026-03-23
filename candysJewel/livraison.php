<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/modele/AdresseLivraison.php';

$panier = $_SESSION['panier'] ?? [];

if (empty($panier)) {
    header("Location: panier.php");
    exit;
}

if (!isset($_SESSION['utilisateur']) || empty($_SESSION['utilisateur']['id'])) {
    header("Location: connexion.php");
    exit;
}

$anciennesDonnees = $_SESSION['ancienne_livraison'] ?? [];

$adresseLivraison = new AdresseLivraison([
    'nom' => $anciennesDonnees['nom'] ?? ($_SESSION['utilisateur']['nom'] ?? ''),
    'prenom' => $anciennesDonnees['prenom'] ?? ($_SESSION['utilisateur']['prenom'] ?? ''),
    'email' => $anciennesDonnees['email'] ?? ($_SESSION['utilisateur']['email'] ?? ''),
    'telephone' => $anciennesDonnees['telephone'] ?? '',
    'adresse' => $anciennesDonnees['adresse'] ?? '',
    'appartement' => $anciennesDonnees['appartement'] ?? '',
    'ville' => $anciennesDonnees['ville'] ?? '',
    'province' => $anciennesDonnees['province'] ?? '',
    'code_postal' => $anciennesDonnees['code_postal'] ?? '',
    'pays' => $anciennesDonnees['pays'] ?? 'Canada',
    'instructions' => $anciennesDonnees['instructions'] ?? ''
]);

$erreursLivraison = $_SESSION['erreurs_livraison'] ?? [];
$erreurGlobale = $_SESSION['erreur_livraison_globale'] ?? null;

unset($_SESSION['ancienne_livraison'], $_SESSION['erreurs_livraison'], $_SESSION['erreur_livraison_globale']);
?>

<main class="page-livraison">
    <section class="conteneur-livraison">
        <h1>Adresse de livraison</h1>

        <?php if (!empty($erreurGlobale)): ?>
            <p class="message-erreur">
                <?= htmlspecialchars($erreurGlobale, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($erreursLivraison)): ?>
            <div class="erreurs">
                <?php foreach ($erreursLivraison as $message): ?>
                    <p class="message-erreur">
                        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="traiter_livraison.php" method="post" class="form-livraison">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            >

            <label>Nom</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($adresseLivraison->obtenir('nom') ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label>Prénom</label>
            <input type="text" name="prenom" value="<?= htmlspecialchars($adresseLivraison->obtenir('prenom') ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($adresseLivraison->obtenir('email') ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label>Téléphone</label>
            <input type="text" name="telephone" value="<?= htmlspecialchars($adresseLivraison->obtenir('telephone') ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label>Adresse</label>
            <input type="text" name="adresse" value="<?= htmlspecialchars($adresseLivraison->obtenir('adresse') ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label>Appartement</label>
            <input type="text" name="appartement" value="<?= htmlspecialchars($adresseLivraison->obtenir('appartement') ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <label>Ville</label>
            <input type="text" name="ville" value="<?= htmlspecialchars($adresseLivraison->obtenir('ville') ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label>Province</label>
            <input type="text" name="province" value="<?= htmlspecialchars($adresseLivraison->obtenir('province') ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label>Code postal</label>
            <input type="text" name="code_postal" value="<?= htmlspecialchars($adresseLivraison->obtenir('code_postal') ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label>Pays</label>
            <input type="text" name="pays" value="<?= htmlspecialchars($adresseLivraison->obtenir('pays') ?? 'Canada', ENT_QUOTES, 'UTF-8') ?>" required>

            <button type="submit" class="btn-commande">Continuer</button>
        </form>
    </section>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
