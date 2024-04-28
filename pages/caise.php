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

if (!empty($_POST["barcode"]) && $_POST["quantite"] != 0) {
    $barcode = $_POST["barcode"];
    $qnt = $_POST["quantite"];
    $prix = $_POST["prix_total"];
    $prix_unit = $_POST["prix_unit"];
    $id_vnt = $_GET["id_vnt"];
    $prix_total_achat = $_POST["prix_achat"] * $qnt;
    $requete = $db->prepare("SELECT COUNT(*) FROM produit WHERE barcode = ?");
    $requete->bindValue(1, $barcode);
    $requete->execute();
    $count = $requete->fetchColumn();

    if ($count > 0) {
        $requete = $db->prepare("SELECT COUNT(*) FROM contenir WHERE barcode = ? AND id_vnt = ?");
        $requete->bindValue(1, $barcode);
        $requete->bindValue(2, $id_vnt);
        $requete->execute();
        $count = $requete->fetchColumn();

        if ($count > 0) {
            $requete = $db->prepare("UPDATE contenir SET quantite = quantite + ?  , total_ht = total_ht + ?, total_achat = total_achat + ? WHERE barcode = ? AND id_vnt = ?");
            $requete->bindValue(1, $qnt);
            $requete->bindValue(2, $prix);
            $requete->bindValue(3, $prix_total_achat);
            $requete->bindValue(4, $barcode);
            $requete->bindValue(5, $id_vnt);
            $requete->execute();
        } else {
            $requete = $db->prepare("INSERT INTO contenir VALUES (?,?,?,?,?,?)");
            $requete->bindValue(1, $id_vnt);
            $requete->bindValue(2, $barcode);
            $requete->bindValue(3, $qnt);
            $requete->bindValue(4, $prix_unit);
            $requete->bindValue(5, $prix);
            $requete->bindValue(6, $prix_total_achat);
            $requete->execute();
        }

        $requete = $db->prepare("UPDATE vente SET montant_total = montant_total + ? WHERE id_vnt = ?");
        $requete->bindValue(1, $prix);
        $requete->bindValue(2, $id_vnt);
        $requete->execute();

        $requete = $db->prepare("UPDATE produit SET stock = stock - ? WHERE barcode = ?");
        $requete->bindValue(1, $qnt);
        $requete->bindValue(2, $barcode);
        $requete->execute();
    }
    echo '<script>
            if ( window.history.replaceState ) {
                window.history.replaceState( null, null, window.location.href );
                location.reload();
            }
        </script>';
}

if (isset($_GET["prdD"])) {
    $id_vnt = $_GET["id_vnt"];
    $barcode = $_GET["prdD"];
    $qnt = $_GET["qnt"];
    $montant = $_GET["montant"];
    $requete = $db->prepare("DELETE FROM contenir WHERE barcode = ? AND id_vnt = ?");
    $requete->bindValue(1, $barcode);
    $requete->bindValue(2, $id_vnt);
    $requete->execute();
    $requete = $db->prepare("UPDATE vente SET montant_total = montant_total - ? WHERE id_vnt = ?");
    $requete->bindValue(1, $montant);
    $requete->bindValue(2, $id_vnt);
    $requete->execute();
    $requete = $db->prepare("UPDATE produit SET stock = stock + ? WHERE barcode = ?");
    $requete->bindValue(1, $qnt);
    $requete->bindValue(2, $barcode);
    $requete->execute();
    header("Location:caise.php?id_vnt=" . $id_vnt);
}

if (!empty($_GET["annuler"])) {
    $id_vnt = $_GET["annuler"];

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
    header("Location:stock.php");
}

if(isset($_POST["reduction"])) {
    $pcent = $_POST["pcent"];
    $id_vnt = $_GET["id_vnt"];
    $requete = $db->prepare("UPDATE vente SET reduction = ? WHERE id_vnt = ?");
    $requete->bindValue(1, $pcent);
    $requete->bindValue(2, $id_vnt);
    $requete->execute();
    header("Location:caise.php?id_vnt=" . $id_vnt);
}

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/icon.png">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v6.0.0-beta3/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/caise.css">
    <link rel="stylesheet" href="../css/menu.css">
    <title>Caise</title>
    <style>
        /* Custom styles */
        #form-container,
        #table-container {
            padding: 20px;
            padding-left: 30px;
            padding-right: 30px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: white;
        }

        .form-row:not(:last-of-type) {
            margin-bottom: 15px !important;
        }

        #table-container {
            overflow-y: auto;
            max-height: 420px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg ">
        <?php
        if ($_SESSION["type_user"] == "caise")
            include "../includes/caiseMenu.php";
        else if ($_SESSION["type_user"] == "admin")
            include "../includes/adminMenu.php";
        ?>
    </nav>

    <div class="container pageContainer">
        <div class="row">
            <!-- form -->
            <div class="col-md-5">
                <div id="form-container">
                    <div class="panel-heading">
                        <h3 class="panel-title">Add Product</h3>
                    </div>
                    <hr>
                    <form method="post" id="addForm">
                        <div class="form-row form-group row">
                            <label for="barcode" class="col-sm-4 col-form-label card-title text-muted mb-0">Barcode</label>
                            <div class="col-sm-8">
                                <input type="text" autofocus required class="form-control" id="barcode" name="barcode">
                            </div>
                        </div>
                        <div class="form-row form-group row">
                            <label for="libelle" class="col-sm-4 col-form-label card-title text-muted mb-0">Libelle</label>
                            <div class="col-sm-8">
                                <input type="text" required readonly class="form-control" name="libelle" id="libelle">
                            </div>
                        </div>

                        <input type="hidden" name="prix_achat" id="prix_achat">

                        <div class="form-row form-group row">
                            <label for="quantite" class="col-sm-4 col-form-label card-title text-muted mb-0">Quantite</label>
                            <div class="col-sm-8">
                                <input type="number" min=0 required class="form-control" name="quantite" id="quantite" onchange="changePrixTotal()">
                            </div>
                        </div>
                        <div class="form-row form-group row">
                            <label for="disponible" class="col-sm-4 col-form-label card-title text-muted mb-0"> Qnt disponible</label>
                            <div class="col-sm-8">
                                <input type="number" readonly min=0 required class="form-control" name="disponible" id="disponible">
                            </div>
                        </div>
                        <div class="form-row form-group row">
                            <label for="prix_unit" class="col-sm-4 col-form-label card-title text-muted mb-0">Prix Unitaire</label>
                            <div class="col-sm-8">
                                <input type="number" min=0 class="form-control" name="prix_unit" id="prix_unit" onchange="changePrixTotal()">
                            </div>
                        </div>
                        <div class="form-row form-group row">
                            <label for="prix_total" class="col-sm-4 col-form-label card-title text-muted mb-0">Prix Total</label>
                            <div class="col-sm-8">
                                <input type="number" min=0 class="form-control" name="prix_total" id="prix_total">
                            </div>
                        </div>
                        <div class="btnDiv"> <!-- Add text-right class here -->
                            <a href="#" role="button" id="submitBtn" class="btn btn-primary">Valider</a>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Second div with table -->
            <div class="col-md-7">
                <div id="table-container">
                    <a href="facture.php?id_vnt=<?php echo $_GET["id_vnt"] . '&facture=' . $_GET["id_vnt"]; ?>" role="button" class="btn btn-primary mb-4">Facture</a>
                    <a href="boune.php?id_vnt=<?php echo $_GET["id_vnt"] . '&boune=' . $_GET["id_vnt"]; ?>" role="button" class="btn btn-success mb-4">Bon</a>
                    <a href="?annuler=<?php echo $_GET["id_vnt"] . '&id_vnt=' . $_GET["id_vnt"]; ?>" role="button" class="btn btn-danger mb-4">Annler</a>
                    <div class="col-sm-8">
                        <form method="post">
                            <input type="number" min=0 value="0" class="form-control" name="pcent" id="pcent">
                            <br>
                            <button role="button" class="btn btn-primary mb-4" name="reduction">Reduction</a>
                            <br>
                        </form>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="text-align:center">supp</th>
                                <th>Produit</th>
                                <th>Quantite</th>
                                <th>Prix unitaire</th>
                                <th>Total HT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Add your table data here -->
                            <?php
                            $requete = $db->prepare("SELECT c.*, p.libelle FROM contenir c, produit p WHERE p.barcode = c.barcode AND c.id_vnt = ?");
                            $requete->bindValue(1, $_GET["id_vnt"]);
                            $requete->execute();
                            $produits = $requete->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($produits as $produit) {
                                echo '<tr>';
                                echo '<td style="text-align:center"><a href=?id_vnt=' . $produit["id_vnt"] . '&prdD=' . $produit["barcode"] . '&qnt=' . $produit["quantite"] . '&montant=' . $produit["total_ht"] . '><i class="fa-solid fa-trash" style="color:#8d99ae;"></i></a>';
                                echo '<td>' . $produit["libelle"] . '</td>';
                                echo '<td>' . $produit["quantite"] . '</td>';
                                echo '<td>' . $produit["prix_unit"] . '</td>';
                                echo '<td>' . $produit["total_ht"] . '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <th>Total HT</th>
                                <th>
                                    <?php
                                    $requete = $db->prepare("SELECT montant_total FROM vente WHERE id_vnt = ?");
                                    $requete->bindValue(1, $_GET["id_vnt"]);
                                    $requete->execute();
                                    $result = $requete->fetch();
                                    echo $result["montant_total"];
                                    ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Attach event handler to the barcode input
            $("#barcode").on("change", function() {
                // Get the barcode value
                var barcodeValue = $(this).val();

                // Make an AJAX request to fetch product information based on the barcode
                $.ajax({
                    type: "POST",
                    url: "getInfos.php", // Replace with the actual URL of your PHP script
                    data: {
                        barcode: barcodeValue
                    },
                    dataType: "json",
                    success: function(response) {
                        // Update the other input fields with the retrieved product information
                        if (response) {
                            $("#libelle").val(response.libelle);
                            $("#prix_unit").val(response.prix);
                            $("#quantite").val((response.stock == 0 ? 0 : 1));
                            $("#quantite").attr("max", response.stock);
                            $("#disponible").val(response.stock);
                            $("#prix_total").val(response.prix);
                            $("#prix_achat").val(response.prix_achat);
                        } else {
                            // Barcode not found, clear the other input fields
                            $("#libelle").val("");
                            $("#quantite").val("");
                            $("#prix_unit").val("");
                            $("#prix_total").val("");
                            $("#disponible").val("");
                            $("#prix_achat").val("");
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle any error that may occur during the AJAX request
                        console.error(error);
                    }
                });
            });
        });



        function changePrixTotal() {
            var qnt = parseInt(document.getElementById("quantite").value);
            var prix_unit = parseFloat(document.getElementById("prix_unit").value);
            var dispo = parseInt(document.getElementById("disponible").value);
            var q = document.getElementById("quantite");
            if (dispo < qnt) {
                q.value = dispo;
            }
            qnt = parseInt(q.value);
            document.getElementById("prix_total").value = prix_unit * qnt;
        }

        document.getElementById('submitBtn').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default anchor behavior (following the link)
            document.getElementById('addForm').submit(); // Submit the form programmatically
        });
    </script>

</body>

</html>