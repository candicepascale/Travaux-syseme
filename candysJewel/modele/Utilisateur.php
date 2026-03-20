<?php

class Utilisateur
{
    public static $filtres = [
        'id'          => FILTER_VALIDATE_INT,
        'email'       => FILTER_SANITIZE_EMAIL,
        'motDePasse'  => FILTER_DEFAULT,
        'nom'         => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'prenom'      => FILTER_SANITIZE_FULL_SPECIAL_CHARS
    ];

    protected $id;
    protected $email;
    protected $motDePasse;
    protected $nom;
    protected $prenom;

    public $erreurs = [];

    public function __construct(array $tab = [])
    {
        $tab = filter_var_array($tab, self::$filtres);

        $this->id         = $tab['id'] ?? null;
        $this->email      = $tab['email'] ?? '';
        $this->motDePasse = $tab['motDePasse'] ?? '';
        $this->nom        = $tab['nom'] ?? '';
        $this->prenom     = $tab['prenom'] ?? '';
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

    public function validerConnexion(): bool
    {
        $this->erreurs = [];

        if (empty($this->email)) {
            $this->erreurs['email'] = "L'adresse email est obligatoire.";
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->erreurs['email'] = "Email invalide.";
        }

        if (empty($this->motDePasse)) {
            $this->erreurs['motDePasse'] = "Le mot de passe est obligatoire.";
        } elseif (strlen($this->motDePasse) < 6) {
            $this->ajouterErreur('motDePasse', "Le mot de passe doit contenir au moins 6 caractères.");
        }

        return empty($this->erreurs);
    }

    public function validerInscription(?string $confirmationMotDePasse = null): bool
    {
        $this->erreurs = [];

        if (empty($this->nom)) {
            $this->erreurs['nom'] = "Le nom est obligatoire.";
        }

        if (empty($this->prenom)) {
            $this->erreurs['prenom'] = "Le prénom est obligatoire.";
        }

        if (empty($this->email)) {
            $this->erreurs['email'] = "L'adresse email est obligatoire.";
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->erreurs['email'] = "Format d'email invalide.";
        }

        if (empty($this->motDePasse)) {
            $this->erreurs['motDePasse'] = "Le mot de passe est obligatoire.";
        } elseif (strlen($this->motDePasse) < 6) {
            $this->erreurs['motDePasse'] = "Le mot de passe doit contenir au moins 6 caractères.";
        }

        if ($confirmationMotDePasse !== null && $this->motDePasse !== $confirmationMotDePasse) {
            $this->erreurs['confirmationMotDePasse'] = "Les mots de passe ne correspondent pas.";
        }

        return empty($this->erreurs);
    }

    public function ajouterErreur(string $cle, string $msg)
    {
        $this->erreurs[$cle] = $msg;
    }

    public function verifierConnexionModele(?Utilisateur $utilisateurBD): bool
    {
        if (!$utilisateurBD) {
            $this->ajouterErreur('email', "Aucun compte trouvé avec cet email.");
            return false;
        }

        if (!password_verify($this->motDePasse, $utilisateurBD->obtenir('motDePasse'))) {
            $this->ajouterErreur('motDePasse', "Mot de passe incorrect.");
            return false;
        }

        // Connexion valide : copier les données utiles
        $this->id     = $utilisateurBD->obtenir('id');
        $this->nom    = $utilisateurBD->obtenir('nom');
        $this->prenom = $utilisateurBD->obtenir('prenom');
        $this->email  = $utilisateurBD->obtenir('email');

        return true;
    }
}
