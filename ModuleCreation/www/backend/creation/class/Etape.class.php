<?php
class Etape {
    private int $id;
    private int $idProcessus;
    private string $texte;
    private ?string $image; // Peut être NULL
    private int $ordre;

    public function __construct(int $idProcessus, string $texte, ?string $image, int $ordre) {
        $this->idProcessus = $idProcessus;
        $this->texte = $texte;
        $this->image = $image;
        $this->ordre = $ordre;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getIdProcessus(): int {
        return $this->idProcessus;
    }

    public function getTexte(): string {
        return $this->texte;
    }

    public function getImage(): ?string {
        return $this->image;
    }

    public function getOrdre(): int {
        return $this->ordre;
    }

    public function insererDansBDD(PDO $pdo): bool {
        $sql = "INSERT INTO Etapes (idProcessus, texte, image, ordre) VALUES (:idProcessus, :texte, :image, :ordre)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'idProcessus' => $this->idProcessus,
            'texte' => $this->texte,
            'image' => $this->image,
            'ordre' => $this->ordre
        ]);
    }
}
?>
