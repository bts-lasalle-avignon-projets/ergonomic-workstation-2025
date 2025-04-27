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
				$this->viewmodel->add();
				$this->display();
			}
		}
	}

	protected function edit()
	{
		if (!empty($this->request['id'])) {
			$this->id = $this->request['id'];
		} else {
			header('Location: ' . URL_PATH . 'processus');
		}
		if (NO_LOGIN) {
			$processus = $this->viewmodel->edit();
			$this->display($processus);
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				$processus = $this->viewmodel->edit();
				$this->display($processus);
			}
		}
	}

	protected function delete()
	{
		if (!empty($this->request['id'])) {
			$this->id = $this->request['id'];
		} else {
			header('Location: ' . URL_PATH . 'processus');
		}
		if (NO_LOGIN) {
			$idProcessus = $this->viewmodel->delete();
			$this->display($idProcessus);
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				$idProcessus = $this->viewmodel->delete();
				$this->display($idProcessus);
			}
		}
	}

	protected function view()
	{
		if (!empty($this->request['id'])) {
			$this->id = $this->request['id'];
		} else {
			header('Location: ' . URL_PATH . 'processus');
		}
		if (NO_LOGIN) {
			$idProcessus = $this->viewmodel->view();
			$this->display($idProcessus);
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				$idProcessus = $this->viewmodel->view();
				$this->display($idProcessus);
			}
		}
	}

	public function export()
	{
		if (NO_LOGIN) {
			$json = $this->viewmodel->export();
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
				$json = $this->viewmodel->export();
				header('Content-Type: application/json');
				header('Content-Disposition: attachment; filename="processus_export.json"');
				header('Content-Length: ' . strlen($json));
				echo $json;
				exit;
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
				$this->viewmodel->import();
				$this->display();
			}
		}
	}
}
