<?php

class Configuration
{
    private static array $config = [];
    private static bool $loaded = false;

    public static function load(string $envPath): void
    {
        if (self::$loaded) {
            return;
        }

        if (!file_exists($envPath)) {
            throw new RuntimeException("Fichier .env introuvable.");
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            throw new RuntimeException("Impossible de lire le fichier .env.");
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $parts = explode('=', $line, 2);

            if (count($parts) !== 2) {
                continue;
            }

            $key = trim($parts[0]);
            $value = trim($parts[1]);

            $value = trim($value, "\"'");

            self::$config[$key] = $value;
        }

        self::$loaded = true;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return self::$config[$key] ?? $default;
    }
}

Configuration::load(__DIR__ . '/../.env');

class Connexion
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $usager = Configuration::get('DB_USER');
            $motdepasse = Configuration::get('DB_PASS');
            $hote = Configuration::get('DB_HOST', 'localhost');
            $base = Configuration::get('DB_NAME');
            $charset = Configuration::get('DB_CHARSET', 'utf8mb4');

            if (!$usager || !$base) {
                error_log('Configuration BD incomplète.');
                die('Une erreur de configuration est survenue.');
            }

            $dsn = "mysql:host={$hote};dbname={$base};charset={$charset}";

            try {
                self::$instance = new PDO($dsn, $usager, $motdepasse, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                error_log('Erreur de connexion PDO : ' . $e->getMessage());
                die('Une erreur technique est survenue. Veuillez réessayer plus tard.');
            }
        }

        return self::$instance;
    }
}

/*
|--------------------------------------------------------------------------
| Configuration Stripe
|--------------------------------------------------------------------------
*/

define('STRIPE_SECRET_KEY', Configuration::get('STRIPE_SECRET_KEY', ''));
define('STRIPE_PUBLIC_KEY', Configuration::get('STRIPE_PUBLIC_KEY', ''));

/*
|--------------------------------------------------------------------------
| Webhook secret
|--------------------------------------------------------------------------
*/

define('STRIPE_WEBHOOK_SECRET', Configuration::get('STRIPE_WEBHOOK_SECRET', ''));

/*
|--------------------------------------------------------------------------
| URL du site
|--------------------------------------------------------------------------
*/

define('BASE_URL', Configuration::get('BASE_URL', 'http://localhost'));
