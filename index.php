<?php
//Konfiguration einbinden (globale Variablen und Funktionen)
require_once $_SERVER['DOCUMENT_ROOT'] .'/vereinsverwaltung/src/conf/config.php';

//Wenn Benutzer angemeldet dann zu home weiterleiten
if(isset($_SESSION['username']))
{
    header('Location: ' .LINK_HOME);
}

//Prüfen ob Formular abgeschickt wurde
if($_POST)
{

    //Prpfen ob Felder ausgefüllt
    if(!empty($_POST['username']) && !empty($_POST['password'])) {

        //Prüfen ob Daten mit Datenbank übereinstimmen
        $error = checkUserData($_POST['username'], $_POST['password']);
        
        //Wenn keine Fehlermeldung, erfolgreich angemeldet --> weiterleten zu home
        //Wenn Fehlermeldung message in SESSION speichern --> Ausgabe bei nächstem Templating->render/renderWrapper
        if($error == "")
        {
            header("Location: " .LINK_HOME);
        }
        else{
            $_SESSION['message'] = ['text' => $error,'type' => 'danger'];
        }
    }
}

//Klassen zum rendern von HTML-Templates (Layout Menü)
$tmpl = new Templating();
$wrappers = $tmpl->renderWrapper('layout.html');

//Wenn Rendern erfolgreich Header und Footer um Content ausgeben
if($wrappers) {
    echo $wrappers[0];
    ?>
        <form action="" method="post">
            <!--<input type="hidden" name="_csrf_token" value="{{ csrf_token }}" />-->

            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input type="text" class="form-control" placeholder="Benutzername" id="username" name="username"
                       value="<?php if (isset($_POST['username']))
                           echo $_POST['username'];; ?>"
                       required="required"/>
            </div>
            <br>
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-certificate"></i></span>
                <input type="password" class="form-control" placeholder="Passwort" id="password" name="password"
                       required="required"/>
            </div>

            <div class="form-group pull-right">
                <br>
                <input type="submit" class="btn btn-default" id="submit" name="submit" value="Login"/>
            </div>
        </form>

    <?php
    echo $wrappers[1];
}
//Fehlerseite Rendern bei error
else{
    echo $tmpl->render('error.html');
}

//Login Daten in Datenbank prüfen
function checkUserData($username,$password)
{

    $error = "";
    //Neues mysqli Objekt als Datenbank-Verbindung
    $dbconn = new mysqli('localhost',DBUSER,DBPASSWD,DATABASE);
    //Nach username in Datenbank suchen
    $userData = $dbconn->query("SELECT * FROM " .DATABASE .".login WHERE username = '" .$username ."'");

    //Prüfen ob Benutzername gefunden
    if($userData->num_rows != 0)
    {
        //Passwort aus Datenbank mit eingabe prüfen (password_verify, weil passwort gehasht mit salt)
        $row = $userData->fetch_assoc();
        $correctPw = password_verify($password,$row['password']);

        //Fehlermeldung wenn keie Übereinstimmung
        if(!$correctPw)
            $error = "Falsche Benutzerdaten.";
        else{
            //Benutzername in SESSION speichern (angemeldet)
            $_SESSION['username'] = $row['username'];
        }
    }
    else{
        $error = "Falsche Benutzerdaten.";
    }

    return $error;
}
