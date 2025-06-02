<?php
class Etape extends Controller
{
  private $viewmodel;

  public function __construct($action, $request)
  {
    parent::__construct($action, $request);
    $this->viewmodel = new EtapeModel();
  }

  public function add()
  {
    if (NO_LOGIN) {
      $idProcessus = $this->getID();
      if ($idProcessus > 0) {
        if ($this->viewmodel->add($idProcessus)) {
          $nomProcessus = $this->viewmodel->getTitre($idProcessus);
          $numeroEtape = $this->viewmodel->getNumeroEtape($idProcessus);
          $this->display(['nomProcessus' => $nomProcessus, 'numeroEtape' => $numeroEtape]);
        } else {
          if ($this->viewmodel->estProcessusExistant($idProcessus)) {
            header('Location: ' . URL_PATH . 'etape' . '/' . 'add' . '/' . $idProcessus);
          } else {
            header('Location: ' . URL_PATH . 'processus');
          }
        }
      } else {
        header('Location: ' . URL_PATH . 'processus');
      }
    } else {
      if (!isset($_SESSION['is_logged_in'])) {
        header('Location: ' . URL_PATH . 'processus');
      } else {
        $idProcessus = $this->getID();
        if ($idProcessus > 0) {
          if ($this->viewmodel->add($idProcessus)) {
            $nomProcessus = $this->viewmodel->getTitre($idProcessus);
            $numeroEtape = $this->viewmodel->getNumeroEtape($idProcessus);
            $this->display(['nomProcessus' => $nomProcessus, 'numeroEtape' => $numeroEtape]);
          } else {
            if ($this->viewmodel->estProcessusExistant($idProcessus)) {
              header('Location: ' . URL_PATH . 'etape' . '/' . 'add' . '/' . $idProcessus);
            } else {
              header('Location: ' . URL_PATH . 'processus');
            }
          }
        } else {
          header('Location: ' . URL_PATH . 'processus');
        }
      }
    }
  }


  public function edit()
  {
    if (NO_LOGIN) {
      $idEtape = $this->getID();
      if ($idEtape > 0) {
        if ($this->viewmodel->edit($idEtape)) {
          $etape = $this->viewmodel->getEtapeParID($idEtape);
          $bac = $this->viewmodel->getBacEtape($idEtape);
          $imageData = $this->viewmodel->getImageEtape($idEtape);
          $imageData = $imageData[0] ?? []; // sécurise l'accès
          $etapeData = $etape[0]; // Puisque $etape est un tableau avec un élément à l'index 0 
          $bacData = $bac[0];
          if (isset($etapeData['idProcessus']) && isset($etapeData['numeroEtape'])) {
            $nomProcessus = $this->viewmodel->getTitre($etapeData['idProcessus']);
            $numeroEtape = $etapeData['numeroEtape'];

            $this->display(array_merge($etapeData, [
              'nomProcessus' => $nomProcessus,
              'numeroEtape' => $numeroEtape,
              'contenance' => $bacData['contenance'] ?? '',
              'numeroBac' => $bacData['numeroBac'] ?? '',
              'imageBase64' => isset($imageData['contenuBlob'])
                ? 'data:' . $imageData['typeMIME'] . ';base64,' . base64_encode($imageData['contenuBlob'])
                : null,
              'nomImage' => $imageData['nomFichier'] ?? null
            ]));
          } else {
            echo "Données manquantes pour cette étape.";
          }
        } else {
          header('Location: ' . URL_PATH . 'etape' . '/' . 'edit' . '/' . $idEtape);
        }
      } else {
        header('Location: ' . URL_PATH . 'processus');
      }
    } else {
      if (!isset($_SESSION['is_logged_in'])) {
        header('Location: ' . URL_PATH . 'processus');
      } else {
        $idEtape = $this->getID();
        if ($idEtape > 0) {
          if ($this->viewmodel->edit($idEtape)) {
            $etape = $this->viewmodel->getEtapeParID($idEtape);
            $bac = $this->viewmodel->getBacEtape($idEtape);
            $imageData = $this->viewmodel->getImageEtape($idEtape);
            $imageData = $imageData[0] ?? []; // sécurise l'accès
            $etapeData = $etape[0]; // Puisque $etape est un tableau avec un élément à l'index 0 
            $bacData = $bac[0];
            if (isset($etapeData['idProcessus']) && isset($etapeData['numeroEtape'])) {
              $nomProcessus = $this->viewmodel->getTitre($etapeData['idProcessus']);
              $numeroEtape = $etapeData['numeroEtape'];

              $this->display(array_merge($etapeData, [
                'nomProcessus' => $nomProcessus,
                'numeroEtape' => $numeroEtape,
                'contenance' => $bacData['contenance'] ?? '',
                'numeroBac' => $bacData['numeroBac'] ?? '',
                'imageBase64' => isset($imageData['contenuBlob'])
                  ? 'data:' . $imageData['typeMIME'] . ';base64,' . base64_encode($imageData['contenuBlob'])
                  : null,
                'nomImage' => $imageData['nomFichier'] ?? null
              ]));
            } else {
              echo "Données manquantes pour cette étape.";
            }
          } else {
            header('Location: ' . URL_PATH . 'etape' . '/' . 'edit' . '/' . $idEtape);
          }
        } else {
          header('Location: ' . URL_PATH . 'processus');
        }
      }
    }
  }




  private function getID()
  {
    if (!isset($this->request['id']) || empty($this->request['id'])) {
      Messages::setMsg("ID du processus manquant !", "error");
      return -1;
    }
    return (int) $this->request['id'];
  }
}
