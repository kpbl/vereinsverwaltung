<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vereinsverwaltung/src/conf/config.php';
securityCheck();

//Verbindung mit Datenbank aufbauen
$dbmanager = new DBManager();
if($dbmanager->isConnected()){
    $infos = $dbmanager->getInfos();
}else{
    echo $tmpl->render('error.html');
}

$tmpl = new Templating();
$wrappers = $tmpl->renderWrapper('layoutMenu.html');
if($wrappers){
    echo $wrappers[0];
?>

    <div class="jumbotron home">
      <p>        
        <div class="row">
            <div class="col-md-6">
                <h3><u>Vereinsübersicht:</u></h3>
                <dl class="dl-horizontal">
                    <dt>Mitglieder: </dt>
                    <dd><?php echo $infos['total']?></dd>
                    <dt>Arbeitstätig: </dt>
                    <dd><?php echo $infos['working']?></dd>
                    <dt>Volljährig: </dt>
                    <dd><?php echo $infos['adult']?></dd>
                    <dt>Sepa hinterlegt: </dt>
                    <dd><?php echo $infos['sepa']?></dd>
                </dl>
            </div>
            <div class="col-md-6">
                <h3><u>Kontoübersicht:</u></h3>
                <?php
                foreach($infos['accounts'] as $accInf){
                ?>
                <dl class="dl-horizontal">
                    <dt>Konto: </dt>
                    <dd><?php echo $accInf['name']?></dd>
                    <dt>Saldo: </dt>
                    <dd><?php echo $accInf['saldo']?></dd>
                    <dt><u>Zugänge</u></dt>
                    <dd></dd>
                    <dt>offene Buchungen: </dt>
                    <dd><?php echo $accInf['topaypos']?></dd>
                    <dt>Summe: </dt>
                    <dd><?php echo $accInf['topayposmoney']?>€</dd>
                    <dt><u>Abgänge</u></dt>
                    <dd></dd>
                    <dt>offene Buchungen: </dt>
                    <dd><?php echo $accInf['topayneg']?></dd>
                    <dt>Summe: </dt>
                    <dd><?php echo $accInf['topaynegmoney']?>€</dd>
                </dl>
                <?php                    
                }
                ?>
            </div>
        </div>
      </p>
    </div>
<?php
    echo $wrappers[1];
}
else{
    echo $tmpl->render('error.html');
}

