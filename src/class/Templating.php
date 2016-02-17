<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vereinsverwaltung/src/conf/config.php';

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

    //Template Datei einlesen und bei '%CONTENT%' trennen, um zwei strings zurückzugeben
    public function renderWrapper($filename){

        $file = $this->tmpldir .$filename;
        if(file_exists($file)){
            $html = file_get_contents($file);

            //Wenn Nachricht in Session, dann darstellen
            $html = $this->insertParameters($html);
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

    //Template Datei einlesen und als String zurückgeben
    public function render($filename){
        $file = $this->tmpldir .$filename;
        if(file_exists($file)){
            $html = file_get_contents($file);
            if($html === false){
                $this->logger->writeError('Templating: Die Datei ' .$file .' konnte nicht gelesen werden');
                return false;
            }

            //Wenn Nachricht in Session, dann darstellen
            $html = $this->insertParameters($html);
            return $html;
        }
        else {
            //write error message to logfile
            $this->logger->writeError('Templating: Die Datei ' .$file .' wurde nicht gefunden');
            return false;
        }
    }

    //Template Verzeichnis anzeigen
    public function getInfo(){
        return 'Verzeichnis: ' .$this->tmpldir;
    }

    //Wenn Parameter in Template erstzen
    private function insertParameters($htmlCode)
    {

        $newHtml = $htmlCode;
        $messageHtml = "";
        //Parameter %MESSAGE% mit html alert wenn SESSION['message'] gesetzt
        if(isset($_SESSION['message']))
        {
            $messageHtml = '<div class="alert alert-' .$_SESSION['message']['type'] .' alert-dismissible" role="alert">
                                <button class="close" aria-label="Close" data-dismiss="alert" type="button">
                                    <span aria-hidden="true">×</span>
                                </button>'
                            . $_SESSION['message']['text']
                            .'</div>';

            unset($_SESSION['message']);
        }

        $newHtml = str_replace('%MESSAGE%',$messageHtml,$newHtml);

        //Parameter %USERNAME% mit angemeldetem Benutzer ersetzen
        if(isset($_SESSION['username']))
        {
            $newHtml = str_replace('%USERNAME%',$_SESSION['username'],$newHtml);
        }
        
        return $newHtml;
    }
}