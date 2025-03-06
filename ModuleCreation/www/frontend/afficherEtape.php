<?php
require_once "../backend/creation/connectionDB.php";

if (isset($_GET["idProcessus"])) {
    $idProcessus = $_GET["idProcessus"];

    $sql = "SELECT * FROM Etapes WHERE idProcessus = ? ORDER BY ordre ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idProcessus]);
    $etapes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<h2>Étapes du processus</h2>
<?php if (!empty($etapes)) : ?>
    <ul>
        <?php foreach ($etapes as $e) : ?>
            <li>
                <p><?= htmlspecialchars($e["texte"]) ?></p>
                <?php if (!empty($e["image"])) : ?>
                    <img src=<?= htmlspecialchars($e['image']) ?> width="100">
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else : ?>
    <p>Aucune étape pour ce processus.</p>
<?php endif; ?>
