<?php
// models/Categorie.php
declare(strict_types=1);

class Categorie implements JsonSerializable
{
    public function __construct(
        public int $id,
        public string $nom,
        public string $slug,
        public ?string $date_creation
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
