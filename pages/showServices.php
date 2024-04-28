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

$requete = $db->prepare("DELETE FROM devis WHERE total_devis_ttc = 0 or total_devis_ht = 0");
$requete->execute();

if (isset($_GET["delete"])) {
    if (isset($_GET["confirm"]) && $_GET["confirm"] === "true") {
        $id_devis = $_GET["delete"];
        $requete = $db->prepare("DELETE FROM devis WHERE id_devis = ?");
        $requete->bindValue(1, $id_devis);
        $requete->execute();
        header("Location:showServices.php");
    } else {
        echo "<script>
                var confirmDelete = confirm('Voulez vous vraiment supprimer ce Devis?');
                if (confirmDelete) {
                    window.location.href = '?delete=" . $_GET["delete"] . "&confirm=true';
                } else {
                    window.location.href = '?'; // Redirect back to the same page or any other desired page
                }
              </script>";
    }
}
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/icon.png">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v6.0.0-beta3/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/showServices.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/stock.css">
    <title>Services</title>
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
        <?php include "../includes/serviceMenu.php"; ?>
    </nav>
    <div class="top">
        <div class="selectDiv">
            <select class="form-select" id="searchBy">
                <option value="id_devis">Num Devis</option>
                <option value="full_name_client">Nom client</option>
            </select>
        </div>
        <div class="searchDiv">
            <span class="input-group-text" style="background-color: #7b2cbf;" id="basic-addon1"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i></span>
            <input type="text" class="form-control" id="search" placeholder="recherche ..." aria-label="Username" aria-describedby="basic-addon1">
        </div>
    </div>
    <div class="container mt-5">
        <table class="table">
            <tr id="header">
                <th>N°</th>
                <th>NOM CLIENT</th>
                <th>DATE</th>
                <th>TOTAL HT</th>
                <th>TOTAL TTC</th>
                <th>DEVIS</th>
                <th>SUPPRIMER</th>
                <th>DETAILS</th>
            </tr>

            <?php
            $requete = $db->prepare("SELECT * FROM Devis ORDER BY id_devis ASC");
            $requete->execute();
            $allDevis = $requete->fetchAll(PDO::FETCH_ASSOC);
            foreach ($allDevis as $devis) {
                echo "<tr>";
                echo "<td>" . $devis["id_devis"] . "</td>";
                echo "<td>" . $devis["full_name_client"] . "</td>";
                echo "<td>" . $devis["date_devis"] . "</td>";
                echo "<td>" . $devis["total_devis_ht"] . " DH</td>";
                echo "<td>" . $devis["total_devis_ttc"] . " DH</td>";
                echo '<td><a href="devis.php?id_devis=' . $devis["id_devis"] . '" style="color:#7b2cbf"><i class="fa-solid fa-receipt fa-lg" style="color:#7b2cbf;"></i></a></td>';
                echo '<td><a href="?delete=' . $devis["id_devis"] . '" style="color:#7b2cbf"><i class="fa-solid fa-trash fa-lg" style="color:#7b2cbf;"></i></a></td>';
                echo '<td><a href="devisDetails.php?show=' . $devis["id_devis"] . '" style="color:#7b2cbf"><i class="fa-solid fa-eye fa_lg" style="color:#7b2cbf;"></i></i></a></td>';
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
            var searchBy = $("#searchBy").val();
            var search = document.getElementById("search").value;
            $.ajax({
                type: 'POST',
                url: "devisSearch.php",
                data: {
                    'searchBy': searchBy,
                    'search': search
                },
                success: function(data) {
                    $('.table').html(data);
                },

            });
        });
    </script>
</body>

</html>