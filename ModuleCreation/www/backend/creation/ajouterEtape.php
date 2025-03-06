<?php
var_dump($_POST);
var_dump($_FILES);

require_once 'connectionDB.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["idProcessus"], $_POST["texte"])) {
    $idProcessus = $_POST["idProcessus"];
    $texte = trim($_POST["texte"]);
    $ordre = 1;

    // Gestion de l’upload d’image
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $tmpName = $_FILES['image']['tmp_name'];
        $name = basename($_FILES['image']['name']); // Sécuriser le nom du fichier
        $newFileName = time() . "_" . uniqid() . "." . $fileExtension;
        $uploadDir = '../../images/';


        // Vérification des chemins
        echo "Chemin temporaire : " . $tmpName . "<br>";
        echo "Chemin de destination : " . $uploadDir . $name . "<br>";

        // Déplacement du fichier
        $imagePath = $uploadDir . $newFileName;
        if (move_uploaded_file($tmpName, $imagePath)) {
            echo " L'image a bien été déplacée.";
        } else {
            echo " Erreur : Impossible de déplacer l'image. Vérifiez les permissions du dossier.";
            $imagePath = null;
        }
    } else {
        echo "Aucune image envoyée ou erreur lors de l'upload.";
    }

    // Insérer l’étape en base
    $sql = "INSERT INTO Etapes (idProcessus, texte, image, ordre) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idProcessus, $texte, $imagePath, $ordre]);

    echo " Étape ajoutée avec succès !";
} else {
    echo " Erreur : Données manquantes.";
}
?>
