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

if (isset($_POST["modifier"])) {
    if (!empty($_POST["libelle"]) && !empty($_POST["prix"])) {
        $barcode = $_POST["barcode"];
        $libelle = $_POST["libelle"];
        $id = $_GET["edit"];
        $prix = $_POST["prix"];
        $prix_achat = $_POST["prix_achat"];
        $stock = $_POST["stock"];

        $is_valid = true;
        if ($id != $barcode) {
            $requete = $db->prepare("SELECT COUNT(*) as count FROM produit WHERE barcode = ?");
            $requete->bindValue(1, $barcode);
            $requete->execute();
            $count = $requete->fetchColumn();
            $is_valid = $count == 0 ? true : false;
        }

        if ($is_valid) {
            $requete = $db->prepare("UPDATE produit set barcode=?, libelle=?, prix=?, stock=?, prix_achat=? WHERE barcode=?");
            $requete->bindValue(1, $barcode);
            $requete->bindValue(2, $libelle);
            $requete->bindValue(3, $prix);
            $requete->bindValue(4, $stock);
            $requete->bindValue(5, $prix_achat);
            $requete->bindValue(6, $id);
            $requete->execute();
        }
    }
    header("Location:stock.php");
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
    <link rel="stylesheet" href="../css/stock.css">
    <title>Stock</title>
    <style>
        #stock .nav-link {
            color: black;
        }

        #stock {
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

    <?php
    if (isset($_GET["edit"])) {
        $idE = $_GET["edit"];
        $requete = $db->prepare("SELECT * FROM produit WHERE barcode=?");
        $requete->bindValue(1, $idE);
        $requete->execute();
        $produit = $requete->fetch();
    }
    ?>

    <div class="top">
        <div class="selectDiv">
            <select class="form-select" id="searchByDispo">
                <option value="2">tous</option>
                <option value="1">Disponible</option>
                <option value="0">non Disponible</option>
            </select>
        </div>
        <div class="selectDiv">
            <select class="form-select" id="searchBy">
                <option value="barcode">Code Produit</option>
                <option value="libelle">Libelle</option>
            </select>
        </div>
        <div class="searchDiv">
            <span class="input-group-text" style="background-color: #7b2cbf;" id="basic-addon1"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i></span>
            <input type="text" class="form-control" id="search" placeholder="recherche ..." aria-label="Username" aria-describedby="basic-addon1">
        </div>
    </div>

    <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form action="" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-centre" id="exampleModalLongTitle">MODIFICATION</h5>
                    </div>
                    <div class="modal-body">
                        <div class="cotainer">
                            <div class="row">
                                <div class="col-25 text-left">BARCODE</div>
                                <div class="col-75">
                                    <input type="text" class="form-control" name="barcode" id="" placeholder="BARCODE" value="<?php echo $produit["barcode"] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-25 text-left">LIBELLE PRODUIT</div>
                                <div class="col-75">
                                    <input type="text" class="form-control" name="libelle" id="" placeholder="LIBELLE" value="<?php echo $produit["libelle"] ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-25 text-left" id="multiple">PRIX ACHAT</div>
                                <div class="col-75">
                                    <input type="number" min=0 step="any" class="form-control" name="prix_achat" id="" placeholder="Prix Achat" value="<?php echo $produit["prix_achat"] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-25 text-left">PRIX VENTE</div>
                                <div class="col-75">
                                    <input type="number" min=0 step="any" class="form-control" name="prix" id="" placeholder="PRIX" value="<?php echo $produit["prix"] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-25 text-left" id="multiple">STOCK</div>
                                <div class="col-75">
                                    <input type="number" min=0 step="any" class="form-control" name="stock" id="" placeholder="STOCK" value="<?php echo $produit["stock"] ?>">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn text-white" value="Modifier" role="button" name="modifier" style="    background-color:#7b2cbf">
                        <input type="button" class="btn btn-secondary" value="Annuler" id="stop" role="button" name="annuler" onclick=window.location.href='stock.php'>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="container mt-5">
        <table class="table">
            <tr id="header">
                <th>CODE PRODUIT</th>
                <th>LIBELLE</th>
		<?php 
                    if($_SESSION["type_user"] == "admin"){
                        echo "<th>PRIX ACHAT</th>";
                    }
                ?>
                <th>PRIX VENTE</th>
                <th>STOCK</th>
                <?php 
                    if($_SESSION["type_user"] == "admin"){
                        echo "<th>MODIFIER</th>";
                    }
                ?>
            </tr>

            <?php
            $requete = $db->prepare("SELECT * FROM produit");
            $requete->execute();
            $produits = $requete->fetchAll(PDO::FETCH_ASSOC);
            foreach ($produits as $produit) {
                echo "<tr>";
                echo "<td>" . $produit["barcode"] . "</td>";
                echo "<td>" . $produit["libelle"] . "</td>";
		if($_SESSION["type_user"] == "admin"){
                    echo "<td>" . $produit["prix_achat"] . " DH</td>";
                }
                echo "<td>" . $produit["prix"] . " DH</td>";
                echo "<td>" . $produit["stock"] . "</td>";
                if($_SESSION["type_user"] == "admin"){
                    echo '<td><a href="?edit=' . $produit["barcode"] . '" style="color:#7b2cbf"><i class="fa-solid fa-pen-to-square fa-lg "></i></a></td>';
                }
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
            var searchByDispo = $("#searchByDispo").val();
            var search = document.getElementById("search").value;
            $.ajax({
                type: 'POST',
                url: "stockSearch.php",
                data: {
                    'searchBy': searchBy,
                    'search': search,
                    'searchByDispo': searchByDispo
                },
                success: function(data) {
                    $('.table').html(data);
                },

            });
        });

        $('#searchByDispo').change(function(e) {
            var searchBy = $("#searchBy").val();
            var searchByDispo = $("#searchByDispo").val();
            var search = document.getElementById("search").value;
            $.ajax({
                type: 'POST',
                url: "stockSearch.php",
                data: {
                    'searchBy': searchBy,
                    'search': search,
                    'searchByDispo': searchByDispo
                },
                success: function(data) {
                    $('.table').html(data);
                },

            });
        });

        var url = window.location.href;
        var str = url.substring(url.indexOf("?") + 1);
        if (str.includes("edit")) {
            $("#exampleModalCenter2").modal()
        }
    </script>
</body>

</html>