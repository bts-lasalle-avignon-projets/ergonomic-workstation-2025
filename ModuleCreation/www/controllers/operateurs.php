<?php

class Operateurs extends Controller
{
	protected function register()
	{
		$viewmodel = new OperateurModel();
		$this->returnView($viewmodel->register(), true);
	}

	protected function login()
	{
		$viewmodel = new OperateurModel();
		$this->returnView($viewmodel->login(), true);
	}

	protected function logout()
	{
		unset($_SESSION['is_logged_in']);
		unset($_SESSION['user_data']);
		session_destroy();
		// Redirect
		header('Location: ' . URL_PATH);
	}
}
