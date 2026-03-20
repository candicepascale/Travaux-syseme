<?php

require_once __DIR__ . "/../accesseur/Configuration.php";
require_once __DIR__ . "/../modele/Bijou.php";

class AccesseurBijou
{
    public static $basededonnees = null;

    protected static function initialiser(): void
    {
        if (self::$basededonnees === null) {
            self::$basededonnees = Connexion::getInstance();
        }
    }
}

class BijouDAO extends AccesseurBijou
{
    public const SQL_LISTER_BIJOUX = "
        SELECT id, categorie_id, nom, description, prix, materiau, pierre, poids, actif, date_creation
        FROM bijoux
        WHERE actif = 1
        ORDER BY date_creation DESC, id DESC
    ";

    public const SQL_DETAIL_BIJOU = "
        SELECT id, categorie_id, nom, description, prix, materiau, pierre, poids, actif, date_creation
        FROM bijoux
        WHERE id = :id
        LIMIT 1
    ";

    public const SQL_LISTER_BIJOUX_PAR_CATEGORIE = "
        SELECT id, categorie_id, nom, description, prix, materiau, pierre, poids, actif, date_creation
        FROM bijoux
        WHERE categorie_id = :categorie_id
          AND actif = 1
        ORDER BY date_creation DESC, id DESC
    ";

    public const SQL_IMAGES_PAR_BIJOU = "
        SELECT id, bijou_id, chemin_image, est_principale, texte_alternatif, date_creation
        FROM images_bijoux
        WHERE bijou_id = :bijou_id
        ORDER BY est_principale DESC, id ASC
    ";

    public const SQL_VARIANTES_PAR_BIJOU = "
        SELECT
            vb.id,
            vb.bijou_id,
            vb.taille_id,
            vb.stock,
            t.libelle,
            t.type_bijou
        FROM variantes_bijoux vb
        INNER JOIN tailles t ON vb.taille_id = t.id
        WHERE vb.bijou_id = :bijou_id
        ORDER BY t.id ASC
    ";

    public static function listerBijoux(): array
    {
        self::initialiser();

        $statement = self::$basededonnees->prepare(self::SQL_LISTER_BIJOUX);
        $statement->execute();

        $bijoux = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $bijou = new Bijou($row);
            $bijou->definirImages(self::listerImagesParBijou((int)$bijou->obtenir('id')));
            $bijou->definirVariantes(self::listerVariantesParBijou((int)$bijou->obtenir('id')));
            $bijoux[] = $bijou;
        }

        return $bijoux;
    }

    public static function listerBijouxParCategorie(int $categorieId): array
    {
        self::initialiser();

        $statement = self::$basededonnees->prepare(self::SQL_LISTER_BIJOUX_PAR_CATEGORIE);
        $statement->bindValue(':categorie_id', $categorieId, PDO::PARAM_INT);
        $statement->execute();

        $bijoux = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $bijou = new Bijou($row);
            $bijou->definirImages(self::listerImagesParBijou((int)$bijou->obtenir('id')));
            $bijou->definirVariantes(self::listerVariantesParBijou((int)$bijou->obtenir('id')));
            $bijoux[] = $bijou;
        }

        return $bijoux;
    }

    public static function trouverParId(int $id): ?Bijou
    {
        self::initialiser();

        $statement = self::$basededonnees->prepare(self::SQL_DETAIL_BIJOU);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $bijou = new Bijou($row);
        $bijou->definirImages(self::listerImagesParBijou($id));
        $bijou->definirVariantes(self::listerVariantesParBijou($id));

        return $bijou;
    }

    public static function listerImagesParBijou(int $bijouId): array
    {
        self::initialiser();

        $statement = self::$basededonnees->prepare(self::SQL_IMAGES_PAR_BIJOU);
        $statement->bindValue(':bijou_id', $bijouId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listerVariantesParBijou(int $bijouId): array
    {
        self::initialiser();

        $statement = self::$basededonnees->prepare(self::SQL_VARIANTES_PAR_BIJOU);
        $statement->bindValue(':bijou_id', $bijouId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
