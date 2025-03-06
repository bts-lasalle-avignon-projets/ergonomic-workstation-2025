<?php
require_once './connectionDB.php';
require_once './class/Processus.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["nom"])) {
    $nom = trim($_POST["nom"]);

    if (!empty($nom)) {
        if (!isset($pdo)) {
            die("Erreur : connexion à la base de données non disponible.");
        }

        try {
            $idProcessus = Processus::creerProcessus($pdo, $nom);
            echo " Processus créé avec succès (ID : $idProcessus)";
        } catch (Exception $e) {
            echo " Erreur lors de la création du processus : " . $e->getMessage();
        }
    } else {
        echo "Le nom du processus ne peut pas être vide.";
    }
} else {
    echo " Requête invalide.";
}
?>
