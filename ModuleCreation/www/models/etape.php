<?php
class EtapeModel extends Model {
    public function add() {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            Messages::setMsg("ID du processus manquant.", "error");
            return null;
        }

        $idProcessus = (int) $_GET['id'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nomEtape = $_POST['nomEtape'] ?? null;
            $descriptionEtape = $_POST['descriptionEtape'] ?? null;
            $contenance = $_POST['contenance'] ?? null;
            $numeroBac = $_POST['numeroBac'] ?? null;

            if (empty($nomEtape) || empty($descriptionEtape) || empty($contenance)) {
                Messages::setMsg("Tous les champs doivent être remplis.", "error");
                return null;
            }

            if (DEBUG) {
                ini_set('display_errors', 1);
                error_reporting(E_ALL);
                echo "ID du processus : " . $idProcessus . "<br>";
                echo "Nom de l'étape : " . $nomEtape . "<br>";
                echo "Description de l'étape : " . $descriptionEtape . "<br>";
                echo "Contenance : " . $contenance . "<br>";
                echo "Numéro de bac : " . $numeroBac . "<br>";
            }

            try {

                $bac = $this->ajouterBac($contenance, $numeroBac);

                if ($bac === null) {
                    return null;
                }

                $idImage = null;

				if (!empty($_FILES['image']['name'])) {
					$idImage = $this->ajouterImage();
				}

                $this->query("INSERT INTO Etapes (idProcessus, idBac, nomEtape, descriptionEtape, idImage) 
                              VALUES (:idProcessus, :idBac, :nomEtape, :descriptionEtape, :idImage)");
                $this->bind(':idProcessus', $idProcessus);
                $this->bind(':idBac', $bac->getIdBac());
                $this->bind(':nomEtape', $nomEtape);
                $this->bind(':descriptionEtape', $descriptionEtape);
                $this->bind(':idImage', $idImage, PDO::PARAM_INT);

                $this->execute();

                if ($this->stmt->rowCount() == 0) {
                    Messages::setMsg("L'insertion de l'étape a échoué.", "error");
                    return null;
                }
            } catch (PDOException $e) {
                Messages::setMsg("Erreur SQL : " . $e->getMessage(), "error");
            }
        }
        return null;
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

    private function ajouterBac($contenance, $numeroBac)
    {
        $this->query("SELECT idBac, contenance FROM Bac WHERE contenance = :contenance");
        $this->bind(':contenance', $contenance);
        $bacExist = $this->getResult(); 
        if ($bacExist) {
            $bac = new Bac($bacExist['idBac'], $bacExist['contenance']);
        } else {
            if (empty($numeroBac)) {
                Messages::setMsg("Le numéro de bac doit être fourni si le bac n'existe pas.", "error");
                return null;
            }

            $this->query("INSERT INTO Bac (contenance, numeroBac) VALUES (:contenance, :numeroBac)");
            $this->bind(':contenance', $contenance);
            $this->bind(':numeroBac', $numeroBac);
            $this->execute();
            if ($this->stmt->rowCount() == 0) {
                Messages::setMsg("L'insertion du bac a échoué.", "error");
                return null;
            }

            $idBac = $this->getLastInsertId();
            $bac = new Bac($idBac, $contenance);
        }
        return $bac;
    }
}
?>
