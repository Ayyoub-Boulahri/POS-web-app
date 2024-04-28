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
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/icon.png">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v6.0.0-beta3/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/stat.css">
    <title>Statistiques</title>
    <style>
        #stat .nav-link {
            color: black;
        }

        #stat {
            background-color: white;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg ">
        <?php include "../includes/adminMenu.php"; ?>
    </nav>
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-3 mt-3 text-black">STATISTIQUES</h2>
            <div class="header-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">TOTAL TTC</h5>
                                        <span class="h2 font-weight-bold mb-0">
                                            <?php
                                            $requete = $db->prepare("SELECT coalesce(SUM(ttc),0) AS sum FROM vente WHERE DAY(date_vnt) = DAY(CURDATE())");
                                            $requete->execute();
                                            $result = $requete->fetch();
                                            echo $result["sum"] . ' DH';
                                            ?>
                                        </span>
                                        <h5 class="card-title text-uppercase text-muted mb-0">VENTES</h5>
                                        <span class="h2 font-weight-bold mb-0">
                                            <?php
                                            $requete = $db->prepare("SELECT coalesce(count(*),0) AS sum FROM vente WHERE DAY(date_vnt) = DAY(CURDATE())");
                                            $requete->execute();
                                            $result = $requete->fetch();
                                            echo $result["sum"];
                                            ?>
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                            <i class="fas fa-money-bill-1 fa-xs"></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 mb-0 text-muted text-sm">
                                    <span class="text-success mr-2"><i class="fa fa-arrow-up"></i></span>
                                    <span class="text-nowrap">Statistiques de cette jour ( <?php echo date('D') ?>)</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">TOTAL TTC</h5>
                                        <span class="h2 font-weight-bold mb-0">
                                            <?php
                                            $requete = $db->prepare("SELECT coalesce(SUM(ttc),0) AS sum FROM vente WHERE MONTH(date_vnt) = MONTH(CURDATE())");
                                            $requete->execute();
                                            $result = $requete->fetch();
                                            echo $result["sum"] . ' DH';
                                            ?>
                                        </span>
                                        <h5 class="card-title text-uppercase text-muted mb-0">VENTES</h5>
                                        <span class="h2 font-weight-bold mb-0">
                                            <?php
                                            $requete = $db->prepare("SELECT coalesce(count(*),0) AS sum FROM vente WHERE MONTH(date_vnt) = MONTH(CURDATE())");
                                            $requete->execute();
                                            $result = $requete->fetch();
                                            echo $result["sum"];
                                            ?>
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                            <i class="fas fa-chart-pie fa-xs"></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 mb-0 text-muted text-sm">
                                    <span class="text-success mr-2"><i class="fa fa-arrow-up"></i></span>
                                    <span class="text-nowrap">Statistiques de cette mois ( <?php echo date('M') ?>)</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">TOTAL TTC</h5>
                                        <span class="h2 font-weight-bold mb-0">
                                            <?php
                                            $requete = $db->prepare("SELECT coalesce(SUM(ttc),0) AS sum FROM vente WHERE YEAR(date_vnt) = YEAR(CURDATE())");
                                            $requete->execute();
                                            $result = $requete->fetch();
                                            echo $result["sum"] . ' DH';
                                            ?>
                                        </span>
                                        <h5 class="card-title text-uppercase text-muted mb-0">VENTES</h5>
                                        <span class="h2 font-weight-bold mb-0">
                                            <?php
                                            $requete = $db->prepare("SELECT coalesce(count(*),0) AS sum FROM vente WHERE YEAR(date_vnt) = YEAR(CURDATE())");
                                            $requete->execute();
                                            $result = $requete->fetch();
                                            echo $result["sum"];
                                            ?>
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
                                            <i class="fas fa-chart-bar fa-xs"></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 mb-0 text-muted text-sm">
                                    <span class="text-success mr-2"><i class="fa fa-arrow-up"></i></span>
                                    <span class="text-nowrap">Statistiques de cette annee ( <?php echo date('Y') ?>)</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="" method="post">
            <div class="top">
                <div class="stat_date">
                    <input type="text" class="form-control" name="date_debut" min="2021-01-01" id="date_debut" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Premier Date">
                </div>
                <div class="stat_date">
                    <input type="text" class="form-control" name="date_fin" min="year" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Deuxieme Date">
                </div>
                <div class="stat_date">
                    <button type="submit" class="btn text-white" style="background-color: #7b2cbf;" name="valider">Valider</button>
                </div>
            </div>
        </form>


        <?php
        if (!empty($_POST["date_debut"]) && !empty($_POST["date_fin"])) {
            $date_debut = $_POST["date_debut"];
            $date_fin = $_POST["date_fin"];

            $requete = $db->prepare("SELECT SUM(ttc) AS ttc FROM vente WHERE date_vnt BETWEEN ? AND ?");
            $requete->bindValue(1, $date_debut);
            $requete->bindValue(2, $date_fin);
            $requete->execute();
            $ttc = $requete->fetch();

            $requete = $db->prepare("SELECT SUM(montant_total) AS ht FROM vente WHERE date_vnt BETWEEN ? AND ?");
            $requete->bindValue(1, $date_debut);
            $requete->bindValue(2, $date_fin);
            $requete->execute();
            $ht = $requete->fetch();

            $requete = $db->prepare("SELECT COUNT(*) AS ventes FROM vente WHERE date_vnt BETWEEN ? AND ?");
            $requete->bindValue(1, $date_debut);
            $requete->bindValue(2, $date_fin);
            $requete->execute();
            $ventes = $requete->fetch();

            $requete = $db->prepare("SELECT SUM(quantite) AS qnt FROM contenir c, vente v WHERE v.id_vnt = c.id_vnt AND date_vnt BETWEEN ? AND ?");
            $requete->bindValue(1, $date_debut);
            $requete->bindValue(2, $date_fin);
            $requete->execute();
            $qnt = $requete->fetch();

            $requete = $db->prepare("SELECT SUM(c.total_achat) AS t_achat FROM contenir c, vente v WHERE v.id_vnt = c.id_vnt AND date_vnt BETWEEN ? AND ?");
            $requete->bindValue(1, $date_debut);
            $requete->bindValue(2, $date_fin);
            $requete->execute();
            $t_achat = $requete->fetch();

            $count = $ventes["ventes"];
            if ($count == 0) {
                echo '<div class="title" id="title">
                            <p id="title">il n \'y a pas de vente</p>
                          </div>';
            } else {
        ?>
                <div class="title" id="title">
                    <p id="title" style="color:white;background-color:#7b2cbf">les vente entre &nbsp;&nbsp;&nbsp; <?php echo $date_debut . '&nbsp;&nbsp;et&nbsp;&nbsp;' . $date_fin ?></p>
                </div>

                <div class="container mt-5">
                    <table class="table" style="margin-right: 20%; margin-left:20%; width:60%">
                        <tr id="header">
                            <th>Nombre Ventes</th>
                            <th>Articles Vendu</th>
                            <th>Total HT</th>
                            <th>Total TTC</th>
                            <th>Total Achat</th>
                        </tr>

                        <tr>
                            <?php
                            echo '<td>' . $ventes["ventes"] . '</td>';
                            echo '<td>' . $qnt["qnt"] . '</td>';
                            echo '<td>' . $ht["ht"] . ' DH</td>';
                            echo '<td>' . $ttc["ttc"] . ' DH</td>';
                            echo '<td>' . $t_achat["t_achat"] . ' DH</td>';
                            ?>
                        </tr>

                    </table>
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
                            if ($_SESSION["type_user"] == "admin")
                                echo "<th>MONTANT ACHAT</th>";
                            ?>
                            <th>DETAILS</th>
                        </tr>

                        <?php
                        $requete = $db->prepare("SELECT v.*, username, SUM(c.total_achat) AS total_achat FROM vente v, contenir c, user u WHERE v.id_vnt = c.id_vnt AND v.serveur = u.id_user AND date_vnt BETWEEN ? AND ? GROUP BY id_vnt ORDER BY id_vnt ASC");
                        $requete->bindValue(1, $date_debut);
                        $requete->bindValue(2, $date_fin);
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
                            if ($_SESSION["type_user"] == "admin")
                                echo "<td>" . $vente["total_achat"] . " DH</td>";
                            echo '<td><a href="vente_details.php?show=' . $vente["id_vnt"] . '" style="color:#7b2cbf"><i class="fa-solid fa-eye fa_lg" style="color:#7b2cbf;"></i></i></a></td>';
                            echo "</tr>";
                        }

                        ?>

                    </table>
                </div>

        <?php
            }
        } else if (isset($_POST["valider"])) {
            echo '<div class="title" id="title">
                            <p id="title" style="color:white;background-color:red">Saisissez d\'abord les dates</p>
                        </div>';
        }
        ?>


    </div>

    <script>
        var date_debut = document.getElementById("date_debut");
        var date_fin = document.getElementsByName("date_fin")[0];
        date_debut.max = new Date().toISOString().substring(0, 10);

        date_debut.addEventListener("change", function() {
            var year = date_debut.value.substring(0, 4);
            var lastDayOfYear = new Date(year, 11, 31);
            var formattedLastDayOfYear = lastDayOfYear.toISOString().substring(0, 10);
            date_fin.min = date_debut.value;
            date_fin.max = formattedLastDayOfYear;
        });
    </script>

</html>