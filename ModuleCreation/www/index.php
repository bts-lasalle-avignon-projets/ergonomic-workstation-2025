<?php
// Démarre la session PHP
session_start();

require('config.php');

require('classes/message.php');
require('classes/messages.php');
require('classes/router.php');
require('classes/controller.php');
require('classes/model.php');
require('classes/bac.php');

require('controllers/accueil.php');
require('controllers/operateurs.php');
require('controllers/processus.php');
require('controllers/etape.php');

require('models/accueil.php');
require('models/operateur.php');
require('models/processus.php');
require('models/etape.php');

// Forme de l'URL, après réécriture : http://root/controleur/action/id
$router = new Router($_GET);
$controller = $router->createController();

if ($controller) {
	$controller->executeAction();
}