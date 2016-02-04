<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vereinsverwaltung/src/conf/config.php';

session_destroy();
header('Location: ' .LINK_LOGIN);