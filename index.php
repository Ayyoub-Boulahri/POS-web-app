<!DOCTYPE html>
<html lang="en">

<?php
if (isset($_SESSION["id_user"]))
    header("Location:logout.php");

include("database.php");
session_start();

//!Login 
if (isset($_POST["connect"])) {
    $id = $_POST["id"];
    $requete = $db->prepare("select count(*) as count,type_user,username from user where id_user=?");
    $requete->bindValue(1, $id);
    $requete->execute();
    $login = $requete->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION["log"] = 0;
    if ($login[0]["count"] == 1) {
        $_SESSION["type_user"] = $login[0]["type_user"];
        $_SESSION["id_user"] = $id;
        $_SESSION["username"] = $login[0]["username"];
        $_SESSION["log"] = 1;
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
    <link rel="stylesheet" href="css/index.css">
    <title>Connexion</title>
</head>

<body>
    <!-- Main Content -->
    <div class="container-fluid">
        <div class="container">
            <img src="images/logo.png" alt="">
            <h1 class="title">Scanner Votre Clé Pour Se connecter a Votre Compte</h1>
            <form method="post">
                <input class="submit" type="text" id="myInput" name="id" autofocus required autocomplete="off">
                <button class="submit" type="submit" name="connect"></button>
            </form>
            <button onclick="window.location.href='index1.php'" class="btn btn-primary">Login manuel</button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputElement = document.getElementById("myInput");

            inputElement.addEventListener("blur", function() {
                setTimeout(function() {
                    inputElement.focus();
                }, 0);
            });
        });
    </script>
</body>

</html>