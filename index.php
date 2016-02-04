<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vereinsverwaltung/src/conf/config.php';

if(isset($_SESSION['username']))
{
    header('Location: ' .LINK_HOME);
}

if($_POST)
{

    if(!empty($_POST['username']) && !empty($_POST['password'])) {

        $username = $_POST['username'];
        $error = checkUserData($_POST['username'], $_POST['password']);

        if($error == "")
        {
            header("Location: " .LINK_HOME);
        }
        else{
            $_SESSION['message'] = ['text' => $error,'type' => 'danger'];
        }
    }
}

$tmpl = new Templating();
$wrappers = $tmpl->renderWrapper('layout.html');

if($wrappers) {
    echo $wrappers[0];
    ?>
        <form action="" method="post">
            <!--<input type="hidden" name="_csrf_token" value="{{ csrf_token }}" />-->

            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input type="text" class="form-control" placeholder="Benutzername" id="username" name="username"
                       value="<?php if (!empty($username))
                           echo $username; ?>"
                       required="required"/>
            </div>
            <br>
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-certificate"></i></span>
                <input type="password" class="form-control" placeholder="Passwort" id="password" name="password"
                       required="required"/>
            </div>

            <div class="form-group pull-right">
                <br>
                <input type="submit" class="btn btn-default" id="submit" name="submit" value="Login"/>
            </div>
        </form>

    <?php
    echo $wrappers[1];
}
else{

    echo $tmpl->render('error.html');

}

function checkUserData($username,$password)
{

    $error = "";
    $dbconn = new mysqli('localhost',DBUSER,DBPASSWD,DATABASE);
    $userData = $dbconn->query("SELECT * FROM " .DATABASE .".login WHERE username = '" .$username ."'");

    if($userData->num_rows != 0)
    {

        $row = $userData->fetch_assoc();
        $correctPw = password_verify($password,$row['password']);

        if(!$correctPw)
            $error = "Falsche Benutzerdaten.";
        else
        {
            $_SESSION['username'] = $row['username'];
        }
    }
    else
    {
        $error = "Falsche Benutzerdaten.";
    }

    return $error;
}
