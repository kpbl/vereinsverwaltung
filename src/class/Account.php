<?php

class Account {
    
    private $id;
    private $name;
    private $iban;
    private $bic;
    private $saldo;

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

    public function getName(){
            return $this->name;
    }

    public function setName($name){
            $this->name = $name;
    }

    public function getIban(){
            return $this->iban;
    }

    public function setIban($iban){
            $this->iban = $iban;
    }

    public function getBic(){
            return $this->bic;
    }

    public function setBic($bic){
            $this->bic = $bic;
    }

    public function getSepa(){
            return $this->saldo;
    }

    public function setSepa($saldo){
            $this->saldo = $saldo;
    }
}