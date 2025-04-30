<?php
class EtapeModel extends Model
{
    public function add()
    {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            Messages::setMsg("ID du processus manquant !", "error");
            return false;
        }

        $idProcessus = (int) $_GET['id'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nomEtape = $_POST['nomEtape'] ?? null;
            $descriptionEtape = $_POST['descriptionEtape'] ?? null;
            $contenance = $_POST['contenance'] ?? null;
            $numeroBac = $_POST['numeroBac'] ?? null;

            if (empty($nomEtape) || empty($descriptionEtape) || empty($contenance)) {
                Messages::setMsg("Tous les champs doivent être remplis.", "error");
                return false;
            }

            if (DEBUG) {
                echo "ID du processus : " . $idProcessus . "<br>";
                echo "Nom de l'étape : " . $nomEtape . "<br>";
                echo "Description de l'étape : " . $descriptionEtape . "<br>";
                echo "Contenance : " . $contenance . "<br>";
                echo "Numéro de bac : " . $numeroBac . "<br>";
            }

            try {
                $bac = $this->ajouterBac($numeroBac, $idProcessus, $contenance);

                if ($bac === null) {
                    return false;
                }

                $idImage = null;

                if (!empty($_FILES['image']['name'])) {
                    $idImage = $this->ajouterImage();
                }

                $numeroEtape = $this->incrementerNumeroEtape($idProcessus);

                $this->query("INSERT INTO Etape (idProcessus, idBac, nomEtape, descriptionEtape, idImage, numeroEtape) 
                              VALUES (:idProcessus, :idBac, :nomEtape, :descriptionEtape, :idImage, :numeroEtape)");
                $this->bind(':idProcessus', $idProcessus);
                $this->bind(':idBac', $bac->getIdBac());
                $this->bind(':nomEtape', $nomEtape);
                $this->bind(':descriptionEtape', $descriptionEtape);
                $this->bind(':idImage', $idImage, PDO::PARAM_INT);
                $this->bind(':numeroEtape', $numeroEtape);
                $this->execute();

                if ($this->stmt->rowCount() == 0) {
                    Messages::setMsg("L'insertion de l'étape a échoué !", "error");
                    return false;
                }
            } catch (PDOException $e) {
                Messages::setMsg("Erreur SQL : " . $e->getMessage(), "error");
                return false;
            }
        }

        return true;
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

    private function ajouterBac($numeroBac, $idProcessus, $contenance)
    {
        $verif = $this->verifierBac($numeroBac, $idProcessus, $contenance);
        if (!$verif) {
            return null;
        }
        $this->query("SELECT numeroBac, idProcessus, contenance FROM Bac WHERE numeroBac = :numeroBac AND idProcessus = :idProcessus AND contenance = :contenance");
        $this->bind(':numeroBac', $numeroBac);
        $this->bind(':idProcessus', $idProcessus);
        $this->bind(':contenance', $contenance);
        $bacExist = $this->getResult();
        if ($bacExist) {
            $bac = new Bac($bacExist['numeroBac'], $bacExist['contenance']);
        } else {
            $this->query("INSERT INTO Bac (numeroBac, idProcessus, contenance) VALUES (:numeroBac, :idProcessus, :contenance)");
            $this->bind(':numeroBac', $numeroBac);
            $this->bind(':idProcessus', $idProcessus);
            $this->bind(':contenance', $contenance);
            if (DEBUG) {
                echo "numeroBac : " . $numeroBac . "<br>";
                echo "idProcessus : " . $idProcessus . "<br>";
                echo "contenance : " . $contenance . "<br>";
            }
            $this->execute();
            $errorInfo = $this->stmt->errorInfo();

            if ($this->stmt->rowCount() == 0) {
                Messages::setMsg("L'insertion du bac a échoué !", "error");
                return null;
            }

            $bac = new Bac($numeroBac, $contenance);
        }
        return $bac ?? null;
    }

    public function getTitre()
    {

        $idProcessus = (int) $_GET['id'];
        $this->query("SELECT nomProcessus FROM Processus WHERE idProcessus = :idProcessus");
        $this->bind(':idProcessus', $idProcessus);
        $this->execute();
        $nomProcessus = $this->getResult();

        return $nomProcessus;
    }


    public function incrementerNumeroEtape($idProcessus)
    {
        $this->query("SELECT COUNT(*) AS total FROM Etape WHERE idProcessus = :idProcessus");
        $this->bind(':idProcessus', $idProcessus);
        $this->execute();


        $result = $this->getResult();

        if (!$result || !isset($result['total'])) {
            return 1;
        }

        $numeroProcessus = $result['total'] + 1;

        return $numeroProcessus;
    }

    public function getNumeroEtape()
    {
        $idProcessus = (int) $_GET['id'];
        $this->query("SELECT numeroEtape FROM Etape WHERE idProcessus = :idProcessus ORDER BY numeroEtape DESC LIMIT 1");
        $this->bind(':idProcessus', $idProcessus);
        $this->execute();
        $result = $this->getResult();

        // Vérifie si le résultat est valide (pas un booléen false ou vide)
        if ($result === false || empty($result)) {
            // Si aucun résultat, renvoyer un tableau avec 'numeroEtape' égal à 1
            return ['numeroEtape' => 1];
        }

        // Si un résultat existe, renvoyer le tableau avec 'numeroEtape' incrémenté
        return ['numeroEtape' => $result['numeroEtape'] + 1];
    }
private function verifierBac($numeroBac, $idProcessus, $contenance)
{
    // Vérifie si le numéro de bac existe déjà avec une autre contenance
    $this->query("SELECT contenance FROM Bac WHERE numeroBac = :numeroBac AND idProcessus = :idProcessus");
    $this->bind(':numeroBac', $numeroBac);
    $this->bind(':idProcessus', $idProcessus);
    $resultNum = $this->getResult();

    if ($resultNum && $resultNum['contenance'] != $contenance) {
        Messages::setMsg(
            "Le bac n°{$numeroBac} existe déjà dans ce processus avec une contenance différente : {$resultNum['contenance']}.",
            "error"
        );
        return false;
    }

    // Vérifie si la contenance est déjà associée à un autre numéro de bac
    $this->query("SELECT numeroBac FROM Bac WHERE contenance = :contenance AND idProcessus = :idProcessus");
    $this->bind(':contenance', $contenance);
    $this->bind(':idProcessus', $idProcessus);
    $resultCont = $this->getResult();

    if ($resultCont && $resultCont['numeroBac'] != $numeroBac) {
        Messages::setMsg(
            "La contenance de : {$contenance} est déjà utilisée par un autre bac n°{$resultCont['numeroBac']} dans ce processus.",
            "error"
        );
        return false;
    }

    return true;
}


}