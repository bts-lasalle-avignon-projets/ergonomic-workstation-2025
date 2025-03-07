<?php
class Processus {
    private int $id;  // La propriété $id est typée mais sera initialisée après insertion
    private string $nom;
    private string $dateCreation;

    public function __construct(string $nom) {
        $this->nom = $nom;
        $this->dateCreation = date('Y-m-d H:i:s');
    }

    public function getId(): int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function insererDansBDD(PDO $pdo): bool {
        $sql = "INSERT INTO Processus (nom, dateCreation) VALUES (:nom, :dateCreation)";
        $stmt = $pdo->prepare($sql);

        $result = $stmt->execute([
            'nom' => $this->nom,
            'dateCreation' => $this->dateCreation
        ]);

        if ($result) {
            $this->id = (int) $pdo->lastInsertId();  
            return true;
        }

        return false; 
    }
}

?>
