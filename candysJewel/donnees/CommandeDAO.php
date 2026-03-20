<?php

require_once __DIR__ . '/../modele/Commande.php';
require_once __DIR__ . '/../accesseur/Configuration.php';

class CommandeDAO
{
    public static function creerCommande(int $utilisateurId, array $panier, ?int $adresseLivraisonId = null): ?int
    {
        $pdo = Connexion::getInstance();
        $pdo->beginTransaction();

        try {
            $montantTotal = 0;

            foreach ($panier as $article) {
                $montantTotal += (float)$article['prix'] * (int)$article['quantite'];
            }

            $sqlCommande = "
                INSERT INTO commande (
                    utilisateur_id,
                    adresse_livraison_id,
                    statut,
                    montant_total,
                    date_creation
                ) VALUES (
                    :utilisateur_id,
                    :adresse_livraison_id,
                    :statut,
                    :montant_total,
                    NOW()
                )
            ";

            $requeteCommande = $pdo->prepare($sqlCommande);
            $requeteCommande->execute([
                ':utilisateur_id' => $utilisateurId,
                ':adresse_livraison_id' => $adresseLivraisonId,
                ':statut' => 'en_attente',
                ':montant_total' => $montantTotal
            ]);

            $commandeId = (int)$pdo->lastInsertId();

            $sqlLigne = "
                INSERT INTO ligne_commande (
                    commande_id,
                    bijou_id,
                    taille_id,
                    quantite,
                    prix_unitaire,
                    nom_bijou,
                    libelle_taille
                ) VALUES (
                    :commande_id,
                    :bijou_id,
                    :taille_id,
                    :quantite,
                    :prix_unitaire,
                    :nom_bijou,
                    :libelle_taille
                )
            ";

            $requeteLigne = $pdo->prepare($sqlLigne);

            foreach ($panier as $article) {
                $requeteLigne->execute([
                    ':commande_id' => $commandeId,
                    ':bijou_id' => (int)$article['bijou_id'],
                    ':taille_id' => !empty($article['taille_id']) ? (int)$article['taille_id'] : null,
                    ':quantite' => (int)$article['quantite'],
                    ':prix_unitaire' => (float)$article['prix'],
                    ':nom_bijou' => $article['nom'],
                    ':libelle_taille' => $article['taille'] ?? null
                ]);
            }

            $pdo->commit();
            return $commandeId;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            die("Erreur DAO commande : " . $e->getMessage());
        }
    }

    public static function trouverParId(int $id): ?Commande
    {
        $pdo = Connexion::getInstance();

        $sql = "SELECT * FROM commande WHERE id = :id";
        $requete = $pdo->prepare($sql);
        $requete->execute([':id' => $id]);

        $donnees = $requete->fetch(PDO::FETCH_ASSOC);

        if (!$donnees) {
            return null;
        }

        $commande = new Commande(
            (int)$donnees['id'],
            (int)$donnees['utilisateur_id'],
            isset($donnees['adresse_livraison_id']) ? (int)$donnees['adresse_livraison_id'] : null,
            $donnees['statut'],
            (float)$donnees['montant_total'],
            $donnees['stripe_session_id'] ?? null,
            $donnees['stripe_payment_intent_id'] ?? null,
            $donnees['date_creation'] ?? null,
            $donnees['date_paiement'] ?? null
        );

        $sqlLignes = "SELECT * FROM ligne_commande WHERE commande_id = :commande_id";
        $requeteLignes = $pdo->prepare($sqlLignes);
        $requeteLignes->execute([':commande_id' => $id]);

        $lignes = $requeteLignes->fetchAll(PDO::FETCH_ASSOC);

        foreach ($lignes as $ligne) {
            $commande->ajouterLigne($ligne);
        }

        return $commande;
    }

    public static function listerParUtilisateur(int $utilisateurId): array
    {
        $pdo = Connexion::getInstance();

        $sql = "
            SELECT *
            FROM commande
            WHERE utilisateur_id = :utilisateur_id
            ORDER BY date_creation DESC
        ";

        $requete = $pdo->prepare($sql);
        $requete->execute([
            ':utilisateur_id' => $utilisateurId
        ]);

        $resultats = $requete->fetchAll(PDO::FETCH_ASSOC);
        $commandes = [];

        foreach ($resultats as $donnees) {
            $commandes[] = new Commande(
                (int)$donnees['id'],
                (int)$donnees['utilisateur_id'],
                isset($donnees['adresse_livraison_id']) ? (int)$donnees['adresse_livraison_id'] : null,
                $donnees['statut'],
                (float)$donnees['montant_total'],
                $donnees['stripe_session_id'] ?? null,
                $donnees['stripe_payment_intent_id'] ?? null,
                $donnees['date_creation'] ?? null,
                $donnees['date_paiement'] ?? null
            );
        }

        return $commandes;
    }

    public static function mettreAJourStatut(
        int $commandeId,
        string $statut,
        ?string $stripeSessionId = null,
        ?string $stripePaymentIntentId = null
    ): bool {
        $pdo = Connexion::getInstance();

        $sql = "
            UPDATE commande
            SET statut = :statut,
                stripe_session_id = :stripe_session_id,
                stripe_payment_intent_id = :stripe_payment_intent_id,
                date_paiement = CASE
                    WHEN :statut = 'payee' THEN NOW()
                    ELSE date_paiement
                END
            WHERE id = :id
        ";

        $requete = $pdo->prepare($sql);

        return $requete->execute([
            ':statut' => $statut,
            ':stripe_session_id' => $stripeSessionId,
            ':stripe_payment_intent_id' => $stripePaymentIntentId,
            ':id' => $commandeId
        ]);
    }

    public static function decrementerStockCommande(int $commandeId): bool
    {
        $pdo = Connexion::getInstance();

        try {
            $pdo->beginTransaction();

            $sqlLignes = "
                SELECT bijou_id, taille_id, quantite
                FROM ligne_commande
                WHERE commande_id = :commande_id
            ";

            $requeteLignes = $pdo->prepare($sqlLignes);
            $requeteLignes->execute([':commande_id' => $commandeId]);
            $lignes = $requeteLignes->fetchAll(PDO::FETCH_ASSOC);

            if (empty($lignes)) {
                $pdo->rollBack();
                return false;
            }

            $sqlStock = "
                UPDATE variantes_bijoux
                SET stock = stock - :quantite
                WHERE bijou_id = :bijou_id
                  AND taille_id = :taille_id
                  AND stock >= :quantite
            ";

            $requeteStock = $pdo->prepare($sqlStock);

            foreach ($lignes as $ligne) {
                $requeteStock->execute([
                    ':bijou_id' => (int)$ligne['bijou_id'],
                    ':taille_id' => (int)$ligne['taille_id'],
                    ':quantite' => (int)$ligne['quantite']
                ]);

                if ($requeteStock->rowCount() === 0) {
                    $pdo->rollBack();
                    return false;
                }
            }

            $pdo->commit();
            return true;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return false;
        }
    }
}
