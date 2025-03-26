<div class="col-12">
	<?php if (isset($_SESSION['is_logged_in'])) : ?>
		<a class="btn btn-primary btn-share mt-0" href="<?php echo ROOT_PATH; ?>processus/add">Ajouter processus</a>
	<?php else : ?>
		<p>Il faut être connecté pour gérer les processus !</p>
	<?php endif; ?>

	<?php foreach ($viewmodel as $item) : ?>
		<div class="card mb-4">
			<h3 class="card-header"><?php echo $item['title']; ?></h3>
			<div class="card-body">
				<small class="card-subtitle"><?php echo $item['create_date']; ?></small>
				<hr />
				<p class="card-text"><?php echo $item['body']; ?></p>
				<a class="card-link" href="<?php echo $item['link']; ?>" target="_blank">Un lien</a>
				<?php if (isset($_SESSION['is_logged_in'])) : ?>
					<a class="card-link text-success" href="<?php echo ROOT_PATH; ?>processus/edit/<?php echo $item['id']; ?>">Editer processus</a>
					<a class="card-link text-danger" href="<?php echo ROOT_PATH; ?>processus/delete/<?php echo $item['id']; ?>">Supprimer processus</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>

</div>