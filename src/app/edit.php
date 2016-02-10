<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vereinsverwaltung/src/conf/config.php';
//Prüfen ob Benutzer angemeldet, sonst redirect zu login Seite
securityCheck();

//Wenn get parameter 'user' gesetzt Eintrag mit dieser id anzeigen
if(isset($_GET['user'])) {
    //Verbinden mit DB und Eintrag in Userobject laden
    $dbmanager = new DBManager();
    $user = $dbmanager->get('User', $_GET['user']);

    //Wenn get parameter 'delete' gesetzt Eintrag löschen
    if(isset($_GET['delete']))
    {
        if($dbmanager->delete('User',$user->getId())){
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Das Mitglied wurde gelöscht'];
            header('Location: ' .LINK_OVERVIEW);
            die();
        }
        else{
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Das Mitglied konnte nicht gelöscht werden'];
            header('Location: ' .LINK_OVERVIEW);
            die();
        }
    }
}
else {

    $_SESSION['message'] = ['text' => 'Wählen Sie einen Benutzer aus','type' => 'danger'];
    header('Location: ' .LINK_OVERVIEW);
    die();
}

//Prüfen ob Formular abgeschickt wurde
if($_POST)
{
    //Daten von Formular in Array speichern und prüfen
    $userData = getDataFromPost();
    if(!isset($userData['error']))
    {

        //Benutzer von Datenbank laden und eingegebenen Daten speichern
        $dbmanager = new DBManager();
        if($dbmanager->isConnected()){
            $user = $dbmanager->get('User', $_GET['user']);
            $user->fetchData($userData);
            //die(var_dump($user));
            if($dbmanager->update('User',[$user])){
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Das Mitglied wurde im System gespeichert'];
                header('Location: ' .LINK_OVERVIEW);
                die();
            }
            else{
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Die Daten konnten nicht gespeichert werde'];
            }
        }
        else{
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Es konnte keine Verbindung zur Datenbank hergestellt werden'];
        }
    }
    else{
        $_SESSION['message'] = ['type' => 'danger', 'text' => $userData['error']];
    }
}

$tmpl = new Templating();
$wrappers = $tmpl->renderWrapper('layoutMenu.html');
if($wrappers) {
    echo $wrappers[0];
    ?>

    <form action="" method="post" id="formCreateUser">
        <div class="form-group col-md-12">
            <label for="inpFirstname">Vorname</label>
            <input type="text" class="form-control" id="inpFirstname" name="firstname"
                   value="<?php echo $user->getFirstname(); ?>"
                   required>
        </div>
        <div class="form-group col-md-12">
            <label for="inpName">Nachname</label>
            <input type="text" class="form-control" id="inpName" name="name"
                   value="<?php echo $user->getName(); ?>" required>
        </div>
        <div class="form-group col-md-8">
            <label for="inpStreetname">Straße</label>
            <input type="text" class="form-control" id="inpStreetname" name="streetname"
                   value="<?php echo $user->getStreetname(); ?>" required>
        </div>
        <div class="form-group col-md-4">
            <label for="inpStreetnumber">Hausnr.</label>
            <input type="text" class="form-control" id="inpStreetnumber" name="streetnumber"
                   value="<?php echo $user->getStreetnumber() .$user->getStreetad(); ?>" required>
        </div>
        <div class="form-group col-md-8">
            <label for="inpCity">Stadt</label>
            <input type="text" class="form-control" id="inpCity" name="city"
                   value="<?php echo $user->getCity(); ?>" required>
        </div>
        <div class="form-group col-md-4">
            <label for="inpPostcode">Postleitzahl</label>
            <input type="text" class="form-control" id="inpPostcode" name="postcode"
                   value="<?php echo $user->getPostcode(); ?>" required>
        </div>
        <div class="form-group col-md-12">
            <label for="inpBirthday">Geburtstag (dd.mm.yyyy)</label>
            <input type="text" class="form-control" id="inpBirthday" name="birthday"
                   value="<?php echo formatDate($user->getBirthday()); ?>" required>
        </div>
        <div class="form-group col-md-12">
            <label for="inpIban">IBAN</label>
            <input type="text" class="form-control" id="inpIban" name="iban"
                   value="<?php echo $user->getIban(); ?>">
        </div>
        <div class="form-group col-md-12">
            <label for="inpBic">BIC</label>
            <input type="text" class="form-control" id="inpBic" name="bic"
                   value="<?php echo $user->getBic(); ?>">
        </div>
        <div class="form-group col-md-12">
            <label>
                Arbeitstätig <input type="checkbox" id="inpWorking"
                                    name="working" <?php if ($user->getWorking()) echo 'checked'; ?>>
            </label>
        </div>
        <button type="submit" class="btn btn-primary pull-right">Speichern</button>
    </form>

    <?php
    echo $wrappers[1];
}
else{
    echo $tmpl->render('error.html');
}

function formatDate($date)
{
    return date('d.m.Y',strtotime($date));
}
































