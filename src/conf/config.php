<?php
//Use session to store login
session_start();

//Database parameters
define('DATABASE','vereinsverwaltung');
define('DBUSER','verein');
define('DBPASSWD','V3rwaltung!');

//Paths
define('PATH_LOG',dirname(__FILE__) .'/../../logs/');
define('PATH_TEMPLATES',dirname(__FILE__) .'/../templates/');

//Links for redirect
define('LINK_LOGIN','/verwaltung/index.php');
define('LINK_LOGOUT','/verwaltung/src/security/logout.php');
define('LINK_HOME','/verwaltung/src/app/home.php');
define('LINK_CREATE','/verwaltung/src/app/create.php');
define('LINK_OVERVIEW','/verwaltung/src/app/overview.php');
define('LINK_EDIT','/verwaltung/src/app/edit.php');
define('LINK_MONEY','/verwaltung/src/app/account.php');
define('LINK_PAYMENT','/verwaltung/src/app/payment.php');

//Parameter for debug mode
define('DEBUG',true);

//functions.php für globale Funktionen einbinden
require_once 'functions.php';