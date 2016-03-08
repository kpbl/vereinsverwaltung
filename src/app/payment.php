<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vereinsverwaltung/src/conf/config.php';

securityCheck();
if(isset($_GET['account'])){
    $dbmanager = new DBManager();
    
    $users = $dbmanager->getAll('User');
    $payments = [];
    foreach ($users as $user){
        $payment['description'] = 'Mitgliedsbeitrag';
        $payment['money'] = calcMoney($user);
        $payment['account_id'] = $_GET['account'];
        $payment['user_id'] = $user->getId();
        
        $payments[] = $payment;
    }
    $dbmanager->persist('Account_History', $payments);
    
    $_SESSION['message'] = ['type' => 'success', 'text' => 'Die Mitgliedsbeitrage wurden eingefordert'];
    header('location: '.LINK_MONEY);
}
else{
    header('location: '.LINK_MONEY);
}

function calcMoney($usr){
    
    //Standard Betrag 10€, wenn nicht volljährig 
    $money = 10.00;
    //Alter berechnen
    $birth = new DateTime($usr->getBirthday());
    $today = new DateTime();
    $age = $birth->diff($today);
    
    //Volljährig/Arbeit --> 20€
    //Volljährig/nicht Arbeit --> 15€
    if($age->y >= 18){
        if($usr->getWorking())
            $money = 20.00;
        else
            $money = 15.00;
    }
    return $money;
}