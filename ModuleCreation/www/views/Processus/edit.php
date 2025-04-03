<div class="card col col-lg-5 mx-3 mx-lg-auto p-0">
	<div class="card-header">
		<h3 class="card-title">Éditer un processus</h3>
	</div>
	<div class="card-body">
		<form method="post" action="<?php $_SERVER['PHP_SELF']; ?>">
			<div class="form-group">
				<label for="nomProcessus">Titre</label>
				<input type="text" name="nomProcessus" class="form-control" id="nomProcessus" value="<?php echo $datas['nomProcessus']; ?>" />
			</div>
			<input type="hidden" name="id" value="<?php echo $datas['idProcessus']; ?>" />
			<input class="btn btn-primary" name="submit" type="submit" value="Envoyer" />
			<a class="btn btn-danger" href="<?php echo ROOT_PATH; ?>processus">Annuler</a>
		</form>
	</div>
</div>