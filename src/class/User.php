<?php

class User {
    
    private $id;
    private $name;
    private $firstname;
    private $streetname;
    private $streetnumber;
    private $streetad;
    private $postcode;
    private $city;
    private $birthday;
    private $iban;
    private $bic;
    private $sepa;
    private $working;
    private $deleted;
    
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

    public function getFirstname(){
            return $this->firstname;
    }

    public function setFirstname($firstname){
            $this->firstname = $firstname;
    }

    public function getStreetname(){
            return $this->streetname;
    }

    public function setStreetname($streetname){
            $this->streetname = $streetname;
    }

    public function getStreetnumber(){
            return $this->streetnumber;
    }

    public function setStreetnumber($streetnumber){
            $this->streetnumber = $streetnumber;
    }

    public function getStreetad(){
            return $this->streetad;
    }

    public function setStreetad($streetad){
            $this->streetad = $streetad;
    }

    public function getPostcode(){
            return $this->postcode;
    }

    public function setPostcode($postcode){
            $this->postcode = $postcode;
    }

    public function getCity(){
            return $this->city;
    }

    public function setCity($city){
            $this->city = $city;
    }

    public function getBirthday(){
            return $this->birthday;
    }

    public function setBirthday($birthday){
            $this->birthday = $birthday;
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
            return $this->sepa;
    }

    public function setSepa($sepa){
            $this->sepa = $sepa;
    }

    public function getWorking(){
            return $this->working;
    }

    public function setWorking($working){
            $this->working = $working;
    }

    public function getDeleted(){
        return $this->deleted;
    }

    public function setDeleted($deleted){
        $this->deleted = $deleted;
    }
}