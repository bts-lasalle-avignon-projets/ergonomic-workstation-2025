<?php
require_once './connectionDB.inc.php';
require_once './class/Processus.class.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["nom"])) {
    $nom = trim($_POST["nom"]);

    if (!empty($nom)) 
    {
        if (!isset($pdo)) 
        {
            die("Erreur : connexion à la base de données non disponible.");
        }
        try 
        {
            $processus = new Processus($nom);
            if ($processus->insererDansBDD($pdo)) 
            {
                echo "Processus ajouté avec succès ! (ID : " . $processus->getId() . ")";
            } else 
            {
                echo "Erreur lors de l'ajout du processus.";
            }
        } 
        catch (Exception $e) 
        {
            echo "Erreur SQL : " . $e->getMessage();
        }
    } else {
        echo "Le nom du processus ne peut pas être vide.";
    }
} else {
    echo "Requête invalide.";
}
?>
