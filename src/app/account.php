<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vereinsverwaltung/src/conf/config.php';

securityCheck();

$dbmanager = new DBManager();
$accounts = $dbmanager->getAll('Account',['name']);

$tmpl = new Templating();
$wrappers = $tmpl->renderWrapper('layoutMenu.html');
if($wrappers) {
    echo $wrappers[0];
    ?>

    <h1>Kontoübersicht</h1>

    <?php
    echo $wrappers[1];
}
else {
    echo $tmpl->render('error.html');
}
