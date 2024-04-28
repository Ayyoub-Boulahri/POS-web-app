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

$id_vnt = $_GET["id_vnt"];

if (!empty($_GET["facture"])) {
    $id_vnt = $_GET["facture"];
    $requete = $db->prepare("UPDATE vente SET ttc = montant_total + ((montant_total * 20)/100) WHERE id_vnt = ?");
    $requete->bindValue(1, $id_vnt);
    $requete->execute();
    header("Location:facture.php?id_vnt=" . $_GET["id_vnt"]);
}
?>

<head>
    <meta charset="utf-8">
    <title>FACTURE</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v6.0.0-beta3/css/all.css">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/facture.css">
    <link rel="stylesheet" href="../css/menu.css">
    <style>
        body {
            background-color: transparent;
            padding: 0;
            margin: 0;
        }

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
    <div class="bodyDiv">

        <?php
        $requete = $db->prepare("SELECT * FROM vente WHERE id_vnt = ?");
        $requete->bindValue(1, $id_vnt);
        $requete->execute();
        $vente = $requete->fetch();

        $requete = $db->prepare("SELECT c.*, p.libelle FROM contenir c, produit p WHERE p.barcode = c.barcode AND id_vnt = ?");
        $requete->bindValue(1, $id_vnt);
        $requete->execute();
        $produits = $requete->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <header>
            <h1>FACTURE</h1>
            <address>
                <p>Global Click</p>
                <p>near lEnsam</p>
                <p>globalclick64@gmail.com</p>
                <p>+212 077777-7674</p>
                <br>
                <br>
            </address>
        </header>
        <article>
            <img src="../images/logo.png" alt="" style="width:40%">
            <table class="meta">
                <tr>
                    <th><span>N° Facture</span></th>
                    <td><span><?php echo $vente["id_vnt"] ?></span></td>
                </tr>
                <tr>
                    <th><span>Date</span></th>
                    <td>
                        <span>
                            <?php
                            $originalDate = $vente["date_vnt"];

                            // Manually translate month names to French
                            $monthNames = array(
                                'January' => 'janvier',
                                'February' => 'février',
                                'March' => 'mars',
                                'April' => 'avril',
                                'May' => 'mai',
                                'June' => 'juin',
                                'July' => 'juillet',
                                'August' => 'août',
                                'September' => 'septembre',
                                'October' => 'octobre',
                                'November' => 'novembre',
                                'December' => 'décembre'
                            );

                            // Format the date using the translated month names
                            $formattedDate = date("F j, Y", strtotime($originalDate));
                            $formattedDate = strtr($formattedDate, $monthNames);

                            echo $formattedDate;
                            ?>
                        </span>
                    </td>
                </tr>
            </table>
            <table class="inventory">
                <thead>
                    <tr>
                        <th><span>Designation</span></th>
                        <th><span>Quantite</span></th>
                        <th><span>Prix Unitaire</span></th>
                        <th><span>Total HT</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($produits as $produit) {
                        echo "<tr>";
                        echo "<td><span>" . $produit["libelle"] . "</span></td>";
                        echo "<td><span>" . $produit["quantite"] . "</span></td>";
                        echo "<td><span>" . $produit["prix_unit"] . " DH</span></td>";
                        echo "<td><span>" . $produit["total_ht"] . " DH</span></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <table class="balance">
                <tr>
                    <th><span>Total HT</span></th>
                    <td><span><?php echo $vente["montant_total"] ?> DH</span></td>
                </tr>
                <tr>
                    <th><span>TVA (20%)</span></th>
                    <td><span><?php echo number_format(($vente["montant_total"] * 20 / 100), 2, '.', ','); ?> DH</span></td>
                </tr>
                <tr>
                    <th><span>Total TTC</span></th>
                    <td><span><?php echo $vente["ttc"] ?> DH</span></td>
                </tr>
            </table>
        </article>
        <aside>
            <h1></h1>
            <div style="text-align: center;">
                <p>MERCI DE VOTRE VISITE ET A BIENTOT ...</p>
            </div>
        </aside>
    </div>
</body>

</html>