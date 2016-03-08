<?php

class Account_History {
    
    private $id;
    private $money;
    private $description;
    private $user_id;
    private $account_id;
    private $payed;
    private $date_payed;

    public function __construct() {
    }

    public function fetchData($data){

        foreach($data as $key => $item) {
            $setter = 'set' .$key;
            $this->$setter($item);
        }
    }
    
    public function getId(){
            return $this->id;
    }

    public function setId($id){
            $this->id = $id;
    }

    public function getMoney(){
            return $this->money;
    }

    public function setMoney($money){
            $this->money = $money;
    }

    public function getDescription(){
            return $this->description;
    }

    public function setDescription($description){
            $this->description = $description;
    }

    public function getUser_id(){
            return $this->user_id;
    }

    public function setUser_id($user_id){
            $this->user_id = $user_id;
    }
    
    public function getAccount_id(){
            return $this->account_id;
    }

    public function setAccount_id($account_id){
            $this->account_id = $account_id;
    }

    public function getPayed(){
            return $this->payed;
    }

    public function setPayed($payed){
            $this->payed = $payed;
    }
    
    public function getDate_payed(){
            return $this->date_payed;
    }

    public function setDate_payed($date_payed){
            $this->date_payed = $date_payed;
    }
}