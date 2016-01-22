<?php
require_once '../../config.php';

securityCheck();

if($_POST)
{
    $userData = getDataFromPost();
    if(!isset($userData['error']))
    {

        $dbmanager = new DBManager();
        if($dbmanager->isConnected()){
            if($dbmanager->persist('User',[$userData])){
                $alert = ['type' => 'success', 'message' => 'Das Mitglied wurde im System gespeichert'];
                unset($_POST);
            }
            else{
                $alert = ['type' => 'danger', 'message' => 'Die Daten konnten nicht gespeichert werde'];
            }
        }
        else{
            $alert = ['type' => 'danger', 'message' => 'Es konnte keine Verbindung zur Datenbank hergestellt werden'];
        }
    }
}

$tmpl = new Templating();
$wrappers = $tmpl->renderWrapper('layoutMenu.html');
if($wrappers) {
    echo $wrappers[0];

    if (isset($userData['error'])) {
        echo '<div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  ' . $userData['error'] . '</div>';
    }

    if (isset($alert)) {
        echo '<div class="alert alert-' .$alert['type'] .' alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  ' . $alert['message'] . '</div>';
    }
    ?>

    <form action="" method="post" id="formCreateUser">
        <div class="form-group col-md-12">
            <label for="inpFirstname">Vorname</label>
            <input type="text" class="form-control" id="inpFirstname" name="firstname"
                   <?php if (isset($_POST['firstname'])) echo 'value="' . $_POST['firstname'] . '" '; ?>
                   required>
        </div>
        <div class="form-group col-md-12">
            <label for="inpName">Nachname</label>
            <input type="text" class="form-control" id="inpName" name="name"
                   <?php if (isset($_POST['name'])) echo 'value="' . $_POST['name'] . '" '; ?>required>
        </div>
        <div class="form-group col-md-8">
            <label for="inpStreetname">Straße</label>
            <input type="text" class="form-control" id="inpStreetname" name="streetname"
                   <?php if (isset($_POST['streetname'])) echo 'value="' . $_POST['streetname'] . '" '; ?>required>
        </div>
        <div class="form-group col-md-4">
            <label for="inpStreetnumber">Hausnr.</label>
            <input type="text" class="form-control" id="inpStreetnumber" name="streetnumber"
                   <?php if (isset($_POST['streetnumber'])) echo 'value="' . $_POST['streetnumber'] . '" '; ?>required>
        </div>
        <div class="form-group col-md-8">
            <label for="inpCity">Stadt</label>
            <input type="text" class="form-control" id="inpCity" name="city"
                   <?php if (isset($_POST['city'])) echo 'value="' . $_POST['city'] . '" '; ?>required>
        </div>
        <div class="form-group col-md-4">
            <label for="inpPostcode">Postleitzahl</label>
            <input type="text" class="form-control" id="inpPostcode" name="postcode"
                   <?php if (isset($_POST['postcode'])) echo 'value="' . $_POST['postcode'] . '" '; ?>required>
        </div>
        <div class="form-group col-md-12">
            <label for="inpBirthday">Geburtstag</label>
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

    <?php
    echo $wrappers[1];
}
else{
    echo $tmpl->render('error.html');
}

function getDataFromPost(){

    $data = [];
    if (!empty($_POST['firstname'])) {
        $data['firstname'] = $_POST['firstname'];
    } else {
        return ["error" => "Der Vorname muss gefüllt sein."];
    }

    if (!empty($_POST['name'])) {
        $data['name'] = $_POST['name'];
    } else {
        return ["error" => "Der Nachname muss gefüllt sein."];
    }

    if (!empty($_POST['streetname'])) {
        $data['streetname'] = $_POST['streetname'];
    } else {
        return ["error" => "Die Straße muss gefüllt sein."];
    }

    if (!empty($_POST['streetnumber'])) {
        $data['streetnumber'] = $_POST['streetnumber'];
    } else {
        return ["error" => "Die Hausnummer muss gefüllt sein."];
    }

    if (!empty($_POST['postcode'])) {
        $data['postcode'] = $_POST['postcode'];
    } else {
        return ["error" => "Die Postleitzahl muss gefüllt sein."];
    }

    if (!empty($_POST['city'])) {
        $data['city'] = $_POST['city'];
    } else {
        return ["error" => "Die Stadt muss gefüllt sein."];
    }

    if (!empty($_POST['birthday'])) {
        $data['birthday'] = $_POST['birthday'];
    } else {
        return ["error" => "Der Geburtstag muss gefüllt sein."];
    }

    if (!empty($_POST['iban']) || !empty($_POST['bic'])) {

        if(!empty($_POST['iban']) && !empty($_POST['bic'])) {
            $data['iban'] = $_POST['iban'];
            $data['bic'] = $_POST['bic'];
            $data['sepa'] = true;
        }
        else
        {
            return ["error" => "IBAN und BIC müssen gefüllt sein."];
        }
    }

    if(!empty($_POST['working'])){
        $data['working'] = true;
    }

    return $data;
}
































