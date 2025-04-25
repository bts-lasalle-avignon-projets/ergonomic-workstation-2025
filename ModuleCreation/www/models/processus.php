<?php

class ProcessusModel extends Model
{
	public function index()
	{
		$this->query("
			SELECT p.idProcessus, p.nomProcessus, p.dateCreation, p.idImage
			FROM Processus p
			ORDER BY p.dateCreation
		");

		$processus = $this->getResults();

		foreach ($processus as &$p) {
			if (!empty($p['idImage'])) {  // Vérifier si une image est associée
				$this->query("SELECT contenuBlob, typeMIME FROM Image WHERE idImage = :idImage LIMIT 1");
				$this->bind(':idImage', $p['idImage']);
				$imageData = $this->getResult();
				$p['image'] = $imageData ? $imageData : null;
			} else {
				$p['image'] = null; // Aucune image associée
			}
		}

		return $processus;
	}

	public function add()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
			$nomProcessus = trim($_POST['nomProcessus']);

			if (empty($nomProcessus)) {
				Message::afficher("Le nom est requis !", "erreur");
				return;
			}

			try {
				$idImage = null;

				if (!empty($_FILES['image']['name'])) {
					$idImage = $this->ajouterImage(); // Ajoute l'image et récupère son ID
				}

				// Insérer le processus avec idImage (NULL si pas d'image)
				$this->query("INSERT INTO Processus (nomProcessus, idImage) VALUES (:nomProcessus, :idImage)");
				$this->bind(':nomProcessus', $nomProcessus);
				$this->bind(':idImage', $idImage, PDO::PARAM_INT);
				$this->execute();

				Message::afficher("Processus ajouté avec succès !", "success");
			} catch (PDOException $e) {
				Message::afficher("Erreur lors de l'insertion : " . $e->getMessage(), "erreur");
			}
		}
	}

	private function ajouterImage()
	{
		$nomFichier = $_FILES['image']['name'];
		$typeMIME = $_FILES['image']['type'];
		$tailleImage = $_FILES['image']['size'];
		$contenuBlob = file_get_contents($_FILES['image']['tmp_name']);

		$typesAutorises = ['image/jpeg', 'image/png', 'image/webp'];
		if (!in_array($typeMIME, $typesAutorises)) {
			Message::afficher("Format d'image non autorisé !", "erreur");
			return null;
		}

		$this->query("INSERT INTO Image (nomFichier, typeMIME, contenuBlob, tailleImage) 
					VALUES (:nomFichier, :typeMIME, :contenuBlob, :tailleImage)");
		$this->bind(':nomFichier', $nomFichier);
		$this->bind(':typeMIME', $typeMIME);
		$this->bind(':contenuBlob', $contenuBlob, PDO::PARAM_LOB);
		$this->bind(':tailleImage', $tailleImage, PDO::PARAM_INT);
		$this->execute();

		$this->query("SELECT idImage FROM Image WHERE nomFichier = :nomFichier ORDER BY idImage DESC LIMIT 1");
		$this->bind(':nomFichier', $nomFichier);
		$image = $this->getResult();

		return $image['idImage'] ?? null;
	}

	public function edit() {}

	public function delete() {}
}
