<?php

//Loading classes from path 'src/class/'
function __autoload($classname)
{

    if(file_exists(dirname(__FILE__) .'/../class/' .$classname .'.php'))
        include dirname(__FILE__) .'/../class/' .$classname .'.php';
}

//Check if logged in and redirect if false
function securityCheck()
{

    if(empty($_SESSION['username'])) {
        header('Location: ' .LINK_LOGIN);
        die();
    }
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
        $data['birthday'] = date_create_from_format('m.d.Y',$_POST['birthday']);
        if($data['birthday'] === FALSE)
            return ["error" => "Das Format des Geburtstags ist nicht korrekt."];

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
    else {
        $data['iban'] = "";
        $data['bic'] = "";
        $data['sepa'] = false;
    }

    if(!empty($_POST['working'])){
        $data['working'] = true;
    }
    else {
        $data['working'] = false;
    }

    return $data;
}