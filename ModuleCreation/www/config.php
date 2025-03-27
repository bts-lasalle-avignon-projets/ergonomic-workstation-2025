<?php

// Pour le debug
define("DEBUG", false);

// Pour la base de données
define("DB_DRIVER", true); // true pour MySQL, false sans base de données
define("DB_HOST", "localhost");
define("DB_USER", "ergoWork");
define("DB_PASS", "password");
define("DB_NAME", "ergonomic_workstation");

// URL
define("ROOT_PATH", "/");
//define("URL_PATH", "http://" . $_SERVER['HTTP_HOST'] . "/ergonomic-workstation/");
define("URL_PATH", "http://" . $_SERVER['HTTP_HOST'] . "/");

// Divers
define("TITRE_SITE", "Ergonomic Workstation");