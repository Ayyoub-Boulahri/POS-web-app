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

$requete = $db->prepare("DELETE FROM vente WHERE montant_total = 0");
$requete->execute();

$requete = $db->prepare("SELECT id_vnt FROM vente WHERE ttc = 0");
$requete->execute();
$count = $requete->rowCount();
if($count > 0) {
    $ventes = $requete->fetchAll(PDO::FETCH_ASSOC);
    foreach($ventes as $vente) {
        $requete = $db->prepare("SELECT * FROM contenir WHERE id_vnt = ?");
        $requete->bindValue(1, $vente["id_vnt"]);
        $requete->execute();
        $resultats = $requete->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultats as $resultat) {
            $requete = $db->prepare("UPDATE produit SET stock = stock + ? WHERE barcode = ?");
            $requete->bindValue(1, $resultat["quantite"]);
            $requete->bindValue(2, $resultat["barcode"]);
            $requete->execute();
        }

        $requete = $db->prepare("DELETE FROM vente WHERE id_vnt = ?");
        $requete->bindValue(1, $vente["id_vnt"]);
        $requete->execute();
    }
}


if (isset($_GET["delete"])) {
    if (isset($_GET["confirm"]) && $_GET["confirm"] === "true") {
        $id_vnt = $_GET["delete"];
        $requete = $db->prepare("SELECT * FROM contenir WHERE id_vnt = ?");
        $requete->bindValue(1, $id_vnt);
        $requete->execute();
        $resultats = $requete->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultats as $resultat) {
            $requete = $db->prepare("UPDATE produit SET stock = stock + ? WHERE barcode = ?");
            $requete->bindValue(1, $resultat["quantite"]);
            $requete->bindValue(2, $resultat["barcode"]);
            $requete->execute();
        }

        $requete = $db->prepare("DELETE FROM vente WHERE id_vnt = ?");
        $requete->bindValue(1, $id_vnt);
        $requete->execute();
        header("Location:ventes.php");
    } else {
        echo "<script>
                var confirmDelete = confirm('Voulez vous vraiment supprimer cette vente?');
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
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/ventes.css">
    <title>Ventes</title>
    <style>
        #ventes .nav-link {
            color: black;
        }

        #ventes {
            background-color: white;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <?php
        if ($_SESSION["type_user"] == "admin")
            include "../includes/adminMenu.php";
        else
            include "../includes/caiseMenu.php";
        ?>
    </nav>
    <div class="top">
        <div class="selectDiv">
            <select class="form-select" id="searchBy">
                <option value="id_vnt">Num Vente</option>
                <option value="serveur">Serveur</option>
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
                <th>SERVEUR</th>
                <th>DATE</th>
                <th>TOTAL HT</th>
                <th>TOTAL TTC</th>
                <th>REDUCTION</th>
                <?php
                if($_SESSION["type_user"] == "admin")
                        echo "<th>MONTANT ACHAT</th>";
                ?>
                <th>Facture</th>
                <th>BON</th>
                <th>SUPPRIMER</th>
                <th>DETAILS</th>
            </tr>

            <?php
            $requete = $db->prepare("SELECT v.*, u.username, SUM(c.total_achat) AS total_achat FROM vente v, contenir c, user u WHERE v.id_vnt = c.id_vnt AND u.id_user = v.serveur GROUP BY id_vnt ORDER BY id_vnt ASC");
            $requete->execute();
            $ventes = $requete->fetchAll(PDO::FETCH_ASSOC);
            foreach ($ventes as $vente) {
                echo "<tr>";
                echo "<td>" . $vente["id_vnt"] . "</td>";
                echo "<td>" . $vente["username"] . "</td>";
                echo "<td>" . $vente["date_vnt"] . "</td>";
                echo "<td>" . $vente["montant_total"] . " DH</td>";
                echo "<td>" . $vente["ttc"] . " DH</td>";
                echo "<td>" . ($vente["montant_total"] - ($vente["montant_total"] * $vente["reduction"]) / 100) . " DH</td>";
                if($_SESSION["type_user"] == "admin")
                    echo "<td>" . $vente["total_achat"] . " DH</td>";
                echo '<td><a href="facture.php?id_vnt=' . $vente["id_vnt"] . '" style="color:#7b2cbf"><i class="fa-solid fa-receipt fa-lg" style="color:#7b2cbf;"></i></a></td>';
                echo '<td><a href="boune.php?id_vnt=' . $vente["id_vnt"] . '" style="color:#7b2cbf"><i class="fa-solid fa-receipt fa-lg" style="color:#7b2cbf;"></i></a></td>';
                echo '<td><a href="?delete=' . $vente["id_vnt"] . '" style="color:#7b2cbf"><i class="fa-solid fa-trash fa-lg" style="color:#7b2cbf;"></i></a></td>';
                echo '<td><a href="vente_details.php?show=' . $vente["id_vnt"] . '" style="color:#7b2cbf"><i class="fa-solid fa-eye fa_lg" style="color:#7b2cbf;"></i></i></a></td>';
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
                url: "ventesSearch.php",
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