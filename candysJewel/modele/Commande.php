<?php

class Commande
{
    protected ?int $id;
    protected int $utilisateur_id;
    protected ?int $adresse_livraison_id;
    protected string $statut;
    protected float $montant_total;
    protected ?string $stripe_session_id;
    protected ?string $stripe_payment_intent_id;
    protected ?string $date_creation;
    protected ?string $date_paiement;
    protected array $lignes;

    public function __construct(
        ?int $id = null,
        int $utilisateur_id = 0,
        ?int $adresse_livraison_id = null,
        string $statut = 'en_attente',
        float $montant_total = 0.00,
        ?string $stripe_session_id = null,
        ?string $stripe_payment_intent_id = null,
        ?string $date_creation = null,
        ?string $date_paiement = null,
        array $lignes = []
    ) {
        $this->id = $id;
        $this->utilisateur_id = $utilisateur_id;
        $this->adresse_livraison_id = $adresse_livraison_id;
        $this->statut = $statut;
        $this->montant_total = $montant_total;
        $this->stripe_session_id = $stripe_session_id;
        $this->stripe_payment_intent_id = $stripe_payment_intent_id;
        $this->date_creation = $date_creation;
        $this->date_paiement = $date_paiement;
        $this->lignes = $lignes;
    }

    public function obtenir(string $propriete)
    {
        return $this->$propriete ?? null;
    }

    public function assigner(string $propriete, $valeur): void
    {
        $this->$propriete = $valeur;
    }

    public function ajouterLigne(array $ligne): void
    {
        $this->lignes[] = $ligne;
    }

    public function obtenirLignes(): array
    {
        return $this->lignes;
    }
}
