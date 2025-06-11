<?php if (!empty($datas)) : ?>
    <div class="col-12">
        <div class="card-header">
            <h3 class="card-title">
                Statistique du processus : 
                <?php echo htmlspecialchars($datas[0]['nomProcessus']); ?>
            </h3>
        </div>
        <div class="card mb-4">
            <div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID de l'assemblage</th>
                            <th>Nombre de mauvaise pioche</th>
                            <th>Durée</th>
                        </tr>
                    </thead>
                    <tbody id="table-processus">
                        <?php foreach ($datas as $AssemblageData) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($AssemblageData['idAssemblage']); ?></td>
                                <td><?php echo htmlspecialchars($AssemblageData['tauxErreur']); ?></td>
                                <td><?php echo htmlspecialchars($AssemblageData['dureeProcessus']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else : ?>
    <div class="alert alert-info">Aucune donnée à afficher pour ce processus.</div>
<?php endif; ?>
