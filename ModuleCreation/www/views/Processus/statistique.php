<div class="col-12">
    <div class="card-header">
        <h3 class="card-title">Statistique du Processus : <?php echo htmlspecialchars($datas[0]['nomProcessus']); ?></h3>
    </div>
    <div class="card mb-4">
        <div>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID du Processus</th>
                        <th>Exécutions</th>
                        <th>Réussites</th>
                        <th>Échecs</th>
                        <th>Taux de réussite (%)</th>
                        <th>Taux d'échec (%)</th>
                        <th>Durée</th>
                    </tr>
                </thead>
                <tbody id="table-processus">
                    <?php foreach ($datas as $processusData) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($processusData['idProcessus']); ?></td>
                            <td><?php echo htmlspecialchars($processusData['nombreExecutions']); ?></td>
                            <td><?php echo htmlspecialchars($processusData['nombreReussites']); ?></td>
                            <td><?php echo htmlspecialchars($processusData['nombreEchecs']); ?></td>
                            <td><?php echo number_format($processusData['tauxReussite'], PRECISION_POURCENTAGE); ?></td>
                            <td><?php echo number_format($processusData['tauxEchec'], PRECISION_POURCENTAGE); ?></td>
                            <td><?php echo htmlspecialchars($processusData['dureeProcessus']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
