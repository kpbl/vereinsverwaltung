<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vereinsverwaltung/src/conf/config.php';

securityCheck();
$dbmanager = new DBManager();

//Eintrag auf 'bezahlt' setzen
if(isset($_GET['payed'])){
    if(isset($_GET['date']) && !empty($_GET['date'])){
        $dateObj = date_create_from_format('d.m.Y',$_GET['date']);
        if(dateObj !== FALSE){        
        $accHist = $dbmanager->get('Account_History',$_GET['payed']);
        $accHist->setPayed(true);
        $accHist->setDate_payed($dateObj);
        if($dbmanager->update('Account_History', [$accHist]))
                $message = ['type' => 'success', 'text' => 'Der Eintrag wurde als Bezahlt gespeichert'];
        else
                $message = ['type' => 'danger', 'text' => 'Der Eintrag konnte nicht bearbeitet werden'];    
        $_SESSION['message'] = $message;
        header('location: ' .LINK_MONEY);
        }
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Das Datum muss im Format d.m.Y sein'];  
    }
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Geben Sie ein Datum an'];  
}

//Editierte Kontodaten speichern
if(isset($_POST['account_name'])){
    $account = $dbmanager->get('Account',$_POST['account_id']);    
    $account->setName($_POST['account_name']);
    $account->setIban($_POST['account_iban']);
    $account->setBic($_POST['account_bic']);
    
    if($dbmanager->update('Account', [$account]))
            $message = ['type' => 'success', 'text' => 'Die Kontodaten wurden aktualisiert'];
    else
            $message = ['type' => 'danger', 'text' => 'Die Kontodaten konnten nicht bearbeitet werden'];    
    $_SESSION['message'] = $message;
}

//Neue Buchung speichern
if(isset($_POST['account_history_description'])){
    //Formulardaten in Arrayspeichern und prüfen ob Valide -> sonst error Nachricht
    $data = getAccHistDataFromPost();
    if(!isset($data['error']))
    {        
        //Neues Objekt der Klasse Account_History mit übergebenen Daten in datenbank speichern
        if($dbmanager->persist('Account_History',[$data])){
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Das Mitglied wurde im System gespeichert'];
            //Seite neu laden ($_POST leer)--> sonst eingetragene Daten in Felder darstellen
            header('location: ' .LINK_MONEY);                
        }
        else{
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Die Daten konnten nicht gespeichert werde'];
        }
    }
    else{
        $_SESSION['message'] = ['text' => $data['error'],'type' => 'danger'];
    }
}

//Saldi für Konten aus history berechnen
$dbmanager->calcAccSaldo();
//Alle accounts aus Datenbank laden
$accounts = $dbmanager->getAll('Account',['name']);
//Alle Benutzer aus Datenbank laden (Auswahlbox neue Buchung)
$users = $dbmanager->getAll('User',['name']);

$tmpl = new Templating();
$wrappers = $tmpl->renderWrapper('layoutMenu.html');
if($wrappers) {
    echo $wrappers[0];
    
    
    foreach($accounts as $acc){
    ?>
    
    <div class="row">
        <div class="col-md-12">
            <button id="edit-acc" class="btn btn-default pull-right">Kontodaten bearbeiten</button>
        </div>
    </div>
<br>
    <div class="row">
        <form method="POST" action="">
            <div class="form-group col-md-3">
                <label for="account_name">Konto Bezeichnung</label>
                <input type="text" class="form-control" id="account_name" name="account_name" placeholder="Bezeichnung" value="<?php echo $acc->getName();?>" disabled>
            </div>
            <div class="form-group col-md-3">
                <label for="account_iban">IBAN</label>
                <input type="text" class="form-control" id="account_iban" name="account_iban" placeholder="IBAN" value="<?php echo $acc->getIban();?>" disabled>
            </div>
            <div class="form-group col-md-3">
                <label for="account_bic">BIC</label>
                <input type="text" class="form-control" id="account_bic" name="account_bic" placeholder="BIC" value="<?php echo $acc->getBic();?>" disabled>
            </div>
            <div class="form-group col-md-3">
                <label for="account_saldo">Kontostand</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="account_saldo" value="<?php echo $acc->getSaldo();?>" name="account_saldo" disabled>
                    <div class="input-group-addon">€</div>
                </div>
            </div>
            <div class="form-group col-md-12">
                <input type="hidden" name="account_id" value="<?php echo $acc->getId();?>">
                <input id="account_submit" type="submit" class="btn btn-primary pull-right" value="Speichern">
            </div>
        </form>
    </div>
    <div class="row">
        <a href="<?php echo LINK_PAYMENT .'?account='.$acc->getId();?>" class="btn btn-danger pull-right">Mitgliedsbeiträge generieren</a>        
    </div>

    <?php
        //Datumsspanne
        if(isset($_GET['startDate']) && isset($_GET['endDate'])){
            $startDate = DateTime::createFromFormat('d.m.Y', $_GET['startDate']);
            $endDate = DateTime::createFromFormat('d.m.Y', $_GET['endDate']);

            if($startDate === false || $endDate === false){
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Datum in Format d.m.Y'];
                //Seite neu laden --> falsches Format
                header('location: ' .LINK_MONEY); 
            }            
        }
        else{
            $endDate = new DateTime();
            $startDate = new DateTime();
            
            //enddatum --> 6Monate zurück
            $interv = DateInterval::createfromdatestring('-6 months');
            $startDate->add($interv);
        }
        
        $acc_histories = $dbmanager->getRange('Account_History',['payed','date_payed'],'ASC',['account_id' => $acc->getId()],['start' => $startDate,
                                                                                                                                    'end' => $endDate]);
        ?>

    <div class="row">
        <div class="col-md-1">
            <label for="range_start" class="pull-right">Von:</label>
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control input-sm datepicker-range" id="range_start" value="<?php echo $startDate->format('d.m.Y');?>">
        </div>
        <div class="col-md-1 col-md-offset-1">
            <label for="range_end" class="pull-right">Bis:</label>
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control input-sm datepicker-range" id="range_end" value="<?php echo $endDate->format('d.m.Y');?>">
        </div>
    </div>

    <div class="row">
        <h2>Zugänge</h2>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Beschreibung</th>
                    <th>Betrag</th>
                    <th>Mitglied</th>
                    <th>Bezahlt</th>
                    <th>Datum</th>
                    <th><span class="glyphicon glyphicon-wrench"></span></th>
                </tr>
            </thead>
            <tbody>
    <?php
    foreach ($acc_histories as $acc_hist){
        //Wenn Saldo negativ überspringen--> nur Zugänge
        if($acc_hist->getMoney() < 0)
            continue;
        
        //Zugeordneten Benutzer laden, wenn vorhanden
        $user = null;
        if($acc_hist->getUser_id() !== null)
            $user = $dbmanager->get('User',$acc_hist->getUser_id());
            
        echo '<tr>'
            . '<td>' .$acc_hist->getDescription() .'</td>'
            . '<td>' .$acc_hist->getMoney() .'€</td>'
            . '<td>' .($user === null ? 'Allgemeine Buchung':$user->getFirstname().' '.$user->getName()) .'</td>'
            . '<td>' .($acc_hist->getPayed() ? 'Ja <span class="glyphicon glyphicon-ok pull-right text-success" aria-hidden="true"></span>' : 'Nein <span class="glyphicon glyphicon-remove pull-right text-danger" aria-hidden="true"></span>' ) .'</td>'
            . '<td>' .($acc_hist->getDate_payed() != null ? date_create($acc_hist->getDate_payed())->format('d.m.Y'): '') .'</td>'
            . '<td><a href="' .LINK_MONEY .'?payed=' .$acc_hist->getId() .'" class="btn btn-xs btn-default payed-link' .($acc_hist->getPayed() ? ' disabled':'') .'"><span class="glyphicon glyphicon-ok"></span></a>'
            . ($acc_hist->getPayed() ? '</td>' : '<input type="text" class="invisible onepx datepicker-noico"></td>')
            . '</tr>';
    }
    ?>
                
            </tbody>    
        </table>
    </div>

    <div class="row">
        <h2>Abgänge</h2>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Beschreibung</th>
                    <th>Betrag</th>
                    <th>Mitglied</th>
                    <th>Bezahlt</th>
                    <th>Datum</th>
                    <th><span class="glyphicon glyphicon-wrench"></span></th>
                </tr>
            </thead>
            <tbody>
    <?php
    
    foreach ($acc_histories as $acc_hist){
        //Wenn Saldo positiv überspringen--> nur Abgänge
        if($acc_hist->getMoney() > 0)
            continue;
        
        //Zugeordneten Benutzer laden, wenn vorhanden
        $user = null;
        if($acc_hist->getUser_id() !== null)
            $user = $dbmanager->get('User',$acc_hist->getUser_id());
            
        echo '<tr>'
            . '<td>' .$acc_hist->getDescription() .'</td>'
            . '<td>' .$acc_hist->getMoney() .'€</td>'
            . '<td>' .($user === null ? 'Allgemeine Buchung':$user->getFirstname().' '.$user->getName()) .'</td>'
            . '<td>' .($acc_hist->getPayed() ? 'Ja <span class="glyphicon glyphicon-ok pull-right text-success" aria-hidden="true"></span>' : 'Nein <span class="glyphicon glyphicon-remove pull-right text-danger" aria-hidden="true"></span>' ) .'</td>'
            . '<td>' .($acc_hist->getDate_payed() != null ? date_create($acc_hist->getDate_payed())->format('d.m.Y'): '') .'</td>'
            . '<td><a href="' .LINK_MONEY .'?payed=' .$acc_hist->getId() .'" class="btn btn-xs btn-default payed-link' .($acc_hist->getPayed() ? ' disabled':'') .'"><span class="glyphicon glyphicon-ok"></span></a>'
            . ($acc_hist->getPayed() ? '</td>' : '<input type="text" class="invisible onepx datepicker-noico"></td>')
            . '</tr>';
    }
    ?>
                
            </tbody>    
        </table>
    </div>

    <div class="row">
        <button id="new-acc-hist" class="btn btn-default pull-right">neue Buchung eintragen</button>
    </div>
<br>
    <div id="account_history_form" class="bordered">
        <div class="row">
            <form method="POST" action="">
                <div class="form-group col-md-3">
                    <label for="account_history_description">Beschreibung</label>
                    <input type="text" class="form-control" id="account_history_description" name="account_history_description" placeholder="Beschreibung"
                           <?php if (isset($_POST['account_history_description'])) echo 'value="' . $_POST['account_history_description'] . '"'; ?>>
                </div>
                <div class="form-group col-md-3">
                    <label for="account_history_money">Betrag</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="account_history_money" name="account_history_money"
                               <?php if (isset($_POST['account_history_money'])) echo 'value="' . $_POST['account_history_money'] . '"'; ?>>
                        <div class="input-group-addon">€</div>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label for="account_history_user">Mitglied</label>
                    <select id="account_history_user" class="form-control" name="account_history_user">
                        <option value="-1">Allgemeine Buchung</option>
                        <?php
                        foreach($users as $usr){
                            echo '<option value="' .$usr->getId() .'">' .$usr->getFirstname() .' ' .$usr->getName() .'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="account_history_date">Datum</label>
                    <input type="text" class="form-control" id="account_history_date" name="account_history_date" placeholder="Datum"
                               <?php if (isset($_POST['account_history_date'])) echo 'value="' . $_POST['account_history_date'] . '"'; ?>>
                </div>
                <div class="form-group col-md-12">
                    <label>
                        Bezahlt <input type="checkbox" name="account_history_payed"
                                       <?php if (isset($_POST['account_history_payed']) && $_POST['account_history_payed']) echo 'checked'; ?>>
                    </label>
                </div>            
                <div class="form-group">
                    <input type="hidden" name="account_id" value="<?php echo $acc->getId();?>">
                    <input type="submit" class="btn btn-primary pull-right" value="Speichern">
                </div>
            </form>
        </div>
    </div>
    <?php
    }
    echo $wrappers[1];
}
else {
    echo $tmpl->render('error.html');
}
