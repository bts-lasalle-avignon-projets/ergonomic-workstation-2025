<?php
class Etape extends Controller {
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

    public function add() {
      if (NO_LOGIN) {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        $this->viewmodel->add();
        $nomProcessus = $this->viewmodel->getTitre();
        $this->display(['nomProcessus' => $nomProcessus]);
      } else {
        if (!isset($_SESSION['is_logged_in'])) {
          header('Location: ' . URL_PATH . 'processus');
        } else {
          $this->viewmodel->add();
          $this->display();
        }
      }
    }
}
?>