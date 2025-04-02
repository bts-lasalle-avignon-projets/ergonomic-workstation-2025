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
        $this->viewmodel->add();
        $this->display();
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