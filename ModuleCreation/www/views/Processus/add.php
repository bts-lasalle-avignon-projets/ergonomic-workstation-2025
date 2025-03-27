<div class="card col col-lg-5 mx-3 mx-lg-auto p-0">
	<div class="card-header">
		<h3 class="card-title">Ajouter processus</h3>
	</div>
	<div class="card-body">
		<form method="post" action="<?php $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
			<div class="form-group">
				<label for="title">Titre</label>
				<input type="text" name="title" class="form-control" id="title" required />
			</div>
			<div class="form-group">
				<label for='image'>Image :</label>
				<input type='file' name='image' accept='image/*'>
			</div>
			<input class="btn btn-primary" name="submit" type="submit" value="Submit" />
			<a class="btn btn-danger" href="<?php echo ROOT_PATH; ?>processus">Annuler</a>
		</form>
	</div>
</div>