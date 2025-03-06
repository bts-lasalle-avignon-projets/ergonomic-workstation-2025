<?php
if (isset($_GET['nom'])) {
    $nomProcessus = htmlspecialchars($_GET['nom']); // Récupérer le nom du processus et le sécuriser
    echo "Nom du processus sélectionné : " . $nomProcessus;
} else {
    echo "Aucun processus sélectionné.";
}
?>
