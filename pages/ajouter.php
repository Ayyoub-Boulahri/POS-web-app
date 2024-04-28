<!DOCTYPE html>
<html lang="en">

<?php
include "../database.php";
session_start();
$cmp = 0;
if ($_SESSION["log"] == 1 && $_SESSION["type_user"] == "admin") {
    $cmp = 1;
} else {
    echo $cmp;
    header("Location:../index.php");
}


if (!empty($_POST["barcode"]) && !empty($_POST["libelle"]) && !empty($_POST["prix"]) && !empty($_POST["stock"])) {
    $barcode = $_POST["barcode"];
    $libelle = $_POST["libelle"];
    $prix = $_POST["prix"];    
    $prix_achat = $_POST["prix_achat"];
    $stock = $_POST["stock"];
    $requete = $db->prepare("SELECT COUNT(*) FROM produit WHERE barcode = ?");
    $requete->bindValue(1, $barcode);
    $requete->execute();
    $count = $requete->fetchColumn();

    if ($count > 0) {
        $disponible = $_POST["disponible"];
        $requete = $db->prepare("UPDATE produit SET stock = ? + ?, libelle = ?, prix = ? , prix_achat = ? WHERE barcode=?");
        $requete->bindValue(1, $stock);
        $requete->bindValue(2, $disponible);
        $requete->bindValue(3, $libelle);
        $requete->bindValue(4, $prix);
        $requete->bindValue(5, $prix_achat);
        $requete->bindValue(6, $barcode);
        $requete->execute();
    } else {
        $requete = $db->prepare("INSERT INTO produit VALUES (?,?,?,?,?)");
        $requete->bindValue(1, $barcode);
        $requete->bindValue(2, $libelle);
        $requete->bindValue(3, $prix);
        $requete->bindValue(4, $prix_achat);
        $requete->bindValue(5, $stock);
        $requete->execute();
    }
    echo '<script>
            if ( window.history.replaceState ) {
                window.history.replaceState( null, null, window.location.href );
                location.reload();
            }
        </script>';
}



?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/icon.png">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v6.0.0-beta3/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/ajouter.css">
    <title>Ajouter</title>
    <style>
        #ajouter .nav-link {
            color: black;
        }

        #ajouter {
            background-color: white;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <?php include "../includes/adminMenu.php"; ?>
    </nav>
    <div class="container formContainer">
        <form action="" method="post" id="addForm">
            <div class="content">
                <h1>Ajouter un Produit</h1>
                <hr>
                <div class="rowDiv">
                    <label for="barcode">Code Produit</label>
                    <input type="text" autofocus class="form-control" name="barcode" id="barcode">
                </div>
                <div class="infos">
                    <div class="rowDiv">
                        <label for="libelle">Libelle</label>
                        <input type="text" class="form-control" name="libelle" id="libelle">
                    </div>
		    <div class="rowDiv">
                        <label for="prix_achat">Prix d'achat</label>
                        <input type="number"  min=0 class="form-control" step="any" name="prix_achat" id="prix_achat" onchange="changePrixVente()">
                    </div>
                    <div class="rowDiv">
                        <label for="prix">Prix de vente</label>
                        <input type="number"  min=0 class="form-control" step="any" name="prix" id="prix">
                    </div>
                    
                    <div class="rowDiv">
                        <label for="stock">Stock</label>
                        <input type="number"  min=0 class="form-control" name="stock" id="stock">
                    </div>
                </div>
                <hr>
                <div class="buttons">
                    <a href="#" name="add" class="btn btn-lg text-white" style="background-color: #7b2cbf;" id="submitBtn">Valider</a>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <script>
        $('#barcode').keyup(function(e) {
            var barcode = document.getElementById("barcode").value;
            $.ajax({
                type: 'POST',
                url: "ajouterSearch.php",
                data: {
                    'barcode': barcode
                },
                success: function(data) {
                    $('.infos').html(data);
                },

            });
        });

	function changePrixVente(){
		var prix_achat = parseFloat(document.getElementById('prix_achat').value);
		prix_achat += parseFloat(prix_achat * 40) / 100;
		document.getElementById('prix').value = prix_achat;
	}

        document.getElementById('submitBtn').addEventListener('click', function(event) {
            event.preventDefault(); 
            document.getElementById('addForm').submit(); 
        });
    </script>
</body>

</html>