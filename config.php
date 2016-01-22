<?php
//Use session to store login
session_start();

//Database parameters
define('DATABASE','vereinsverwaltung');
define('DBUSER','verein');
define('DBPASSWD','V3rwaltung!');

//Paths
define('PATH_LOG',dirname(__FILE__) .'/logs/');
define('PATH_TEMPLATES',dirname(__FILE__) .'/src/templates/');

//Links for redirect
define('LINK_LOGIN','/verwaltung/index.php');
define('LINK_LOGOUT','/verwaltung/src/security/logout.php');
define('LINK_HOME','/verwaltung/src/app/home.php');
define('LINK_CREATE','/verwaltung/src/app/create.php');

//Loading classes from path 'src/class/'
function __autoload($classname)
{

    if(file_exists(dirname(__FILE__) .'/src/class/' .$classname .'.php'))
        include dirname(__FILE__) .'/src/class/' .$classname .'.php';
}

//Check whether logged in and redirect if false
function securityCheck()
{

    if(empty($_SESSION['username'])) {
        header('Location: ' .LINK_LOGIN);
        die();
    }
}