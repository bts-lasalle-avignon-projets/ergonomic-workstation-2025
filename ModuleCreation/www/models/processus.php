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

	

	public function edit() {}

	public function delete() {}
}
