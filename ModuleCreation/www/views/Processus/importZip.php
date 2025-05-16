<div class="card col col-lg-5 mx-3 mx-lg-auto p-0">
	<div class="card-header">
		<h3 class="card-title">Importer un processus (.zip)</h3>
	</div>
	<div class="card-body">
		<form method="post" action="<?php $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
			<div class="form-group">
				<label for='fichier'>Archive ZIP :</label>
				<input type='file' name='fichier' accept='.zip' required>
			</div>
			<input class="btn btn-primary" name="submit" type="submit" value="Importer ZIP" />
			<a class="btn btn-danger" href="<?php echo ROOT_PATH; ?>processus">Annuler</a>
		</form>
	</div>
</div>
