<?php
//Koniguration laden
require_once '../../config.php';


//Nur ausführen, wenn cli sonst Fehlermeldung
if(php_sapi_name() == 'cli')
{

    $dbconn = new mysqli('localhost',DBUSER,DBPASSWD,DATABASE);
    if ($dbconn->connect_errno) {
        echo "Failed to connect to MySQL: (" . $dbconn->connect_errno . ") " . $dbconn->connect_error ."\n";
        die();
    }

    //Benutzernamen einlesen (eingabe wird mit NEWLINE abgeschlossen)
    echo "Geben sie einen Benutzernamen ein:\n";
    $username = rtrim(fgets(STDIN),"\n");

    while(!usernameAvailable($username,$dbconn))
    {

        echo "Der Benutzername ist bereits vorhanden. Wählen sie einen anderen:\n";
        $username = rtrim(fgets(STDIN),"\n");
    }

    //Passwort lesen mit echo aus
    echo "Geben sie ein Passwort für den Benutzer ein:\n";
    $pw = getPassword();

    //Benutzer in Datenbank anlegen
    createUser($username,$pw,$dbconn);
}
else
{
    echo 'Zugriff verweigert!';
}

function usernameAvailable($name,$conn)
{

    $available = true;
    $dbRes = $conn->query("SELECT * FROM " .DATABASE .".login WHERE username = '" .$name ."'");

    if($dbRes->num_rows != 0)
        $available = false;

    $dbRes->close();

    return $available;
}

function createUser($name,$passwd, $conn)
{

    $pwhash = password_hash($passwd, PASSWORD_DEFAULT);
    $query = "INSERT INTO " .DATABASE .".login (username,password) VALUES ('" .$name ."','" .$pwhash ."')";
    //echo $query ."\n";
    $insertRes = $conn->query($query);

    if($insertRes)
        echo "Benutzer erfolgreich angelegt.\n";
    else
        echo "Fehler beim anlegen des Benutzers.\n";
}

//Stellt die Ausgabe beim Einlesen über die Komandozeile ab und liefert den eingegeben Wert
function getPassword()
{
    // Get current style
    $oldStyle = shell_exec('stty -g');

    shell_exec('stty -echo');
    $password = rtrim(fgets(STDIN), "\n");

    // Reset old style
    shell_exec('stty ' . $oldStyle);

    // Return the password
    return $password;
}