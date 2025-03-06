<!--Connexion à la base de données-->
<?php
$host = "localhost";
$dbname = "ergonomic_workstation";
$username = "ergoWork";  
$password = "password";  

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
