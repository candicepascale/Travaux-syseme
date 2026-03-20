<?php

class AdresseLivraison
{
    protected ?int $id;
    protected int $utilisateur_id;
    protected string $nom;
    protected string $prenom;
    protected string $email;
    protected string $telephone;
    protected string $adresse;
    protected ?string $appartement;
    protected string $ville;
    protected string $province;
    protected string $code_postal;
    protected string $pays;
    public array $erreurs = [];

    public function __construct(array $donnees = [])
    {
        $this->id = $donnees['id'] ?? null;
        $this->utilisateur_id = isset($donnees['utilisateur_id']) ? (int)$donnees['utilisateur_id'] : 0;
        $this->nom = trim($donnees['nom'] ?? '');
        $this->prenom = trim($donnees['prenom'] ?? '');
        $this->email = trim($donnees['email'] ?? '');
        $this->telephone = trim($donnees['telephone'] ?? '');
        $this->adresse = trim($donnees['adresse'] ?? '');
        $this->appartement = trim($donnees['appartement'] ?? '');
        $this->ville = trim($donnees['ville'] ?? '');
        $this->province = trim($donnees['province'] ?? '');
        $this->code_postal = trim($donnees['code_postal'] ?? '');
        $this->pays = trim($donnees['pays'] ?? 'Canada');
    }

    public function obtenir(string $propriete)
    {
        return $this->$propriete ?? null;
    }

    public function assigner(string $propriete, $valeur): void
    {
        $this->$propriete = $valeur;
    }

    public function estValide(): bool
    {
        $this->erreurs = [];

        if ($this->nom === '') {
            $this->erreurs['nom'] = 'Le nom est obligatoire.';
        }

        if ($this->prenom === '') {
            $this->erreurs['prenom'] = 'Le prénom est obligatoire.';
        }

        if ($this->email === '' || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->erreurs['email'] = 'Email invalide.';
        }

        if ($this->telephone === '') {
            $this->erreurs['telephone'] = 'Le téléphone est obligatoire.';
        }

        if ($this->adresse === '') {
            $this->erreurs['adresse'] = 'L’adresse est obligatoire.';
        }

        if ($this->ville === '') {
            $this->erreurs['ville'] = 'La ville est obligatoire.';
        }

        if ($this->province === '') {
            $this->erreurs['province'] = 'La province est obligatoire.';
        }

        if ($this->code_postal === '') {
            $this->erreurs['code_postal'] = 'Le code postal est obligatoire.';
        }

        if ($this->pays === '') {
            $this->erreurs['pays'] = 'Le pays est obligatoire.';
        }

        return empty($this->erreurs);
    }
}
