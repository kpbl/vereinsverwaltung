<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vereinsverwaltung/src/conf/config.php';

securityCheck();

if($_POST)
{
    $userData = getDataFromPost();
    if(!isset($userData['error']))
    {

        $dbmanager = new DBManager();
        if($dbmanager->isConnected()){
            if($dbmanager->persist('User',[$userData])){
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Das Mitglied wurde im System gespeichert'];
                unset($_POST);
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
        $_SESSION['message'] = ['text' => $userData['error'],'type' => 'danger'];
    }
}

$tmpl = new Templating();
$wrappers = $tmpl->renderWrapper('layoutMenu.html');
if($wrappers) {
    echo $wrappers[0];
    ?>

    <form action="" method="post" id="formCreateUser">
        <div class="form-group col-md-12">
            <label for="inpFirstname">Vorname*</label>
            <input type="text" class="form-control" id="inpFirstname" name="firstname"
                   <?php if (isset($_POST['firstname'])) echo 'value="' . $_POST['firstname'] . '" '; ?>
                   required>
        </div>
        <div class="form-group col-md-12">
            <label for="inpName">Nachname*</label>
            <input type="text" class="form-control" id="inpName" name="name"
                   <?php if (isset($_POST['name'])) echo 'value="' . $_POST['name'] . '" '; ?>required>
        </div>
        <div class="form-group col-md-8">
            <label for="inpStreetname">Straße*</label>
            <input type="text" class="form-control" id="inpStreetname" name="streetname"
                   <?php if (isset($_POST['streetname'])) echo 'value="' . $_POST['streetname'] . '" '; ?>required>
        </div>
        <div class="form-group col-md-4">
            <label for="inpStreetnumber">Hausnr.*</label>
            <input type="text" class="form-control" id="inpStreetnumber" name="streetnumber"
                   <?php if (isset($_POST['streetnumber'])) echo 'value="' . $_POST['streetnumber'] . '" '; ?>required>
        </div>
        <div class="form-group col-md-8">
            <label for="inpCity">Stadt*</label>
            <input type="text" class="form-control" id="inpCity" name="city"
                   <?php if (isset($_POST['city'])) echo 'value="' . $_POST['city'] . '" '; ?>required>
        </div>
        <div class="form-group col-md-4">
            <label for="inpPostcode">Postleitzahl*</label>
            <input type="text" class="form-control" id="inpPostcode" name="postcode"
                   <?php if (isset($_POST['postcode'])) echo 'value="' . $_POST['postcode'] . '" '; ?>required>
        </div>
        <div class="form-group col-md-12">
            <label for="inpBirthday">Geburtstag (dd.mm.yyyy)*</label>
            <input type="text" class="form-control" id="inpBirthday" name="birthday"
                   <?php if (isset($_POST['birthday'])) echo 'value="' . $_POST['birthday'] . '" '; ?>required>
        </div>
        <div class="form-group col-md-12">
            <label for="inpIban">IBAN</label>
            <input type="text" class="form-control" id="inpIban" name="iban"
                   <?php if (isset($_POST['iban'])) echo 'value="' . $_POST['iban'] . '"'; ?>>
        </div>
        <div class="form-group col-md-12">
            <label for="inpBic">BIC</label>
            <input type="text" class="form-control" id="inpBic" name="bic"
                    <?php if (isset($_POST['bic'])) echo 'value="' . $_POST['bic'] . '"'; ?>>
        </div>
        <div class="form-group col-md-12">
            <label>
                Arbeitstätig <input type="checkbox" id="inpWorking"
                                    name="working" <?php if (isset($_POST['working']) && $_POST['working']) echo 'checked'; ?>>
            </label>
        </div>
        <button type="submit" class="btn btn-default pull-right">Anlegen</button>
    </form>

    <div class="row">
        <div class="col-md-6">
            Die mit einem * gekennzeichneten Felder sind Pflichtfelder
        </div>
    </div>

    <?php
    echo $wrappers[1];
}
else{
    echo $tmpl->render('error.html');
}
































