<!-- Partage/export.php -->
<div class="container mt-5">
    <h2>Export du processus en JSON</h2>
    <pre>
        <?php 
        // Vérification si la donnée 'json' existe dans $datas
        if (isset($datas['json']) && !empty($datas['json'])) {
            // Si elle existe et n'est pas vide, on l'affiche
            echo htmlspecialchars($datas['json']); 
        } else {
            // Si elle est vide ou n'existe pas
            echo "Aucune donnée à afficher.";
        }
        ?>
    </pre>
</div>
