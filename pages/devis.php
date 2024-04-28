<!DOCTYPE html>
<html lang="fr">

<?php
include "../database.php";
session_start();
$cmp = 0;
if ($_SESSION["log"] == 1 && !empty($_GET["id_devis"])) {
    $cmp = 1;
} else {
    echo $cmp;
    header("Location:../index.php");
}

$id_devis = $_GET["id_devis"];


if (isset($_GET["devis"])) {
    $requete = $db->prepare("UPDATE devis SET total_devis_ttc = total_devis_ht + ((total_devis_ht * 20)/100) WHERE id_devis = ?");
    $requete->bindValue(1, $id_devis);
    $requete->execute();
    header("Location:devis.php?id_devis=" . $_GET["id_devis"]);
}

?>

<head>
    <meta charset="utf-8">
    <title>DEVIS</title>
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
            include "../includes/serviceMenu.php";
        ?>
    </nav>
    <div class="bodyDiv">

        <?php
        $requete = $db->prepare("SELECT * FROM devis WHERE id_devis = ?");
        $requete->bindValue(1, $id_devis);
        $requete->execute();
        $devis = $requete->fetch();

        $requete = $db->prepare("SELECT * FROM service WHERE id_devis = ?");
        $requete->bindValue(1, $id_devis);
        $requete->execute();
        $services = $requete->fetchAll(PDO::FETCH_ASSOC);

        ?>

        <header>
            <h1>DEVIS</h1>
            <address>
                <p>Global Click</p>
                <p>near lEnsam</p>
                <p>globalclick64@gmail.com</p>
                <p>+212 077777-7674</p>
            </address>
            <address style="width: 100%; display:flex;flex-direction:column;align-items:flex-end">
                <div>
                <?php
                    if(!empty($devis["full_name_client"]))
                        echo '<p>Nom : ' . $devis["full_name_client"] . '</p>';
                    if(!empty($devis["addresse"]))
                        echo '<p>ICE : ' . $devis["addresse"] . '</p>';
                    if(!empty($devis["code_postal"]))
                        echo '<p>code postal : ' . $devis["code_postal"] . '</p>';
                    if(!empty($devis["tel"]))
                        echo '<p>tel : ' . $devis["tel"] . '</p>';

                ?>
                </div>
            </address>
        </header>
        <article>
            <img src="../images/logo.png" alt="" style="width:40%">
            
            <table class="meta">
                <tr>
                    <th><span>N° Devis</span></th>
                    <td><span>1</span></td>
                </tr>
                <tr>
                    <th><span>Date</span></th>
                    <td>
                        <span>
                            <?php
                            $originalDate = $devis["date_devis"];

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
                        <th><span>Description</span></th>
                        <th><span>Quantite</span></th>
                        <th><span>Prix Unitaire</span></th>
                        <th><span>Total HT</span></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($services as $service) {
                        echo "<tr>";
                        echo "<td><span>" . nl2br($service["description"]) . "</span></td>";
                        echo "<td><span>" . $service["quantite"] . "</span></td>";
                        echo "<td><span>" . $service["prix_unit_service"] . " DH</span></td>";
                        echo "<td><span>" . $service["total_ht_service"] . " DH</span></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <table class="balance">
                <tr>
                    <th><span>Total HT</span></th>
                    <td><span><?php echo $devis["total_devis_ht"] ?> DH</span></td>
                </tr>
                <tr>
                    <th><span>TVA (20%)</span></th>
                    <td><span><?php echo number_format(($devis["total_devis_ht"] * 20 / 100), 2, '.', ','); ?> DH</span></td>
                </tr>
                <tr>
                    <th><span>Total TTC</span></th>
                    <td><span><?php echo $devis["total_devis_ttc"] ?> DH</span></td>
                </tr>
            </table>
        </article>
        <aside>
            <h1></h1>
            <div style="text-align: center;">
                <p>MERCI ET A BIENTOT ...</p>
            </div>
        </aside>
    </div>
</body>

</html>