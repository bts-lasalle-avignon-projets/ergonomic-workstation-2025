<div class="col col-lg-8 mt-3 mx-3 mx-lg-auto text-center">
    <h1>Processus : <?php echo htmlspecialchars($datas['nomProcessus']['nomProcessus']); ?></h1>
</div>

<div class="card col col-lg-5 mx-3 mb-3 mx-lg-auto p-0">
    <div class="card-header">
        <h3 class="card-title">Modifier étape n° <?php echo htmlspecialchars($datas['numeroEtape']); ?></h3>
    </div>
    
    <form class="p-3" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nomEtape">Nom de l'étape</label>
            <input type="text" name="nomEtape" class="form-control" id="nomEtape" required
                   value="<?php echo htmlspecialchars($datas['nomEtape'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="descriptionEtape">Description de l'étape</label>
            <textarea name="descriptionEtape" class="form-control" id="descriptionEtape" rows="4" required><?php echo htmlspecialchars($datas['descriptionEtape'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="contenance">Contenance du bac</label>
            <input type="text" name="contenance" class="form-control" id="contenance" required
                   value="<?php echo htmlspecialchars($datas['contenance'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="numeroBac">Numéro du bac</label>
            <input type="text" name="numeroBac" class="form-control" id="numeroBac" required
                   value="<?php echo htmlspecialchars($datas['numeroBac'] ?? ''); ?>">
        </div>
        <div>
            <?php if (!empty($datas['imageBase64'])): ?>
                <div class="mb-2">
                    <p>Image actuelle :</p>
                    <img src="<?php echo $datas['imageBase64']; ?>" alt="<?php echo htmlspecialchars($datas['nomImage']); ?>" class="img-fluid" style="max-height: 200px;">
                </div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for='image'>Image :</label>
            <input type='file' name='image' accept='image/*'>
        </div>

        <button type="submit" class="btn btn-primary">Modifier étape</button>
    </form>
</div>
