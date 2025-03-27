<?php

class ProcessusModel extends Model
{
	public function index()
	{
		$this->query("SELECT idProcessus, nomProcessus, dateCreation FROM Processus ORDER BY dateCreation DESC");
		$processus = $this->resultSet();

		foreach ($processus as &$process) {
			$this->query("SELECT contenuBlob, typeMIME FROM Image WHERE idProcessus = :idProcessus LIMIT 1");
			$this->bind(':idProcessus', $process['idProcessus']);
			$imageData = $this->single();
			$process['image'] = $imageData ? $imageData : null; 
		}

		return $processus;
	}

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
            $titre = trim($_POST['title']);

            if (empty($titre)) {
                Message::afficher("Le titre est requis !", "erreur");
                return;
            }

            try {
                $this->query("INSERT INTO Processus (nomProcessus) VALUES (:nomProcessus)");
                $this->bind(':nomProcessus', $titre);
                $this->execute();
                $idProcessus = $this->lastInsertId();

                if (!empty($_FILES['image']['name'])) {
                    $this->ajouterImage($idProcessus, 'processus');
                }
                exit;
            } catch (PDOException $e) {
                Message::afficher("Erreur lors de l'insertion : " . $e->getMessage(), "erreur");
            }
        }
    }

	    private function ajouterImage($idProcessus, $typeImage)
    {
        $nomFichier = $_FILES['image']['name'];
        $typeMIME = $_FILES['image']['type'];
        $tailleImage = $_FILES['image']['size'];
        $contenuBlob = file_get_contents($_FILES['image']['tmp_name']);

        $typesAutorises = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($typeMIME, $typesAutorises)) {
            Message::afficher("Format d'image non autorisé !", "erreur");
            return;
        }

        $this->query("INSERT INTO Image (nomFichier, typeMIME, contenuBlob, tailleImage, idProcessus, typeImage) 
                      VALUES (:nomFichier, :typeMIME, :contenuBlob, :tailleImage, :idProcessus, :typeImage)");

        $this->bind(':nomFichier', $nomFichier);
        $this->bind(':typeMIME', $typeMIME);
        $this->bind(':contenuBlob', $contenuBlob, PDO::PARAM_LOB);
        $this->bind(':tailleImage', $tailleImage, PDO::PARAM_INT);
        $this->bind(':idProcessus', $idProcessus, PDO::PARAM_INT);
        $this->bind(':typeImage', $typeImage);

        $this->execute();
    }

	public function edit() {}

	public function delete() {}
}
