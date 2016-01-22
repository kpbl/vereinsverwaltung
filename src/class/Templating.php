<?php

require_once dirname(__FILE__) .'/../../config.php';

class Templating
{

    private $tmpldir;
    private $logger;

    public function __construct($dir = -1){
        if($dir == -1) {
            $this->tmpldir = PATH_TEMPLATES;
        }
        else{
            $this->tmpldir = $dir;
        }

        $this->logger = new Logger();
    }

    public function renderWrapper($filename){

        $file = $this->tmpldir .$filename;
        if(file_exists($file)){
            $html = file_get_contents($file);
            $htmlParts = explode('%CONTENT%',$html);
            $partsLength = count($htmlParts);
            if($partsLength == 2){
                return $htmlParts;
            }
            else{
                //write error message to logfile
                if($partsLength < 2) {
                    $this->logger->writeError('Templating: Parameter %CONTENT% nicht gefunden');
                }
                elseif ($partsLength > 2){
                    $this->logger->writeError('Templating: Parameter %CONTENT% darf nur einmal verwendet werden');
                }

                return false;
            }
        }
        else {
            //write error message to logfile
            $this->logger->writeError('Templating: Die Datei ' .$file .' wurde nicht gefunden');
            return false;
        }
    }

    public function render($filename){
        $file = $this->tmpldir .$filename;
        if(file_exists($file)){
            $html = file_get_contents($file);
            if($html === false){
                $this->logger->writeError('Templating: Die Datei ' .$file .' konnte nicht gelesen werden');
                return false;
            }

            return $html;
        }
        else {
            //write error message to logfile
            $this->logger->writeError('Templating: Die Datei ' .$file .' wurde nicht gefunden');
            return false;
        }
    }

    public function getInfo(){
        return 'Verzeichnis: ' .$this->tmpldir;
    }
}