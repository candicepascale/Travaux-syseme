<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/accesseur/Configuration.php';
require_once __DIR__ . '/donnees/CommandeDAO.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$payload = file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

if ($payload === false || empty($sigHeader)) {
    error_log('Webhook Stripe invalide : payload ou signature manquant.');
    http_response_code(400);
    exit('Bad request');
}

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sigHeader,
        STRIPE_WEBHOOK_SECRET
    );
} catch (\UnexpectedValueException $e) {
    error_log('Webhook Stripe payload invalide : ' . $e->getMessage());
    http_response_code(400);
    exit('Invalid payload');
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    error_log('Webhook Stripe signature invalide : ' . $e->getMessage());
    http_response_code(400);
    exit('Invalid signature');
} catch (\Exception $e) {
    error_log('Erreur inattendue webhook Stripe : ' . $e->getMessage());
    http_response_code(400);
    exit('Webhook error');
}

/*
|--------------------------------------------------------------------------
| Sécurité : ignorer les événements non-live si on est en production
|--------------------------------------------------------------------------
*/
if (!isset($event->livemode) || $event->livemode !== true) {
    error_log('Webhook Stripe ignoré : événement non-live reçu.');
    http_response_code(200);
    exit('Ignored');
}

/*
|--------------------------------------------------------------------------
| Ne traiter que l’événement attendu
|--------------------------------------------------------------------------
*/
if ($event->type !== 'checkout.session.completed') {
    http_response_code(200);
    exit('Ignored');
}

$session = $event->data->object ?? null;

if (!$session) {
    error_log('Webhook Stripe : session absente dans l’événement.');
    http_response_code(200);
    exit('Ignored');
}

$commandeId = $session->metadata->commande_id ?? $session->client_reference_id ?? null;

if (!$commandeId) {
    error_log('Webhook Stripe : commande_id manquant.');
    http_response_code(200);
    exit('Ignored');
}

$commandeId = (int) $commandeId;

if ($commandeId <= 0) {
    error_log('Webhook Stripe : commande_id invalide.');
    http_response_code(200);
    exit('Ignored');
}

$commande = CommandeDAO::trouverParId($commandeId);

if (!$commande) {
    error_log("Webhook Stripe : commande introuvable pour commande_id={$commandeId}");
    http_response_code(200);
    exit('Ignored');
}

/*
|--------------------------------------------------------------------------
| Éviter les doubles traitements
|--------------------------------------------------------------------------
*/
if ($commande->obtenir('statut') === 'payee') {
    http_response_code(200);
    exit('Already processed');
}

$montantAttendu = (int) round(((float) $commande->obtenir('montant_total')) * 100);
$montantPaye = (int) ($session->amount_total ?? 0);
$currency = strtolower((string) ($session->currency ?? ''));
$paymentIntent = $session->payment_intent ?? null;
$sessionId = $session->id ?? null;

/*
|--------------------------------------------------------------------------
| Vérification minimale de la session Stripe
|--------------------------------------------------------------------------
*/
if (empty($sessionId)) {
    error_log("Webhook Stripe : session_id manquant pour commande_id={$commandeId}");
    http_response_code(200);
    exit('Ignored');
}

/*
|--------------------------------------------------------------------------
| Vérification montant + devise
|--------------------------------------------------------------------------
*/
if ($montantPaye !== $montantAttendu || $currency !== 'cad') {
    error_log("Webhook Stripe : mismatch montant/devise pour commande_id={$commandeId}");

    CommandeDAO::mettreAJourStatut(
        $commandeId,
        'failed',
        $sessionId,
        $paymentIntent
    );

    http_response_code(200);
    exit('Mismatch');
}

/*
|--------------------------------------------------------------------------
| Décrémenter le stock AVANT de confirmer la commande
|--------------------------------------------------------------------------
*/
$stockMisAJour = CommandeDAO::decrementerStockCommande($commandeId);

if (!$stockMisAJour) {
    error_log("Webhook Stripe : échec décrémentation stock pour commande_id={$commandeId}");

    CommandeDAO::mettreAJourStatut(
        $commandeId,
        'failed',
        $sessionId,
        $paymentIntent
    );

    http_response_code(200);
    exit('Stock error');
}

/*
|--------------------------------------------------------------------------
| Paiement confirmé
|--------------------------------------------------------------------------
*/
$statutMisAJour = CommandeDAO::mettreAJourStatut(
    $commandeId,
    'payee',
    $sessionId,
    $paymentIntent
);

if (!$statutMisAJour) {
    error_log("Webhook Stripe : échec mise à jour statut payee pour commande_id={$commandeId}");
    http_response_code(200);
    exit('Update error');
}

http_response_code(200);
echo 'ok';
