<?php

class Operateurs extends Controller
{
	private $viewmodel;

	public function __construct($action, $request)
	{
		parent::__construct($action, $request);
		$this->viewmodel = new OperateurModel();
	}

	protected function register()
	{
		$this->viewmodel->register();
		$this->display();
	}

	protected function login()
	{
		$this->viewmodel->login();
		$this->display();
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
