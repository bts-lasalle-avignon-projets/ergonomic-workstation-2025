<?php
require_once "./backend/creation/connectionDB.php"; // Inclure la connexion à la BDD

try {
    // Requête pour récupérer tous les processus
    $sql = "SELECT nom FROM Processus";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $processus = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer les résultats sous forme de tableau associatif
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<h2>Liste des Processus</h2>
<table border="1">
    <tr>
        <th>Nom du processus</th>
    </tr>
    <?php foreach ($processus as $p): ?>
        <tr>
            <td><a href="./backend/creation/traiterProcessus.php?nom=<?= urlencode($p['nom']) ?>"><?= htmlspecialchars($p['nom']) ?></a></td>
        </tr>
    <?php endforeach; ?>
</table>


