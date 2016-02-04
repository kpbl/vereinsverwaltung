<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vereinsverwaltung/src/conf/config.php';
securityCheck();

if($_POST)
{

    if($_POST['new'] === $_POST['confirm']) {

        $error = changePw($_POST['current'], $_POST['new']);
    }
    else {

        $error = "Bestätigung stimmt nicht überein";
    }
}

$tmpl = new Templating();
$wrappers = $tmpl->renderWrapper('layoutMenu.html');
if($wrappers){
    echo $wrappers[0];

    if (!empty($error)) {

        echo '<div class="alert alert-danger" role="alert">'
            . $error
            . '</div><br>';
    } else if($_POST){

        echo '<div class="alert alert-success" role="alert">'
            . 'Das Passwort wurde erfolgreich geändert'
            . '</div><br>';
    }
    ?>

    <form action="" method="post">

        <div class="form-group">
            <label for="current">Aktuelles Passwort:</label>
            <input type="password" class="form-control" id="current" name="current" required="required"/>
        </div>
        <br>
        <div class="form-group">
            <label for="new">Neues Passwort:</label>
            <input type="password" class="form-control" id="new" name="new" required="required"/>
        </div>
        <div class="form-group">
            <label for="confirm">Bestätigen:</label>
            <input type="password" class="form-control" id="confirm" name="confirm" required="required"/>
        </div>

        <div class="form-group pull-right">
            <br>
            <input type="submit" class="btn btn-default" id="submit" name="submit" value="Passwort ändern"/>
        </div>
    </form>

    <?php
    echo $wrappers[1];
}
else{
    echo $tmpl->render('error.html');
}

function changePw($pw,$newpw)
{

    $error = "";
    $dbconn = new mysqli('localhost',DBUSER,DBPASSWD,DATABASE);
    $dbResult = $dbconn->query("SELECT password FROM " .DATABASE .".login WHERE username = '" .$_SESSION['username'] ."'");

    $storedPw = $dbResult->fetch_assoc();
    $pwCorrect = password_verify($pw,$storedPw['password']);

    if($pwCorrect)
    {

        $pwhash = password_hash($newpw, PASSWORD_DEFAULT);
        $query = "UPDATE " .DATABASE .".login SET password='" .$pwhash ."' WHERE username='" .$_SESSION['username'] ."'";
        $insertRes = $dbconn->query($query);

        if(!$insertRes)
        {
            $error="Das Passwort konnte nicht geändert werden";
            $logger = new Logger();
            $logger->writeError('DBManager: Fehler bei Statement "' .$query .'"');
        }
    }
    else
        $error = "Aktuelles Passwort nicht korrekt";

    return $error;
}