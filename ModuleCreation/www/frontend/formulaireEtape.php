<?php
require_once "../backend/creation/connectionDB.inc.php";

if (isset($_GET['idProcessus'])) {
    // Vérifier et valider l'ID
    $idProcessus = filter_input(INPUT_GET, 'idProcessus', FILTER_VALIDATE_INT);
    if ($idProcessus === false || $idProcessus === null) {
        die("ID de processus invalide.");
    }

    // Requête préparée sécurisée
    $sql = "SELECT nom FROM Processus WHERE idProcessus = :idProcessus";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['idProcessus' => $idProcessus]);

    // Récupération du résultat
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo "Nom du processus sélectionné : " . htmlspecialchars($result['nom']);
    } else {
        echo "Aucun processus trouvé pour cet ID.";
    }
} else {
    echo "Aucun processus sélectionné.";
}

// Récupérer la liste des processus pour les associer aux étapes
$stmt = $pdo->query("SELECT idProcessus, nom FROM Processus");
$processus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Creation Etape</title>
        <link rel="stylesheet" href="#">
        
        </script>
    </head>
    <body>
        <h2>Ajouter une étape</h2>
        <form action="../backend/creation/ajouterEtape.php" method="POST" enctype="multipart/form-data">
            <label for="processus">Sélectionner un processus :</label>
            <select name="idProcessus" required>
                <?php
                require_once "../backend/creation/connectionDB.php";
                $sql = "SELECT idProcessus, nom FROM Processus";
                $stmt = $pdo->query($sql);
                while ($processus = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$processus['idProcessus']}'>{$processus['nom']}</option>";
                }
                ?>
            </select>

            <label for="texte">Texte de l'étape :</label>
            <textarea name="texte" required></textarea>

            <label for="image">Image :</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit">Ajouter l'étape</button>
        </form>
   
    </body>
</html>