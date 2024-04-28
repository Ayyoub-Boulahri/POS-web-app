<!DOCTYPE html>
<html lang="en">

<?php
include("database.php");
session_start();

//!Login 
if (isset($_POST["connect"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $requete = $db->prepare("select count(*) as count,type_user,id_user from user where username=? and password=?");
    $requete->bindValue(1, $username);
    $requete->bindValue(2, $password);
    $requete->execute();
    $login = $requete->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION["log"] = 0;
    if ($login[0]["count"] == 1) {
        $_SESSION["log"] = 1;
        $_SESSION["type_user"] = $login[0]["type_user"];
        $_SESSION["id_user"] = $login[0]["id_user"];
        $_SESSION["log"] = 1;
        $userId = $login[0]["id_user"];
        if ($login[0]["type_user"] == "caise") {
            header("Location:pages/stock.php");
        } else if ($login[0]["type_user"] == "admin") {
            header("Location:pages/stock.php");
        } else if ($login[0]["type_user"] == "service") {
            header("Location:pages/newService.php");
        }
    }

    echo '<div class="alert alert-danger d-flex justify-content-center " role="alert" >INCORRECT INFORMATIONS</div>';
}

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="images/icon.png">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v6.0.0-beta3/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/login.css">
    <title>Connexion</title>
</head>

<body>
    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row main-content bg-success text-center">
            <div class="col-md-12 col-xs-12 col-sm-12 login_form ">
                <div class="container-fluid">
                    <div class="row">
                        <h2>Connexion</h2>
                    </div>
                    <div class="row">
                        <form control="" class="form-group" action="" method="post">
                            <div class="row">
                                <input type="text" name="username" id="username" class="form__input" placeholder="Username">
                            </div>
                            <div class="row">
                                <!-- <span class="fa fa-lock"></span> -->
                                <input type="password" name="password" id="password" class="form__input" placeholder="Password">
                            </div>
                            <div class="row buttons">
                                <input type="submit" name="connect" value="S'identifier" class="btn">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>