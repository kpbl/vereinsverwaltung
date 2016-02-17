<?php

require_once $_SERVER['DOCUMENT_ROOT'] .'/vereinsverwaltung/src/conf/config.php';

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

    //Verbindung prüfen
    public function isConnected(){
        return $this->connected;
    }

    //Alle Eintrage einer Klasse aus der Datenbank laden
    public function getAll($classname,$order = null, $orderType = 'ASC'){

        $entities = [];
        $sql = 'SELECT * FROM ' .strtolower($classname);

        //Wenn Klasse deleted Flag hat, auf deleted=0 prüfen (nicht gelöschte Objekte)
        if(property_exists($classname,'deleted'))
            $sql .= ' WHERE deleted=0';

        //Wenn Eigenschaften zum Sortieren angegeben, order by an string anfügen
        if($order != null){
            $sql .= ' ORDER BY ';
            foreach($order as $ord){
                $sql .= $ord .' ';
            }
            $sql .= $orderType;
        }

        //SQL in Log Datei speichern, wenn debug
        if(DEBUG)
            $this->logger->writeDebug("DBManager: getAll '" .$sql ."'");

        //SQL ausführen
        $sqlRes = $this->mysqli->query($sql);
        if($sqlRes) {
            //Wenn query erfolgreich Objekte in array speichern (assoziativ)
            while ($row = $sqlRes->fetch_assoc()) {

                $entity = new $classname();
                $entity->fetchData($row);
                $entities[] = $entity;
            }
        }
        else{
            //Bei Fehler false zurückgeben und Fehlermeldung in error log
            $this->logger->writeError('DBManager: Fehler bei Statement "' .$sql .'"');
            return false;
        }

        //gefundene Objekte zurückgeben, wenn keine EInträge vorhanden dann leer
        return $entities;
    }

    //Bestimmten eintrag einer KLasse aus DB laden
    public function get($classname,$id){

        //Select statement
        $sql = 'SELECT * FROM ' .strtolower($classname) .' WHERE id = ' .$id;
        $sqlRes = $this->mysqli->query($sql);

        //Schreib Statement in Debug Log
        if(DEBUG)
            $this->logger->writeDebug("DBManager: get '" .$sql ."'");

        //Wenn statement erfolgreich, in Objekt speichern
        if($sqlRes) {
            $entity = new $classname();
            while ($row = $sqlRes->fetch_assoc()) {
                $entity->fetchData($row);
            }
            return $entity;
        }
        else{
            //Bei Fehler return false und in error log schreiben
            $this->logger->writeError('DBManager: Fehler bei Statement "' .$sql .'"');
            return false;
        }
    }

    //Informationen über Einträge ausgeben
    public function getInfos()
    {
        
        $infos = [];
        
        //Select statements
        $statements = [];
        $statements[] = 'SELECT COUNT(id) as total FROM user WHERE deleted=0';
        $statements[] = 'SELECT COUNT(id) as working FROM user WHERE deleted=0 AND working=1';
        $statements[] = 'SELECT COUNT(id) as sepa FROM user WHERE deleted=0 AND sepa=1';
        $statements[] = "SELECT COUNT(id) as adult FROM user WHERE deleted=0 AND birthday<='" .date('Y-m-d', strtotime('-18 years')) ."'";
        
        foreach ($statements as $sql){
            //Schreib Statement in Debug Log
            if(DEBUG)
                $this->logger->writeDebug("DBManager: get '" .$sql ."'");
            
            $sqlRes = $this->mysqli->query($sql);
            foreach ($sqlRes->fetch_assoc() as $key => $col){
                $infos[$key] = $col;
            }
        }        

        return $infos;
    }
    
    //Neues Objekt in Datenbank speichern
    public function persist($classname,$data){

        foreach($data as $dataEntity) {

            $sql = 'INSERT INTO ' . strtolower($classname) .' (';

            //Prüfen ob $data objekt --> intanz der klasse
            $isObject = false;
            if(gettype($dataEntity) == "object") {
                $dataEntity = (array)$dataEntity;
                $isObject = true;
            }

            $i = 0;
            foreach ($dataEntity as $key => $property){
                if($isObject) {
                    $keyParts = explode("\0", $key);
                    $key = $keyParts[count($keyParts)-1];
                }

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
                if($isObject) {
                    $keyParts = explode("\0", $key);
                    $key = $keyParts[count($keyParts)-1];
                }

                if($key != 'id') {

                    $sql .= $this->convertProperty($property) ;
                }

                if($i < count($dataEntity)-1)
                    $sql .= ',';

                $i++;
            }
            $sql .= ')';

            if(DEBUG)
                $this->logger->writeDebug("DBManager: persist '" .$sql ."'");

            $sqlRes = $this->mysqli->query($sql);
            if(!$sqlRes) {
                $this->logger->writeError("DBManager: query failed '" .$sql ."' (" . $this->mysqli->errno . ") " . $this->mysqli->error);
                return false;
            }
        }

        return true;
    }

    public function update($classname,$data){

        foreach($data as $dataEntity) {
            $sql = 'UPDATE ' . strtolower($classname) .' SET ';

            //Prüfen ob $data objekt --> intanz der klasse
            $isObject = false;
            if(gettype($dataEntity) == "object") {
                $dataEntity = (array)$dataEntity;
                $isObject = true;
            }

            $i = 0;
            foreach ($dataEntity as $key => $property){
                if($isObject) {
                    $keyParts = explode("\0", $key);
                    $key = $keyParts[count($keyParts)-1];
                }

                if($key != 'id') {
                    if($property !== NULL) {
                        if($i != 1)
                            $sql .= ',';

                        $sql .= $key . '=' . $this->convertProperty($property);
                    }
                }
                else {
                    $sqlCondition = ' WHERE id=' .$property;
                }
                $i++;
            }
            $sql .= $sqlCondition;
            if(DEBUG)
                $this->logger->writeDebug("DBManager: update '" .$sql ."'");

            $sqlRes = $this->mysqli->query($sql);
            if(!$sqlRes) {
                $this->logger->writeError("DBManager: query failed '" .$sql ."' (" . $this->mysqli->errno . ") " . $this->mysqli->error);
                return false;
            }
        }

        return true;
    }

    public function delete($classname,$id){

            $sql = 'UPDATE ' . strtolower($classname) .' SET deleted=1 WHERE id=' .$this->convertProperty($id);

            if(DEBUG)
                $this->logger->writeDebug("DBManager: update '" .$sql ."'");

            $sqlRes = $this->mysqli->query($sql);
            if(!$sqlRes) {
                $this->logger->writeError("DBManager: query failed '" .$sql ."' (" . $this->mysqli->errno . ") " . $this->mysqli->error);
                return false;
            }


        return true;
    }

    //Eigenschaft je nach typ anpassen für sql statement
    private function convertProperty($value){

        switch(gettype($value)){
            case 'boolean':
                return (int)$value;
            case 'string':
                return "'" .$value ."'";
            case 'object':
                if($value instanceof DateTime)
                    return "'" .date_format($value,'Y-d-m') ."'";
                else
                    return "'" .$value ."'";
            default:
                return $value;
        }
    }
}