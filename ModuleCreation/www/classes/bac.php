<?php
class Bac {
    private int $idBac;
    private string $contenance;

    public function __construct(int $idBac, string $contenance) {
        $this->idBac = $idBac;
        $this->contenance = $contenance;
    }

    public function getIdBac(): int {
        return $this->idBac;
    }

    public function getContenance(): string {
        return $this->contenance;
    }

    public function setContenance(string $contenance): void {
        $this->contenance = $contenance;
    }
}
?>