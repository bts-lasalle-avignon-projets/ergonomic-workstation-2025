<?php

class Processus extends Controller
{
	protected function index()
	{
		$viewmodel = new ProcessusModel();
		$this->returnView($viewmodel->index(), true);
	}

	protected function add()
	{
		if (!isset($_SESSION['is_logged_in'])) {
			header('Location: ' . URL_PATH . 'shares');
		} else {
			$viewmodel = new ProcessusModel();
			$this->returnView($viewmodel->add(), true);
		}
	}

	protected function edit()
	{
		if (!isset($_SESSION['is_logged_in'])) {
			header('Location: ' . URL_PATH . 'shares');
		} else {
			$viewmodel = new ProcessusModel();
			$this->returnView($viewmodel->edit(), true);
		}
	}

	protected function delete()
	{
		if (!isset($_SESSION['is_logged_in'])) {
			header('Location: ' . URL_PATH . 'shares');
		} else {
			$viewmodel = new ProcessusModel();
			$this->returnView($viewmodel->delete(), true);
		}
	}
}
