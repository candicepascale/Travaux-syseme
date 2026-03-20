<?php

require_once __DIR__ . '/../modele/AdresseLivraison.php';
require_once __DIR__ . '/../accesseur/Configuration.php';

class AdresseLivraisonDAO
{
    public static function creer(AdresseLivraison $adresseLivraison): ?int
    {
        $pdo = Connexion::getInstance();

        $sql = "
            INSERT INTO adresse_livraison (
                utilisateur_id,
                nom,
                prenom,
                email,
                telephone,
                adresse,
                appartement,
                ville,
                province,
                code_postal,
                pays,
                date_creation
            ) VALUES (
                :utilisateur_id,
                :nom,
                :prenom,
                :email,
                :telephone,
                :adresse,
                :appartement,
                :ville,
                :province,
                :code_postal,
                :pays,
                NOW()
            )
        ";

        $requete = $pdo->prepare($sql);

        $succes = $requete->execute([
            ':utilisateur_id' => $adresseLivraison->obtenir('utilisateur_id'),
            ':nom' => $adresseLivraison->obtenir('nom'),
            ':prenom' => $adresseLivraison->obtenir('prenom'),
            ':email' => $adresseLivraison->obtenir('email'),
            ':telephone' => $adresseLivraison->obtenir('telephone'),
            ':adresse' => $adresseLivraison->obtenir('adresse'),
            ':appartement' => $adresseLivraison->obtenir('appartement') ?: null,
            ':ville' => $adresseLivraison->obtenir('ville'),
            ':province' => $adresseLivraison->obtenir('province'),
            ':code_postal' => $adresseLivraison->obtenir('code_postal'),
            ':pays' => $adresseLivraison->obtenir('pays')
        ]);

        if (!$succes) {
            return null;
        }

        return (int)$pdo->lastInsertId();
    }

    public static function trouverParId(int $id): ?AdresseLivraison
    {
        $pdo = Connexion::getInstance();

        $requete = $pdo->prepare("SELECT * FROM adresse_livraison WHERE id = :id");
        $requete->execute([':id' => $id]);

        $donnees = $requete->fetch(PDO::FETCH_ASSOC);

        if (!$donnees) {
            return null;
        }

        return new AdresseLivraison($donnees);
    }
}
