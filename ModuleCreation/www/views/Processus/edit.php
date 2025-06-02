<div class="card col col-lg-5 mx-3 mx-lg-auto p-0">
	<div class="card-header">
		<h3 class="card-title">Éditer un processus</h3>
	</div>
	<div class="card-body">
		<form method="post" action="<?php $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
			<div class="form-group">
				<label for="nomProcessus">Nom</label>
				<input type="text" name="nomProcessus" class="form-control" id="nomProcessus" required value="<?php echo htmlspecialchars($datas[0]['nomProcessus'])?>"/>
			</div>			
			<div class="form-group">
				<label for="descriptionProcessus">Description</label>
				<textarea class="form-control" name="descriptionProcessus" id="descriptionProcessus" rows="4" required><?php echo htmlspecialchars($datas[0]['descriptionProcessus'])?></textarea>
			</div>
			<div>
				<?php if (!empty($datas[1]['contenuBlob'])): ?>
					<div class="mb-2">
						<p>Image actuelle :</p>
							<img src="data:<?php echo htmlspecialchars($datas[1]['typeMIME']); ?>;base64,<?php echo base64_encode($datas[1]['contenuBlob']); ?>" 
							alt="<?php echo htmlspecialchars($datas[1]['nomFichier']); ?>" 
							class="img-fluid" style="max-height: 200px;">
					</div>
				<?php endif; ?>
			</div>
			<div class="form-group">
				<label for="image">Image :</label>
				<input type="file" name="image" accept="image/*">
			</div>
			<input class="btn btn-primary" name="submit" type="submit" value="Envoyer" />
			<a class="btn btn-danger" href="<?php echo ROOT_PATH; ?>processus">Annuler</a>
		</form>
	</div>
</div>