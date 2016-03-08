<?php

//Lade Klassen von  'src/class/'
function __autoload($classname)
{
    if(file_exists(dirname(__FILE__) .'/../class/' .$classname .'.php'))
        include dirname(__FILE__) .'/../class/' .$classname .'.php';
    else if(file_exists(dirname(__FILE__) .'/../tools/' .$classname .'.php'))
        include dirname(__FILE__) .'/../tools/' .$classname .'.php';
}

//Prüfen ob Benutzer angemeldet und redirect auf Login Seite, wenn nicht
function securityCheck()
{
    if(empty($_SESSION['username'])) {
        header('Location: ' .LINK_LOGIN);
    }
}

//Daten von Mitglieder Formular (create,edit) aus Post einlesen und Prüfen ob alles valide sonst Fehlermeldung
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
        $data['birthday'] = date_create_from_format('d.m.Y',$_POST['birthday']);
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

//Daten von Buchung Formular aus Post einlesen und Prüfen ob alles valide sonst Fehlermeldung
function getAccHistDataFromPost(){

    $data = [];
    if (!empty($_POST['account_history_description'])) {
        $data['description'] = $_POST['account_history_description'];
    } else {
        return ["error" => "Die Beschreibung muss gefüllt sein."];
    }

    if (!empty($_POST['account_history_money'])) {
        $data['money'] = $_POST['account_history_money'];
    } else {
        return ["error" => "Der Betrag muss gefüllt sein."];
    }

    if (!empty($_POST['account_history_date'])) {
        $data['date_payed'] = date_create_from_format('d.m.Y',$_POST['account_history_date']);
        if($data['date_payed'] === FALSE)
            return ["error" => "Das Format des Geburtstags ist nicht korrekt."];
    }
    
    if (!empty($_POST['account_id'])) {
        $data['account_id'] = $_POST['account_id'];
    } else {
        return ["error" => "Die Account Id fehlt."];
    }
    
    if (!empty($_POST['account_history_user']) && $_POST['account_history_user'] != "-1") {
        $data['user_id'] = $_POST['account_history_user'];
    }
    
    if (!empty($_POST['account_history_payed'])) {
        $data['payed'] = true;
    }
    else{
        $data['payed'] = false;
    }
    
    return $data;
}