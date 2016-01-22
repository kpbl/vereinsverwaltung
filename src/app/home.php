<?php
require_once '../../config.php';

securityCheck();

$dbmanager = new DBManager();
$users = $dbmanager->getAll('User');
//die(var_dump($users));

$tmpl = new Templating();
$wrappers = $tmpl->renderWrapper('layoutMenu.html');
if($wrappers){
    echo $wrappers[0];
?>

    <h1>Hallo <?php echo $_SESSION['username'] ?></h1>

<?php
    echo $wrappers[1];
}
else{
    echo $tmpl->render('error.html');
}

