<?php
class Etape extends Controller
{
  private int $idEtape;
  private string $nomEtape;
  private string $descriptionEtape;
  private Bac $bac;
  private int $idProcessus;
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
        // @todo
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
                  // Accéder au premier élément du tableau
                  $etapeData = $etape[0]; // Puisque $etape est un tableau avec un élément à l'index 0
                  var_dump($etapeData); // Pour vérifier l'élément récupéré
                  
                  // Vérification si les clés existent avant de les utiliser
                  if (isset($etapeData['idProcessus']) && isset($etapeData['numeroEtape'])) {
                      $nomProcessus = $this->viewmodel->getTitre($etapeData['idProcessus']);
                      $numeroEtape = $etapeData['numeroEtape'];
                      $this->display(['nomProcessus' => $nomProcessus, 'numeroEtape' => $numeroEtape]);
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
              // @todo
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
