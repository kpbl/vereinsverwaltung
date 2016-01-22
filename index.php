<?php
require_once 'config.php';

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
    }
}

$tmpl = new Templating();
$wrappers = $tmpl->renderWrapper('layout.html');

if($wrappers) {
    echo $wrappers[0];
    ?>

    <div class="animated fadeIn col-md-offset-2 col-md-8 login">
        <h2>Vereinsverwaltung</h2>
        <br>
        <?php
        if (!empty($error)) {

            echo '<div class="alert alert-danger" role="alert">'
                . $error
                . '</div><br>';
        }
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

            <div class="input-group pull-right">
                <br>
                <input type="submit" class="btn btn-default" id="submit" name="submit" value="Login"/>
            </div>
        </form>
    </div>

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
            $error = "Falsches Passwort.";
        else
        {
            $_SESSION['username'] = $row['username'];
        }
    }
    else
    {
        $error = "Benutzername nicht gefunden.";
    }

    return $error;
}
