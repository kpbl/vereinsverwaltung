<?php

require_once dirname(__FILE__) .'/../../config.php';

class DBManager
{

    private $mysqli;
    private $logger;
    private $connected;

    public function __construct($dbname = null,$dbuser = null,$dbpass = null){

        if($dbname == null)
            $dbname = DATABASE;
        if($dbuser == null)
            $dbuser = DBUSER;
        if($dbpass == null)
            $dbpass = DBPASSWD;

        $this->logger = new Logger();

        $this->mysqli = new mysqli('localhost',$dbuser,$dbpass,$dbname);
        if ($this->mysqli->connect_errno) {
            $this->connected = false;
            $this->logger->writeError("Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error);
        }
        else{
            $this->connected = true;
        }
    }

    public function isConnected(){
        return $this->connected;
    }

    public function getAll($classname,$order = null, $orderType = 'ASC'){

        $entities = [];
        $sql = 'SELECT * FROM ' .strtolower($classname);

        if($order != null){
            $sql .= ' ORDER BY ';
            foreach($order as $ord){
                $sql .= $ord .' ';
            }
            $sql .= $orderType;
        }
        $sqlRes = $this->mysqli->query($sql);

        if($sqlRes) {
            while ($row = $sqlRes->fetch_assoc()) {

                $entity = new $classname();
                $entity->fetchData($row);
                $entities[] = $entity;
            }
        }
        else{
            $this->logger->writeError('DBManager: Fehler bei Statement "' .$sql .'"');
            return false;
        }

        return $entities;
    }

    public function get($classname,$id){

        $sql = 'SELECT * FROM ' .strtolower($classname) .' WHERE id = ' .$id;
        $sqlRes = $this->mysqli->query($sql);

        if($sqlRes) {
            $entity = new $classname();
            while ($row = $sqlRes->fetch_assoc()) {
                $entity->fetchData($row);
            }
            return $entity;
        }
        else{
            $this->logger->writeError('DBManager: Fehler bei Statement "' .$sql .'"');
            return false;
        }
    }

    public function persist($classname,$data){

        foreach($data as $dataEntity) {

            $sql = 'INSERT INTO ' . strtolower($classname) .' (';

            if(gettype($dataEntity) == "object")
                $dataEntity = (array)$dataEntity;
            $i = 0;
            foreach ($dataEntity as $key => $property){
                if($key != 'id') {
                    $sql .= $key;
                }

                if($i < count($dataEntity)-1)
                    $sql .= ',';

                $i++;
            }

            $sql .= ') VALUES (';
            $i = 0;
            foreach ($dataEntity as $key => $property){
                if($key != 'id') {

                    $sql .= $this->convertProperty($property) ;
                }

                if($i < count($dataEntity)-1)
                    $sql .= ',';

                $i++;
            }
            $sql .= ')';

            $sqlRes = $this->mysqli->query($sql);
            if(!$sqlRes) {
                $this->logger->writeError("DBManager: query failed '" .$sql ."' (" . $this->mysqli->errno . ") " . $this->mysqli->error);
                return false;
            }
        }

        return true;
    }

    private function convertProperty($value){

        switch(gettype($value)){
            case 'boolean':
                return (int)$value;
            case 'string':
                return "'" .$value ."'";
            default:
                return $value;
        }
    }
}