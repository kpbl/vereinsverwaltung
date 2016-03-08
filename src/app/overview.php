<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vereinsverwaltung/src/conf/config.php';

//Prüfen ob Benutzer angemeldet
securityCheck();

//Alle Benutzer laden
$dbmanager = new DBManager();
$users = $dbmanager->getAll('User',['name']);

$tmpl = new Templating();
$wrappers = $tmpl->renderWrapper('layoutMenu.html');
if($wrappers){
    echo $wrappers[0];
    ?>

    <div class="row">
        <a href="<?php echo LINK_CREATE?>" class="btn btn-default pull-right">Benutzer hinzufügen <span class="glyphicon glyphicon-plus"></span></a>
    </div>

    <br>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nachname</th>
                    <th>Vorname</th>
                    <th>Straße</th>
                    <th>Postleitzahl</th>
                    <th>Stadt</th>
                    <th>Geburtstag</th>
                    <th>Sepa</th>
                    <th>Arbeitstätig</th>
                    <th><span class="glyphicon glyphicon-wrench"></span></th>
                </tr>
            </thead>
            <tbody>
    <?php

    foreach($users as $user)
    {
        echo '<tr>'
                .'<td>' .$user->getName() .'</td>'
            .'<td>' .$user->getFirstname() .'</td>'
            .'<td>' .$user->getStreetname() .' ' .$user->getStreetnumber() .$user->getStreetad() .'</td>'
            .'<td>' .$user->getPostcode() .'</td>'
            .'<td>' .$user->getCity() .'</td>'
            .'<td>' .date_create($user->getBirthday())->format('d.m.Y') .'</td>'
            .'<td>';
        if($user->getSepa())
            echo 'Ja';
        else
            echo 'Nein';

        echo '</td>'
            .'<td>';
        if($user->getWorking())
            echo 'Ja';
        else
            echo 'Nein';

        echo '</td>'
            .'<td><a href="' .LINK_EDIT .'?user=' .$user->getId() .'" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>'
            .'<a href="' .LINK_EDIT .'?user=' .$user->getId() .'&delete=1" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-remove"></span></a></td>'
            .'</tr>';
    }

    ?>

            </tbody>
        </table>
    </div>

    <?php
    echo $wrappers[1];
}
else{
    echo $tmpl->render('error.html');
}

