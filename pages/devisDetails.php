<!DOCTYPE html>
<html lang="en">

<?php
include "../database.php";
session_start();
$cmp = 0;
if ($_SESSION["log"] == 1 && !empty($_GET["show"])) {
    $cmp = 1;
} else {
    echo $cmp;
    header("Location:ventes.php");
}

if (!empty($_GET["deleteS"])) {
    $id_service = $_GET["deleteS"];
    $id_devis = $_GET["show"];
    $total = $_GET["total"];
    $requete = $db->prepare("UPDATE devis SET total_devis_ht = total_devis_ht - ? WHERE id_devis = ?");
    $requete->bindValue(1, $total);
    $requete->bindValue(2, $id_devis);
    $requete->execute();
    $requete = $db->prepare("UPDATE devis SET total_devis_ttc = total_devis_ttc - (? + ((? * 20) / 100)) WHERE id_devis = ?");
    $requete->bindValue(1, $total);
    $requete->bindValue(2, $total);
    $requete->bindValue(3, $id_devis);
    $requete->execute();
    $requete = $db->prepare("DELETE FROM service WHERE id_service = ?");
    $requete->bindValue(1, $id_service);
    $requete->execute();
    header("Location:devisDetails.php?show=" . $_GET["show"]);

}


?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/icon.png">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v6.0.0-beta3/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/ventes.css">
    <title>Details</title>
    <style>
        #showServices .nav-link {
            color: black;
        }

        #showServices {
            background-color: white;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <?php
        include "../includes/serviceMenu.php";
        ?>
    </nav>

    <div class="top">
        <div class="searchDiv">
            <span class="input-group-text" style="background-color: #7b2cbf;" id="basic-addon1"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i></span>
            <input type="text" class="form-control" id="search" placeholder="recherche par description . . ." aria-label="Username" aria-describedby="basic-addon1">
        </div>
    </div>
    <div class="container mt-5">
        <table class="table">
            <tr id="header">
                <th>DESCTIPTION</th>
                <th>QUANTITE</th>
                <th>PRIX UNITAIRE HT</th>
                <th>PRIX TOTAL HT</th>
                <th>SUPPRIMER</th>
            </tr>

            <?php
            $requete = $db->prepare("SELECT * FROM service WHERE id_devis = ?");
            $requete->bindValue(1, $_GET["show"]);
            $requete->execute();
            $services = $requete->fetchAll(PDO::FETCH_ASSOC);
            foreach ($services as $service) {
                echo "<tr>";
                echo "<td>" . nl2br($service["description"]). "</td>";
                echo "<td>" . $service["quantite"] . "</td>";
                echo "<td>" . $service["prix_unit_service"] . "</td>";
                echo "<td>" . $service["total_ht_service"] . " DH</td>";
                echo '<td><a href="?deleteS=' . $service["id_service"] . '&show=' . $_GET["show"] . '&total=' . $service["total_ht_service"] . '" style="color:#7b2cbf"><i class="fa-solid fa-trash fa-lg" style="color:#7b2cbf;"></i></a></td>';
                echo "</tr>";
            }
            ?>

        </table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <script>
        $('#search').keyup(function(e) {
            var search = document.getElementById("search").value;
            $.ajax({
                type: 'POST',
                url: "devisDetailsSearch.php",
                data: {
                    'search': search,
                    'id': <?php echo $_GET["show"] ?>
                },
                success: function(data) {
                    $('.table').html(data);
                },
            });
        });
    </script>

</body>

</html>