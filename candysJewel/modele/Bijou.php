<?php

class Bijou
{
    public static $filtres = [
        'id'            => FILTER_VALIDATE_INT,
        'categorie_id'  => FILTER_VALIDATE_INT,
        'nom'           => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'description'   => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'prix'          => FILTER_VALIDATE_FLOAT,
        'materiau'      => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'pierre'        => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'poids'         => FILTER_VALIDATE_FLOAT,
        'actif'         => FILTER_VALIDATE_INT,
        'date_creation' => FILTER_DEFAULT
    ];

    protected $id;
    protected $categorie_id;
    protected $nom;
    protected $description;
    protected $prix;
    protected $materiau;
    protected $pierre;
    protected $poids;
    protected $actif;
    protected $date_creation;

    protected $images = [];
    protected $variantes = [];

    public $erreurs = [];

    public function __construct(array $tab = [])
    {
        $tab = filter_var_array($tab, self::$filtres);

        $this->id            = $tab['id'] ?? null;
        $this->categorie_id  = $tab['categorie_id'] ?? null;
        $this->nom           = $tab['nom'] ?? '';
        $this->description   = $tab['description'] ?? '';
        $this->prix          = $tab['prix'] ?? 0;
        $this->materiau      = $tab['materiau'] ?? '';
        $this->pierre        = $tab['pierre'] ?? '';
        $this->poids         = $tab['poids'] ?? 0;
        $this->actif         = $tab['actif'] ?? 1;
        $this->date_creation = $tab['date_creation'] ?? '';
    }

    public function obtenir(string $cle)
    {
        $vars = get_object_vars($this);
        return $vars[$cle] ?? null;
    }

    public function modifier(string $cle, $valeur): void
    {
        if (property_exists($this, $cle)) {
            $this->{$cle} = $valeur;
        }
    }

    public function definirImages(array $images): void
    {
        $this->images = $images;
    }

    public function definirVariantes(array $variantes): void
    {
        $this->variantes = $variantes;
    }

    public function obtenirImagePrincipale(): ?array
    {
        foreach ($this->images as $image) {
            if ((int)$image['est_principale'] === 1) {
                return $image;
            }
        }

        return $this->images[0] ?? null;
    }

    public function valider(): bool
    {
        $this->erreurs = [];

        if (empty($this->nom)) {
            $this->erreurs['nom'] = "Le nom du bijou est obligatoire.";
        }

        if (empty($this->description)) {
            $this->erreurs['description'] = "La description est obligatoire.";
        }

        if ($this->prix === false || $this->prix <= 0) {
            $this->erreurs['prix'] = "Le prix doit être supérieur à 0.";
        }

        if ($this->categorie_id === false || $this->categorie_id <= 0) {
            $this->erreurs['categorie_id'] = "La catégorie est obligatoire.";
        }

        if (empty($this->materiau)) {
            $this->erreurs['materiau'] = "Le matériau est obligatoire.";
        }

        if ($this->poids === false || $this->poids < 0) {
            $this->erreurs['poids'] = "Le poids doit être valide.";
        }

        return empty($this->erreurs);
    }
}
