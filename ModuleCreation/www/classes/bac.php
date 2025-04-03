<?php

class Bac
{
    private int $idBac; // le numéro de bac
    private string $contenance;

    public function __construct(int $idBac, string $contenance)
    {
        $this->idBac = $idBac;
        $this->contenance = $contenance;
    }

    public function getIdBac(): int
    {
        return $this->idBac;
    }

    public function getContenance(): string
    {
        return $this->contenance;
    }

    public function setContenance(string $contenance): void
    {
        if (empty($contenance)) {
            throw new InvalidArgumentException("La contenance ne peut pas être vide.");
        }
        if (!is_string($contenance)) {
            throw new InvalidArgumentException("La contenance doit être une chaîne de caractères.");
        }
        if ($this->contenance === $contenance) {
            return;
        }
        $this->contenance = $contenance;
    }
}
