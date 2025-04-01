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
}
