<?php
class Bac {
    private int $idBac;

    public function __construct(int $idBac, string $contenance) {
        $this->idBac = $idBac;
    }

    public function getIdBac(): int {
        return $this->idBac;
    }
}
?>