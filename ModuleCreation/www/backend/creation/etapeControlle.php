<?php
var_dump($_POST);
var_dump($_FILES);

require_once './connectionDB.inc.php';
require_once './class/Etape.class.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["idProcessus"], $_POST["texte"]))
{
    $idProcessus = (int) $_POST["idProcessus"];
    $texte = trim($_POST["texte"]);
    $ordre = 1; 

    $imagePath = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) 
    {
        $uploadDir = '../../images/';

        if (!is_dir($uploadDir)) 
        {
            mkdir($uploadDir, 0777, true);
        }

        $tmpName = $_FILES['image']['tmp_name'];
        $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION); 

        $newFileName = time() . "_" . uniqid() . "." . $fileExtension;
        $imagePath = $uploadDir . $newFileName;

        if (move_uploaded_file($tmpName, $imagePath)) 
        {
            echo "L'image a bien été déplacée.";
        } else 
        {
            echo "Erreur : Impossible de déplacer l'image.";
            $imagePath = null;
        }
    }

    // Création de l'objet Etape
    $etape = new Etape($idProcessus, $texte, $imagePath, $ordre);

    if ($etape->insererDansBDD($pdo)) 
    {
        echo "Étape ajoutée avec succès !";
    } 
    else 
    {
        echo "Erreur lors de l'ajout de l'étape.";
    }
} 
else 
{
    echo "Requête invalide.";
}
?>
