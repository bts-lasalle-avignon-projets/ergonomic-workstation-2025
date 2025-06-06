<?php

class Processus extends Controller
{
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
		if (NO_LOGIN) {
			$idProcessus = $this->getID();
			if ($idProcessus > 0) {
				if ($this->viewmodel->edit($idProcessus)) {
					$processus = $this->viewmodel->getProcessus($idProcessus);
					$this->display($processus);
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
					if ($this->viewmodel->edit($idProcessus)) {
						$processus = $this->viewmodel->getProcessus($idProcessus);
						$this->display($processus);
					}
				} else {
					header('Location: ' . URL_PATH . 'processus');
				}
			}
		}
	}

	protected function delete()
	{
		if (NO_LOGIN) {
			$idProcessus = $this->getID();

			if ($idProcessus > 0) {
				if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit']) && $_POST['submit'] === 'Oui') {
					$this->viewmodel->delete($idProcessus);
					Messages::setMsg("Processus supprimé avec succès.", "success");
					header('Location: ' . URL_PATH . 'processus');
					exit;
				} else {
					// Affiche le bouton "Oui" si aucune validation POST
					$this->display($idProcessus);
				}
			} else {
				header('Location: ' . URL_PATH . 'processus');
				exit;
			}
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
				exit;
			} else {
				$idProcessus = $this->getID();

				if ($idProcessus > 0) {
					if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit']) && $_POST['submit'] === 'Oui') {
						$this->viewmodel->delete($idProcessus);
						Messages::setMsg("Processus supprimé avec succès.", "success");
						header('Location: ' . URL_PATH . 'processus');
						exit;
					} else {
						// Affiche le bouton "Oui" si aucune validation POST
						$this->display($idProcessus);
					}
				} else {
					header('Location: ' . URL_PATH . 'processus');
					exit;
				}
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
				$idProcessus = $this->getID();
				if ($idProcessus > 0) {
					$etapesProcessus = $this->viewmodel->view($idProcessus);
					$this->display($etapesProcessus);
				} else {
					header('Location: ' . URL_PATH . 'processus');
				}
			}
		}
	}

	public function exportZip()
	{
		if (NO_LOGIN) {
			$idProcessus = $this->getID();
			if ($idProcessus > 0) {
				$this->viewmodel->exportZip($idProcessus); // méthode déjà prête
			} else {
				header('Location: ' . URL_PATH . 'processus');
			}
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				$idProcessus = $this->getID();
				if ($idProcessus > 0) {
					$this->viewmodel->exportZip($idProcessus); // méthode déjà prête
				} else {
					header('Location: ' . URL_PATH . 'processus');
				}
			}
		}
	}

	public function importZip()
	{
		if (NO_LOGIN) {
			$this->viewmodel->importZip(); // méthode déjà prête
			$this->display();
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				$this->viewmodel->importZip(); // méthode déjà prête
				$this->display();
			}
		}
	}

	public function statistique()
	{
		if (NO_LOGIN) {
			$idProcessus = $this->getID();
			$statistiquesAssemblage = $this->viewmodel->statistiqueAssemblage($idProcessus);
			if (DEBUG) {
				var_dump($statistiquesAssemblage);
			}
			$this->display($statistiquesAssemblage);
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				$idProcessus = $this->getID();
				$statistiquesAssemblage = $this->viewmodel->statistiqueAssemblage($idProcessus);
				if (DEBUG) {
					var_dump($statistiquesAssemblage);
				}
				$this->display($statistiquesAssemblage);
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
