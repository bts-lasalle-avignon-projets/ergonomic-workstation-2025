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
    $this->viewmodel = new  EtapeModel();
  }

  public function add()
  {
    if (NO_LOGIN) {
      ini_set('display_errors', 1);
      error_reporting(E_ALL);
      if ($this->viewmodel->add() != null) {
        $nomProcessus = $this->viewmodel->getTitre();
        $numeroEtape = $this->viewmodel->getNumeroEtape();
        $this->display(['nomProcessus' => $nomProcessus, 'numeroEtape' => $numeroEtape]);
      } else {
        $idProcessus = $this->getID();
        if($idProcessus) {
          header('Location: ' . URL_PATH . 'etape' . '/' . 'add' . '/' . $idProcessus);
        } else {
          header('Location: ' . URL_PATH . 'processus');
        }
      }
    } else {
      if (!isset($_SESSION['is_logged_in'])) {
        header('Location: ' . URL_PATH . 'processus');
      } else {
        if ($this->viewmodel->add() != null) {
          $nomProcessus = $this->viewmodel->getTitre();
          $numeroEtape = $this->viewmodel->getNumeroEtape();
          $this->display(['nomProcessus' => $nomProcessus, 'numeroEtape' => $numeroEtape]);
        } else {
          $idProcessus = $this->getID();
          if($idProcessus) {
          header('Location: ' . URL_PATH . 'etape' . '/' . 'add' . '/' . $idProcessus);
          } else {
            header('Location: ' . URL_PATH . 'processus');
          }
        }
      }
    }
  }

  private function getID()
  {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
      Messages::setMsg("ID du processus manquant !", "error");
      return false;
    }
    return (int) $_GET['id'];
  }
}
