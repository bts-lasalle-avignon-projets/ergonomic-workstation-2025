<?php
require_once "../backend/creation/connectionDB.inc.php";

// Vérifier si un processus est sélectionné
$idProcessus = filter_input(INPUT_GET, 'idProcessus', FILTER_VALIDATE_INT);
$nomProcessus = null;

if ($idProcessus !== false && $idProcessus !== null) {
    $sql = "SELECT nom FROM Processus WHERE idProcessus = :idProcessus";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['idProcessus' => $idProcessus]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $nomProcessus = $result['nom'];
    }
}

// Récupérer la liste des processus pour le menu déroulant
$sql = "SELECT idProcessus, nom FROM Processus";
$stmt = $pdo->query($sql);
$processusList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Création d'une Étape</title>
</head>
<body>

<h2>Ajouter une Étape</h2>

<?php if ($nomProcessus) : ?>
    <p>Processus sélectionné : <strong><?= htmlspecialchars($nomProcessus) ?></strong></p>
<?php endif; ?>

<form action="../backend/creation/etapeControlle.php" method="POST" enctype="multipart/form-data">
    <label for="idProcessus">Sélectionner un processus :</label>
    <select name="idProcessus" required>
        <?php foreach ($processusList as $p) : ?>
            <option value="<?= htmlspecialchars($p['idProcessus']) ?>" 
                <?= ($idProcessus == $p['idProcessus']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="texte">Texte de l'étape :</label>
    <textarea name="texte" required></textarea>

    <label for="image">Image :</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit">Ajouter l'Étape</button>
</form>

</body>
</html>
