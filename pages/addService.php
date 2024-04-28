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

if (isset($_POST["description"])) {
    $description = $_POST["description"];
    $quantite = $_POST["quantite"];
    $prix_unit = $_POST["prix_unit"];
    $total_ht = $_POST["total_ht"];
    $requete = $db->prepare("INSERT INTO service VALUES (null,?,?,?,?,?)");
    $requete->bindValue(1, $_GET["id_devis"]);
    $requete->bindValue(2, $description);
    $requete->bindValue(3, $quantite);
    $requete->bindValue(4, $prix_unit);
    $requete->bindValue(5, $total_ht);
    $requete->execute();

    $requete = $db->prepare("UPDATE devis SET total_devis_ht = total_devis_ht + ?");
    $requete->bindValue(1, $total_ht);
    $requete->execute();
    echo '<script>
            if ( window.history.replaceState ) {
                window.history.replaceState( null, null, window.location.href );
                location.reload();
            }
        </script>';
}

if(!empty($_GET["id_serviceD"])) {
    $id_service = $_GET["id_serviceD"];
    $id_devis = $_GET["id_devis"];
    $total_ht = $_GET["total_ht"];
    $requete = $db->prepare("DELETE FROM service WHERE id_service = ? AND id_devis = ?");
    $requete->bindValue(1, $id_service);
    $requete->bindValue(2, $id_devis);
    $requete->execute();

    $requete = $db->prepare("UPDATE devis SET total_devis_ht = total_devis_ht - ? WHERE id_devis = ?");
    $requete->bindValue(1, $total_ht);
    $requete->bindValue(2, $id_devis);
    $requete->execute();
    header("Location:addService.php?id_devis=" . $id_devis);
}

if (!empty($_GET["annuler"])) {
    $id_devis = $_GET["annuler"];
    $requete = $db->prepare("DELETE FROM devis WHERE id_devis = ?");
    $requete->bindValue(1, $id_devis);
    $requete->execute();
    header("Location:showServices.php");
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
    <link rel="stylesheet" href="../css/caise.css">
    <title>Nouveau Service</title>
    <style>
        #newService .nav-link {
            color: black;
        }

        #newService {
            background-color: white;
            border-radius: 4px;
        }

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
    <nav class="navbar navbar-expand-lg">
        <?php include "../includes/serviceMenu.php"; ?>
    </nav>

    <div class="container pageContainer">
        <div class="row">
            <!-- form -->
            <div class="col-md-5">
                <div id="form-container">
                    <div class="panel-heading">
                        <h3 class="panel-title">Add Service</h3>
                    </div>
                    <hr>
                    <form method="post" id="addForm">
                        <div class="form-row form-group row">
                            <label for="description" class="col-sm-4 col-form-label card-title text-muted mb-0">Desctiption</label>
                            <div class="col-sm-8">
                                <textarea type="text" autofocus required class="form-control" id="description" name="description"></textarea>
                            </div>
                        </div>
                        <div class="form-row form-group row">
                            <label for="quantite" class="col-sm-4 col-form-label card-title text-muted mb-0">Quantite</label>
                            <div class="col-sm-8">
                                <input type="number" min=1 required class="form-control" name="quantite" id="quantite" onchange="changePrixTotal()">
                            </div>
                        </div>
                        <div class="form-row form-group row">
                            <label for="prix_unit" class="col-sm-4 col-form-label card-title text-muted mb-0">Prix Unitaire</label>
                            <div class="col-sm-8">
                                <input type="number" min=1 required class="form-control" name="prix_unit" id="prix_unit" onchange="changePrixTotal()">
                            </div>
                        </div>
                        <div class="form-row form-group row">
                            <label for="total_ht" class="col-sm-4 col-form-label card-title text-muted mb-0">Prix Total</label>
                            <div class="col-sm-8">
                                <input type="number" min=1 class="form-control" name="total_ht" id="total_ht">
                            </div>
                        </div>
                        <div class="btnDiv"> <!-- Add text-right class here -->
                            <a href="#" role="button" id="submitBtn" class="btn btn-primary">Valider</a>
                        </div>
                    </form>
                </div>
            </div>
            <!-- table -->
            <div class="col-md-7">
                <div id="table-container">
                    <a href="devis.php?devis&id_devis=<?php echo $_GET["id_devis"] ?>" role="button" class="btn btn-success mb-4">Devis</a>
                    <a href="facture_service.php?devis&id_devis=<?php echo $_GET["id_devis"] ?>" role="button" class="btn btn-primary mb-4">Facture</a>
                    <a href="?annuler=<?php echo $_GET["id_devis"] . '&id_devis=' . $_GET["id_devis"]; ?>" role="button" class="btn btn-danger mb-4">Annler</a>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="text-align:center">supp</th>
                                <th>Description</th>
                                <th>Quantite</th>
                                <th>Prix unitaire</th>
                                <th>Total HT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $requete = $db->prepare("SELECT * FROM service WHERE id_devis = ?");
                            $requete->bindValue(1, $_GET["id_devis"]);
                            $requete->execute();
                            $services = $requete->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($services as $service) {
                                echo '<tr>';
                                echo '<td style="text-align:center"><a href=?id_devis=' . $_GET["id_devis"] . '&id_serviceD=' . $service["id_service"] . '&total_ht=' . $service["total_ht_service"] . '><i class="fa-solid fa-trash" style="color:#8d99ae;"></i></a>';
                                echo '<td>' . nl2br($service["description"]). '</td>';
                                echo '<td>' . $service["quantite"] . '</td>';
                                echo '<td>' . $service["prix_unit_service"] . '</td>';
                                echo '<td>' . $service["total_ht_service"] . '</td>';
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
                                    $requete = $db->prepare("SELECT total_devis_ht FROM devis WHERE id_devis = ?");
                                    $requete->bindValue(1, $_GET["id_devis"]);
                                    $requete->execute();
                                    $result = $requete->fetch();
                                    echo $result["total_devis_ht"];
                                    ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function changePrixTotal() {
            var qnt = parseInt(document.getElementById("quantite").value);
            var prix_unit = parseFloat(document.getElementById("prix_unit").value);
            document.getElementById("total_ht").value = prix_unit * qnt;
        }

        document.getElementById('submitBtn').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default anchor behavior (following the link)
            document.getElementById('addForm').submit(); // Submit the form programmatically
        });
    </script>
</body>

</html>