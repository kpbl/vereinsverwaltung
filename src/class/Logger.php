<?php

require_once dirname(__FILE__) .'/../../config.php';

class Logger
{

    private $logdir;
    private $errorlog;
    private $debuglog;

    public function __construct(){
        $this->logdir = PATH_LOG;
        $this->errorlog = 'error.log';
        $this->debuglog = 'debug.log';
    }

    public function writeError($message){

        if(file_exists($this->logdir)){
            $logLine = '[' .date('d.m.Y H:i:s') .'] ' .$message ."\n";
            $logfile = $this->logdir .$this->errorlog;
            return file_put_contents($logfile,$logLine,FILE_APPEND);
        }
        else {
            return false;
        }
    }

    public function writeDebug($message){
        if(file_exists($this->logdir)){
            $logLine = '[' .date('d.m.Y H:i:s') .'] ' .$message ."\n";
            $logfile = $this->logdir .$this->debuglog;
            return file_put_contents($logfile,$logLine,FILE_APPEND);
        }
        else {
            return false;
        }
    }

    public function getInfo(){
        return 'Verzeichnis: ' .$this->logdir .', Dateinamen: ' .$this->errorlog .'(Fehler), ' .$this->debuglog .'(Debug)';
    }
}