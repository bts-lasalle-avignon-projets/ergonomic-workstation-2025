<div class="container d-flex flex-column align-items-center">
	<?php foreach ($datas as $etape) : ?>
		<div class="card col-12 col-lg-6" style="margin-bottom: 20px; max-height: 600px;">
			<div class="card-header">
				<h3 class="card-title">Étape n° <?php echo htmlspecialchars($etape['numeroEtape']) ?></h3>
			</div>
			<div class="card-body">
				<h5>Description Étape</h5>
				<p><?php echo htmlspecialchars($etape['descriptionEtape']); ?></p>

				<h5>Bac</h5>
				<p><?php echo htmlspecialchars($etape['bac']); ?></p>

				<h5>Image</h5>
				<?php if (!empty($etape['image']['contenuBlob'])) : ?>
					<img src="data:<?php echo htmlspecialchars($etape['image']['typeMIME']); ?>;base64,<?php echo base64_encode($etape['image']['contenuBlob']); ?>" 
						alt="Image du processus" 
						class="img-fluid" 
						style="max-width: 100%; max-height: 300px; object-fit: contain;" />
				<?php else : ?>
					<p class="mt-3">Aucune image disponible.</p>
				<?php endif; ?>
			</div>
			<small>
				<a class="card-link text-primary" href="<?php echo ROOT_PATH; ?>etape/edit/<?php echo $etape['idEtape']; ?>">Modifier</a>
			</small>
		</div>
	<?php endforeach; ?>
</div>
