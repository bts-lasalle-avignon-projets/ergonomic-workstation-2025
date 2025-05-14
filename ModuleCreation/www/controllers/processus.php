<?php

class Processus extends Controller
{
	private $id;
	private $viewmodel;

	public function __construct($action, $request)
	{
		parent::__construct($action, $request);
		$this->viewmodel = new ProcessusModel();
	}

	protected function index()
	{
		// Récupère la liste des processus
		$listeProcessus = $this->viewmodel->index();
		// Affiche la liste des processus
		$this->display($listeProcessus);
	}

	protected function add()
	{
		if (NO_LOGIN) {
			$this->viewmodel->add();
			$this->display();
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				// @todo
			}
		}
	}

	protected function edit()
	{
		if (NO_LOGIN) {
			$idProcessus = $this->getID();
			if ($idProcessus > 0) {
			} else {
				header('Location: ' . URL_PATH . 'processus');
			}
			$processus = $this->viewmodel->edit($idProcessus);
			$this->display($processus);
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				// @todo
			}
		}
	}

	protected function delete()
	{
		if (NO_LOGIN) {
			$idProcessus = $this->getID();
			if ($idProcessus > 0) {
				$id = $this->viewmodel->delete($idProcessus);
				$this->display($id);
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

	protected function view()
	{
		if (NO_LOGIN) {
			$idProcessus = $this->getID();
			if ($idProcessus > 0) {
				$etapesProcessus = $this->viewmodel->view($idProcessus);
				$this->display($etapesProcessus);
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

	public function export()
	{
		if (NO_LOGIN) {
			$idProcessus = $this->getID();
			if ($idProcessus > 0) {
			} else {
				header('Location: ' . URL_PATH . 'processus');
			}
			$json = $this->viewmodel->export($idProcessus);
			if (defined('DEBUG') && DEBUG === true) {
				$this->display(['json' => $json]);
			} else {
				header('Content-Type: application/json');
				header('Content-Disposition: attachment; filename="processus_export.json"');
				header('Content-Length: ' . strlen($json));
				echo $json;
				exit;
			}
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				// @todo
			}
		}
	}

	public function import()
	{
		if (NO_LOGIN) {
			$this->viewmodel->import();
			$this->display();
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				// @todo
			}
		}
	}

	public function statistique() {
		if (NO_LOGIN) {
			$idProcessus = $this->getID();
			$AssemblageData = $this->viewmodel->statistiqueAssemblage($idProcessus);
			if(DEBUG)
			{
				var_dump($AssemblageData);
			}
			$this->display($AssemblageData);
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
