<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vereinsverwaltung/src/conf/config.php';
securityCheck();

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

