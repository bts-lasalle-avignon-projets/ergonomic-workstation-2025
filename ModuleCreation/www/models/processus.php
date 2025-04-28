<?php

class ProcessusModel extends Model
{
	public function index()
	{
		$this->query("
			SELECT p.idProcessus, p.nomProcessus, p.dateCreation, p.idImage, p.descriptionProcessus
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
			$descriptionProcessus = trim($_POST['descriptionProcessus']);

			if (empty($nomProcessus)) {
				Message::afficher("Le nom est requis !", "erreur");
				return;
			}

			try {
				$idImage = null;

				if (!empty($_FILES['image']['name'])) {
					$idImage = $this->ajouterImage(); // Ajoute l'image et récupère son ID
				}

				$this->query("INSERT INTO Processus (nomProcessus, descriptionProcessus, idImage) VALUES (:nomProcessus, :descriptionProcessus, :idImage)");
				$this->bind(':nomProcessus', $nomProcessus);
				$this->bind(':descriptionProcessus', $descriptionProcessus);
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

	public function view() {}

	public function export()
	{
		$idProcessus = (int) $_GET['id'];

		$params = [":idProcessus" => $idProcessus];
		$processus = $this->fetchAllByQuery("SELECT * FROM Processus WHERE idProcessus = :idProcessus", $params);
		$etapes = $this->fetchAllByQuery("SELECT * FROM Etape WHERE idProcessus = :idProcessus", $params);
		$bacs = $this->fetchAllByQuery("SELECT Bac.numeroBac, Bac.contenance FROM Bac WHERE idProcessus = :idProcessus", $params);
		$images = $this->fetchAllByQuery("SELECT * FROM Image WHERE idImage IN (
                SELECT idImage FROM Processus WHERE idProcessus = :idProcessus
                UNION
                SELECT idImage FROM Etape WHERE idProcessus = :idProcessus
            )", $params);

		$imagesById = array_column($images, null, 'idImage');
		foreach ($imagesById as $id => $img) {
			$imagesById[$id] = [
				'nomFichier' => $img['nomFichier'],
				'typeMIME' => $img['typeMIME'],
				'tailleImage' => $img['tailleImage'],
				'contenu' => base64_encode($img['contenuBlob'])
			];
		}

		$bacsByNumero = array_column($bacs, null, 'numeroBac');

		$processusExport = $processus[0];
		if ($processusExport['idImage'] && isset($imagesById[$processusExport['idImage']])) {
			$processusExport['image'] = $imagesById[$processusExport['idImage']];
		}

		$processusExport['bacs'] = array_values($bacsByNumero);

		$etapesExport = array_map(function ($etape) use ($bacsByNumero, $imagesById) {
			if (isset($bacsByNumero[$etape['idBac']])) {
				$etape['bac'] = $bacsByNumero[$etape['idBac']];
			}

			if ($etape['idImage'] && isset($imagesById[$etape['idImage']])) {
				$etape['image'] = $imagesById[$etape['idImage']];
			}

			return $etape;
		}, $etapes);

		$processusExport['etapes'] = $etapesExport;

		return json_encode($processusExport, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	}

	public function import()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
			$jsonFile = $_FILES['fichier']['tmp_name'];
			$jsonContent = file_get_contents($jsonFile);

			$data = json_decode($jsonContent, true);

			if (!$data || !isset($data['nomProcessus'])) {
				Message::afficher("Le fichier n'est pas valide.", "erreur");
				return;
			}

		
				$idImage = null;
				if (isset($data['image'])) {
					$idImage = $this->insererImageDepuisJson($data['image']);
				}

				$this->query("INSERT INTO Processus (nomProcessus, idImage) VALUES (:nomProcessus, :idImage)");
				$this->bind(':nomProcessus', $data['nomProcessus']);
				$this->bind(':idImage', $idImage, PDO::PARAM_INT);
				$this->execute();

				$idProcessus = $this->getLastInsertId();

				$bacsMap = [];
				if (isset($data['bacs']) && is_array($data['bacs'])) {
					foreach ($data['bacs'] as $bac) {
						try {
							$this->query("INSERT INTO Bac (numeroBac, contenance, idProcessus) VALUES (:numeroBac, :contenance, :idProcessus)");
							$this->bind(':numeroBac', $bac['numeroBac']);
							$this->bind(':contenance', $bac['contenance']);
							$this->bind(':idProcessus', $idProcessus);
							$this->execute();
						} catch (PDOException $e) {
							echo "Erreur lors de l'insertion du bac : " . $e->getMessage();  // Afficher le message complet
						}

						$idBac = $this->getLastInsertId();
            			$bacsMap[$bac['numeroBac']] = $idBac;
					}
				}
				foreach ($data['etapes'] as $etape) {
					$idImageEtape = null;
					if (isset($etape['image'])) {
						$idImageEtape = $this->insererImageDepuisJson($etape['image']);
					}

					$numeroBac = $etape['bac']['numeroBac'] ?? null;
					$this->query("INSERT INTO Etape (nomEtape, descriptionProcessusEtape, idProcessus, idImage, idBac, numeroEtape) 
								VALUES (:nomEtape, :descriptionProcessusEtape, :idProcessus, :idImage, :idBac, :numeroEtape)");
					$this->bind(':nomEtape', $etape['nomEtape']);
					$this->bind(':descriptionProcessusEtape', $etape['descriptionProcessusEtape'] ?? '');
					$this->bind(':idProcessus', $idProcessus);
					$this->bind(':idImage', $idImageEtape, PDO::PARAM_INT);
					$this->bind(':idBac', $numeroBac, PDO::PARAM_INT);
					$this->bind(':numeroEtape', $etape['numeroEtape']);
					$this->execute();
				}
		}
	}

	private function insererImageDepuisJson($image)
	{
		$this->query("INSERT INTO Image (nomFichier, typeMIME, contenuBlob, tailleImage) 
					VALUES (:nomFichier, :typeMIME, :contenuBlob, :tailleImage)");

		$this->bind(':nomFichier', $image['nomFichier']);
		$this->bind(':typeMIME', $image['typeMIME']);
		$this->bind(':contenuBlob', base64_decode($image['contenu']), PDO::PARAM_LOB);
		$this->bind(':tailleImage', $image['tailleImage'], PDO::PARAM_INT);
		$this->execute();

		return $this->getLastInsertId();
	}

	private function fetchAllByQuery($sql, $params = [])
	{
		$this->query($sql);
		foreach ($params as $key => $value) {
			$this->bind($key, $value);
		}
		$this->execute();
		return $this->getResults();
	}
}
