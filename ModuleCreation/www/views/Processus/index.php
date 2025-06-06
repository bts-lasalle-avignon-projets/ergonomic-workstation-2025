<div class="col-12">
	<a class="btn btn-primary btn-share mt-0" href="<?php echo ROOT_PATH; ?>processus/add">Créer</a>
	<a class="btn btn-info btn-share mt-0" href="<?php echo ROOT_PATH; ?>processus/importZip">Importer</a>
	<?php foreach ($datas as $item) : ?>
		<div class="card mb-4">
			<h3 class="card-header"><?php echo $item['nomProcessus']; ?></h3>
			<div class="card-body">
				<div>
					<small class="card-subtitle"><?php echo $item['dateCreation']; ?></small>
				</div>
				<div>
					<small class="card-subtitle"><?php echo $item['descriptionProcessus']; ?></small>
				</div>
				<hr />
				<!-- Affichage de l'image si elle existe -->
				<?php if (!empty($item['image']['contenuBlob'])) : ?>
					<img src="data:<?php echo htmlspecialchars($item['image']['typeMIME']); ?>;base64,<?php echo base64_encode($item['image']['contenuBlob']); ?>" alt="Image du processus" class="img-fluid" />
				<?php else : ?>
					<p>Aucune image disponible.</p>
				<?php endif; ?>
				<hr />
				<small class="card-subtitle">
					<a class="card-link text-success" href="<?php echo ROOT_PATH; ?>etape/add/<?php echo $item['idProcessus']; ?>">Éditer</a>
					<a class="card-link text-warning" href="<?php echo ROOT_PATH; ?>processus/view/<?php echo $item['idProcessus']; ?>">Voir</a>
					<a class="card-link text-primary" href="<?php echo ROOT_PATH; ?>processus/edit/<?php echo $item['idProcessus']; ?>">Modifier</a>
					<a class="card-link text-danger" href="<?php echo ROOT_PATH; ?>processus/delete/<?php echo $item['idProcessus']; ?>">Supprimer</a>
					<a class="card-link text-info" href="<?php echo ROOT_PATH; ?>processus/exportZip/<?php echo $item['idProcessus']; ?>">Exporter</a>
					<a class="card-link text-secondary" href="<?php echo ROOT_PATH; ?>processus/statistique/<?php echo $item['idProcessus']; ?>">Statistique</a>
				</small>
			</div>
		</div>
	<?php endforeach; ?>
</div>