<div class="col-12">
    <div class="card-header">
        <h3 class="card-title">Statistique du processus : <?php echo htmlspecialchars($datas[0]['nomProcessus']); ?></h3>
    </div>
    <div class="card mb-4">
        <div>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID du Assemblage</th>
                        <th>ID du Processus</th>
                        <th>Taux d'erreur (%)</th>
                        <th>Durée</th>
                    </tr>
                </thead>
                <tbody id="table-processus">
                    <?php foreach ($datas as $AssemblageData) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($AssemblageData['idAssemblage']); ?></td>
                            <td><?php echo htmlspecialchars($AssemblageData['idProcessus']); ?></td>
                            <td><?php echo number_format($AssemblageData['tauxErreurParMinute'], PRECISION_POURCENTAGE); ?></td>
                            <td><?php echo htmlspecialchars($AssemblageData['dureeProcessus']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
