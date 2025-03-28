<?php

class Processus extends Controller
{
	private $id;

	protected function index()
	{
		$viewmodel = new ProcessusModel();
		$this->returnView($viewmodel->index(), true);
	}

	protected function add()
	{
		if (NO_LOGIN) {
			$viewmodel = new ProcessusModel();
			$this->returnView($viewmodel->add(), true);
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				$viewmodel = new ProcessusModel();
				$this->returnView($viewmodel->add(), true);
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
			$viewmodel = new ProcessusModel();
			$this->returnView($viewmodel->edit(), true);
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				$viewmodel = new ProcessusModel();
				$this->returnView($viewmodel->edit(), true);
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
			$viewmodel = new ProcessusModel();
			$this->returnView($viewmodel->delete(), true);
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				$viewmodel = new ProcessusModel();
				$this->returnView($viewmodel->delete(), true);
			}
		}
	}
}
