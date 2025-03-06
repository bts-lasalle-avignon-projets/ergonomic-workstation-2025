<?php
class Processus {
    private $nom;

    public static function creerProcessus($pdo, $nom) {
        $stmt = $pdo->prepare("INSERT INTO Processus (nom) VALUES (:nom)");
        $stmt->execute(["nom" => $nom]);
        return $pdo->lastInsertId();
    }
}
?>
