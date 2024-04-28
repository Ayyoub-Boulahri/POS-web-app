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

if (isset($_GET["deleteVnt"]) && isset($_GET["deletePrd"])) {
    if (!empty($_GET["deleteVnt"]) && !empty($_GET["deletePrd"]) && !empty($_GET["qnt"])) {
        $qnt = $_GET["qnt"];
        $id_vnt = $_GET["deleteVnt"];
        $barcode = $_GET["deletePrd"];
        $total = $_GET["total"];
        $requete = $db->prepare("UPDATE produit SET stock = stock + ? WHERE barcode=?");
        $requete->bindValue(1, $qnt);
        $requete->bindValue(2, $barcode);
        $requete->execute();
        $requete = $db->prepare("UPDATE vente SET montant_total = montant_total - ? WHERE id_vnt = ?");
        $requete->bindValue(1, $total);
        $requete->bindValue(2, $id_vnt);
        $requete->execute();
        $requete = $db->prepare("UPDATE vente SET ttc = ttc - (? + ((? * 20) / 100)) WHERE id_vnt = ?");
        $requete->bindValue(1, $total);
        $requete->bindValue(2, $total);
        $requete->bindValue(3, $id_vnt);
        $requete->execute();
        $requete = $db->prepare("DELETE FROM contenir WHERE barcode = ? AND id_vnt = ?");
        $requete->bindValue(1, $barcode);
        $requete->bindValue(2, $id_vnt);
        $requete->execute();
    }
    header("Location:vente_details.php?show=" . $_GET["show"]);
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
    <nav class="navbar navbar-expand-lg ">
    <?php
            if ($_SESSION["type_user"] == "admin")
                include "../includes/adminMenu.php";
            else
                include "../includes/caiseMenu.php";
        ?>
    </nav>

    <div class="top">
        <div class="searchDiv">
            <span class="input-group-text" style="background-color: #7b2cbf;" id="basic-addon1"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i></span>
            <input type="text" class="form-control" id="search" placeholder="recherche par barcode . . ." aria-label="Username" aria-describedby="basic-addon1">
        </div>
    </div>
    <div class="container mt-5">
        <table class="table">
            <tr id="header">
                <th>CODE PRODUIT</th>
                <th>LIBELLE</th>
                <th>QUANTITE</th>
                <th>MONTANT</th>
                <?php
                    if($_SESSION["type_user"] == "admin")
                        echo "<th>MONTANT ACHAT</th>";
                ?>
                <th>SUPPRIMER</th>
            </tr>

            <?php
            $requete = $db->prepare("SELECT c.*, p.libelle FROM contenir c, produit p, vente v WHERE c.barcode = p.barcode AND v.id_vnt = c.id_vnt AND c.id_vnt = ?");
            $requete->bindValue(1, $_GET["show"]);
            $requete->execute();
            $produits = $requete->fetchAll(PDO::FETCH_ASSOC);
            foreach ($produits as $produit) {
                echo "<tr>";
                echo "<td>" . $produit["barcode"] . "</td>";
                echo "<td>" . $produit["libelle"] . "</td>";
                echo "<td>" . $produit["quantite"] . "</td>";
                echo "<td>" . $produit["total_ht"] . " DH</td>";
                if($_SESSION["type_user"] == "admin")
                    echo "<td>" . $produit["total_achat"] . " DH</td>";
                echo '<td><a href="?deleteVnt=' . $produit["id_vnt"] . '&deletePrd=' . $produit["barcode"] . '&total=' . $produit["total_ht"] . '&show=' . $_GET["show"] . '&qnt=' . $produit["quantite"] . '" style="color:#7b2cbf"><i class="fa-solid fa-trash fa-lg" style="color:#7b2cbf;"></i></a></td>';
                echo "</tr>";
            }
            ?>

        </table>
        <div class="title">
            <div id="label">Total HT : </div>
            <div id="nbr">
                <?php
                $requete = $db->prepare("SELECT montant_total FROM vente WHERE id_vnt = ?");
                $requete->bindValue(1, $_GET["show"]);
                $requete->execute();
                $montant = $requete->fetch();
                echo $montant["montant_total"] . ' DH';
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <script>
        $('#search').keyup(function(e) {
            var search = document.getElementById("search").value;
            $.ajax({
                type: 'POST',
                url: "vente_details_search.php",
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