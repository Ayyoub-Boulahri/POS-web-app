<!DOCTYPE html>
<html lang="en">

<?php
include "../database.php";
session_start();
$cmp = 0;
if ($_SESSION["log"] == 1) {
    $cmp = 1;
} else {
    echo $cmp;
    header("Location:../index.php");
}

if(isset($_POST["valider"])) {
    $full_name_client = $_POST["full_name_client"];
    $addresse = $_POST["addresse"];
    $code_postal = $_POST["code_postal"];
    $tel = $_POST["tel"];
    $requete = $db->prepare("INSERT INTO devis(full_name_client,addresse,code_postal,tel,date_devis) VALUES (?,?,?,?,CURDATE())");
    $requete->bindValue(1, $full_name_client);
    $requete->bindValue(2, $addresse);
    $requete->bindValue(3, $code_postal);
    $requete->bindValue(4, $tel);
    $requete->execute();
    $id = $db->lastInsertId();
    header("Location:addService.php?id_devis=" . $id);
}
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/icon.png">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v6.0.0-beta3/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/newService.css">
    <title>Nouveau Service</title>
    <style>
        #newService .nav-link {
            color: black;
        }

        #newService {
            background-color: white;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg ">
        <?php include "../includes/serviceMenu.php"; ?>
    </nav>

    <div class="container mt-5 col-5">
        <h2>Information de Client</h2>
        <form method="post">
            <div class="form-group  mb-2">
                <label for="full_name_client">Nom</label>
                <input type="text" class="form-control" id="full_name_client" name="full_name_client">
            </div>

            <div class="form-group mb-2">
                <label for="addresse">ICE</label>
                <input type="text" class="form-control" id="addresse" name="addresse">
            </div>

            <div class="form-group mb-2">
                <label for="code_postal">Code Postal</label>
                <input type="number" class="form-control" id="code_postal" name="code_postal">
            </div>

            <div class="form-group mb-2">
                <label for="tel">Telephone</label>
                <input type="tel" class="form-control" id="tel" name="tel">
            </div>
            <div class="form-group mb-2" style="display: flex; justify-content:center">
                <button type="submit" name="valider" class="btn btn-primary">Valider</button>
            </div>
        </form>
    </div>

    <!-- Add Bootstrap JS and jQuery (required for some Bootstrap features) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>