<!DOCTYPE html>
<html lang="fr">

<?php
include "../database.php";
session_start();
$cmp = 0;
if ($_SESSION["log"] == 1 && !empty($_GET["id_vnt"])) {
    $cmp = 1;
} else {
    echo $cmp;
    header("Location:../index.php");
}

date_default_timezone_set('Africa/Casablanca');

$id_vnt = $_GET["id_vnt"];

if (!empty($_GET["boune"])) {
    $id_vnt = $_GET["boune"];
    $requete = $db->prepare("UPDATE vente SET ttc = montant_total + ((montant_total * 20)/100) WHERE id_vnt = ?");
    $requete->bindValue(1, $id_vnt);
    $requete->execute();
    header("Location:boune.php?id_vnt=" . $_GET["id_vnt"]);
}
?>

<head>
    <meta charset="utf-8">
    <title>BON</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v6.0.0-beta3/css/all.css">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/boune.css">
    <link rel="stylesheet" href="../css/menu.css">
    <style>
        nav {
            margin-bottom: 20px;
        }

        @media print {
            .navbar {
                display: none !important;
            }
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

    <?php
    $requete = $db->prepare("SELECT v.*, u.username FROM vente v, user u  WHERE u.id_user = v.serveur AND id_vnt = ?");
    $requete->bindValue(1, $id_vnt);
    $requete->execute();
    $vente = $requete->fetch();

    $requete = $db->prepare("SELECT c.*, p.libelle FROM contenir c, produit p WHERE p.barcode = c.barcode AND id_vnt = ?");
    $requete->bindValue(1, $id_vnt);
    $requete->execute();
    $produits = $requete->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <table class="bill-details">
        <tbody>
            <tr>
                <th class="center-align" colspan="2"><span class="receipt">GLOBAL CLICK</span></th>
            </tr>
            <tr>
                <td class="center-align" colspan="2">Address du magasign</td>
            </tr>
            <tr>
                <td class="center-align" colspan="2">MEKNES</td>
            </tr>
            <tr>
                <td class="center-align" colspan="2">05 35 52 04 31</td>
            </tr>

            <tr>
                <td>Date : <span><?php echo $vente["date_vnt"] ?></span>&nbsp;&nbsp;&nbsp;&nbsp; <span><?php echo date("H:i:s") ?></span></td>
            </tr>
            <tr>
                <td>Facture N° : <span><?php echo $vente["id_vnt"] ?></span></td>
            </tr>
            <tr>
                <td>Serveur : <span><?php echo $vente["username"] ?></span></td>
            </tr>
        </tbody>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th class="heading name">ARTICLE</th>
                <th class="heading qty">QNE</th>
                <th class="heading rate">Prix</th>
                <th class="heading amount">Total</th>
            </tr>
        </thead>

        <tbody>
            <?php
            foreach ($produits as $produit) {
                echo "<tr>";
                echo "<td>" . $produit["libelle"] . "</td>";
                echo "<td>" . $produit["quantite"] . "</td>";
                echo "<td class=\"price\">" . $produit["prix_unit"] . " DH</td>";
                echo "<td class=\"price\">" . $produit["total_ht"] . " DH</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <table class="items totalTable">
        <tbody>
            <?php if ($vente["reduction"] != 0) {
                echo '<tr>';
                echo '<th colspan="3" class="text" style="text-align: left;">Total</th>';
                echo '<th class="price" style="text-align: right;">' . $vente["montant_total"] . ' DH</th>';
                echo '</tr>';
                echo "<tr>";
                echo '<th colspan="3" class="text" style="text-align: left;">Reduction</th>';
                echo '<th class="price" style="text-align: right;">' . $vente["reduction"] . ' %(' . (($vente["montant_total"] * $vente["reduction"]) / 100)  . ')</th>';
                echo "</tr>";
                echo "<tr>";
                echo '<th colspan="3" class="total text" style="text-align: left;">Total</th>';
                echo '<th class="total price">' . ($vente["montant_total"] - ($vente["montant_total"] * $vente["reduction"]) / 100) . ' DH</th>';
                echo "</tr>";
            } else {
                echo '<tr>';
                echo '<th colspan="3" class="total text" style="text-align: left;">Total</th>';
                echo '<th class="total price" style="text-align: right;">' . $vente["montant_total"] . ' DH</th>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
    <section>
        <p style="text-align:center;margin-top:30px;margin-bottom:100px">
            MERCI DE VOTRE VISITE ET A BIENTOT ...
        </p>
        .
    </section>
</body>

</html>