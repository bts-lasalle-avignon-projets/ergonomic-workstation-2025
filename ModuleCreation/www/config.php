<?php

// le nom à ajouter dans l'URL
//define("URL_NAME", "ergonomic-workstation");
define("URL_NAME", "");

// Pour le debug
define("DEBUG", false);

// Pour les tests
define("NO_LOGIN", false);

// Pour la base de données
define("DB_DRIVER", true); // true pour MySQL, false sans base de données
define("DB_HOST", "127.0.0.1");
define("DB_USER", "ergoWork");
define("DB_PASS", "password");
define("DB_NAME", "ergonomic_workstation");

// URL
if (!empty(URL_NAME)) {
    define("ROOT_PATH", "/" . URL_NAME . "/");
    define("URL_PATH", "http://" . $_SERVER['HTTP_HOST'] . "/" . URL_NAME . "/");
} else {
    define("ROOT_PATH", "/");
    define("URL_PATH", "http://" . $_SERVER['HTTP_HOST'] . "/");
}

// Divers
define("TITRE_SITE", "Ergonomic Workstation");

if (DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

define('PRECISION_POURCENTAGE', 2);
define('TEMPS_MINUTE', 60);

define('TEMPS_AFFICHAGE_MESSAGE', 3000);
define('TEMPS_ANIMATION_OPACITE', 500);