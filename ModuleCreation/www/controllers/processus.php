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
		//$viewmodel = new ProcessusModel();
		$this->returnView($this->viewmodel->index(), true);
	}

	protected function add()
	{
		if (NO_LOGIN) {
			//$viewmodel = new ProcessusModel();
			$this->returnView($this->viewmodel->add(), true);
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				//$viewmodel = new ProcessusModel();
				$this->returnView($this->viewmodel->add(), true);
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
			//$viewmodel = new ProcessusModel();
			$this->returnView($this->viewmodel->edit(), true);
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				//$viewmodel = new ProcessusModel();
				$this->returnView($this->viewmodel->edit(), true);
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
			//$viewmodel = new ProcessusModel();
			$this->returnView($this->viewmodel->delete(), true);
		} else {
			if (!isset($_SESSION['is_logged_in'])) {
				header('Location: ' . URL_PATH . 'processus');
			} else {
				//$viewmodel = new ProcessusModel();
				$this->returnView($this->viewmodel->delete(), true);
			}
		}
	}
}
