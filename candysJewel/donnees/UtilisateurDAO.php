<?php

require_once __DIR__ . "/../accesseur/Configuration.php";
require_once __DIR__ . "/../modele/Utilisateur.php";

class Accesseur
{
    public static $basededonnees = null;

    protected static function initialiser(): void
    {
        if (self::$basededonnees === null) {
            self::$basededonnees = Connexion::getInstance();
        }
    }
}

class UtilisateurDAO extends Accesseur
{
    public const SQL_LISTER_UTILISATEURS = "
        SELECT id, nom, prenom, email, mot_de_passe
        FROM utilisateur
        ORDER BY id DESC
    ";

    public const SQL_DETAIL_UTILISATEUR = "
        SELECT id, nom, prenom, email, mot_de_passe
        FROM utilisateur
        WHERE id = :id
        LIMIT 1
    ";

    public const SQL_TROUVER_PAR_EMAIL = "
        SELECT id, nom, prenom, email, mot_de_passe
        FROM utilisateur
        WHERE email = :email
        LIMIT 1
    ";

    public const SQL_AJOUTER_UTILISATEUR = "
        INSERT INTO utilisateur (nom, prenom, email, mot_de_passe)
        VALUES (:nom, :prenom, :email, :mot_de_passe)
    ";

    public static function listerUtilisateurs(): array
    {
        self::initialiser();

        $statement = self::$basededonnees->prepare(self::SQL_LISTER_UTILISATEURS);
        $statement->execute();

        $utilisateurs = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $utilisateurs[] = new Utilisateur([
                'id' => $row['id'],
                'nom' => $row['nom'],
                'prenom' => $row['prenom'],
                'email' => $row['email'],
                'motDePasse' => $row['mot_de_passe']
            ]);
        }

        return $utilisateurs;
    }

    public static function trouverParId(Utilisateur $utilisateur): ?Utilisateur
    {
        self::initialiser();

        $statement = self::$basededonnees->prepare(self::SQL_DETAIL_UTILISATEUR);
        $statement->bindValue(':id', $utilisateur->obtenir('id'), PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Utilisateur([
            'id' => $row['id'],
            'nom' => $row['nom'],
            'prenom' => $row['prenom'],
            'email' => $row['email'],
            'motDePasse' => $row['mot_de_passe']
        ]);
    }

    public static function trouverParEmail(Utilisateur $utilisateur): ?Utilisateur
    {
        self::initialiser();

        $statement = self::$basededonnees->prepare(self::SQL_TROUVER_PAR_EMAIL);
        $statement->bindValue(':email', $utilisateur->obtenir('email'));
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Utilisateur([
            'id' => $row['id'],
            'nom' => $row['nom'],
            'prenom' => $row['prenom'],
            'email' => $row['email'],
            'motDePasse' => $row['mot_de_passe']
        ]);
    }

    public static function ajouterUtilisateur(Utilisateur $utilisateur)
    {
        self::initialiser();

        $statement = self::$basededonnees->prepare(self::SQL_AJOUTER_UTILISATEUR);

        $statement->bindValue(':nom', $utilisateur->obtenir('nom'));
        $statement->bindValue(':prenom', $utilisateur->obtenir('prenom'));
        $statement->bindValue(':email', $utilisateur->obtenir('email'));
        $statement->bindValue(
            ':mot_de_passe',
            password_hash($utilisateur->obtenir('motDePasse'), PASSWORD_DEFAULT)
        );

        if ($statement->execute()) {
            return self::$basededonnees->lastInsertId();
        }

        return false;
    }

    public static function emailExiste(Utilisateur $utilisateur): bool
    {
        return self::trouverParEmail($utilisateur) !== null;
    }

    public static function inscrire(Utilisateur $utilisateur, ?string $confirmationMotDePasse = null): bool
    {
        if (!$utilisateur->validerInscription($confirmationMotDePasse)) {
            return false;
        }

        if (self::emailExiste($utilisateur)) {
            $utilisateur->ajouterErreur('email', "Cette adresse email est déjà utilisée.");
            return false;
        }

        $id = self::ajouterUtilisateur($utilisateur);

        if ($id === false) {
            $utilisateur->ajouterErreur('general', "Erreur lors de l'inscription.");
            return false;
        }

        $utilisateur->modifier('id', (int)$id);
        return true;
    }

    public static function verifierConnexion(Utilisateur $utilisateur): bool
    {
        self::initialiser();

        if (!$utilisateur->validerConnexion()) {
            return false;
        }

        $utilisateurBD = self::trouverParEmail($utilisateur);

        return $utilisateur->verifierConnexionModele($utilisateurBD);
    }
}
?>
