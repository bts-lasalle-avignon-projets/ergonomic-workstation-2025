<div class="col col-lg-8 mt-5 mx-3 mx-lg-auto text-center">
    <h1>Processus : <?php echo htmlspecialchars($datas['nomProcessus']['nomProcessus']); ?></h1>
</div>


<div class="card col col-lg-5 mx-3 mx-lg-auto p-0">
    <div class="card-header">
        <h3 class="card-title">Ajouter étape n° <?php echo htmlspecialchars($datas['numeroEtape']['numeroEtape']); ?></h3>
    </div>
    <form class="p-3" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nomEtape">Nom de l'étape</label>
            <input type="text" name="nomEtape" class="form-control" id="nomEtape" required>
        </div>

        <div class="form-group">
            <label for="descriptionEtape">Description de l'étape</label>
            <textarea name="descriptionEtape" class="form-control" id="descriptionEtape" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="contenance">Contenance du bac</label>
            <input type="text" name="contenance" class="form-control" id="contenance" required>
        </div>

        <div class="form-group">
            <label for="numeroBac">Numéro du bac</label>
            <input type="text" name="numeroBac" class="form-control" id="numeroBac" required>
        </div>
        <div class="form-group">
            <label for='image'>Image :</label>
            <input type='file' name='image' accept='image/*'>
        </div>

        <button type="submit" class="btn btn-primary">Créer l'étape</button>
    </form>
</div>