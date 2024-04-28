<?php
include("../database.php");

$searchBy = $_POST["searchBy"];
$search = $_POST["search"];

$requete = $db->prepare("SELECT * FROM devis WHERE $searchBy LIKE :searchValue ORDER BY id_devis ASC");
$requete->bindValue(':searchValue', $search . '%');
$requete->execute();
$allDevis = $requete->fetchAll(PDO::FETCH_ASSOC);

echo '
<tr id="header">
    <th>N°</th>
    <th>NOM CLIENT</th>
    <th>DATE</th>
    <th>TOTAL HT</th>
    <th>TOTAL TTC</th>
    <th>Devis</th>
    <th>SUPPRIMER</th>
    <th>DETAILS</th>
</tr>';

foreach ($allDevis as $devis) {
    echo "<tr>";
    echo "<td>" . $devis["id_devis"] . "</td>";
    echo "<td>" . $devis["full_name_client"] . "</td>";
    echo "<td>" . $devis["date_devis"] . "</td>";
    echo "<td>" . $devis["total_devis_ht"] . " DH</td>";
    echo "<td>" . $devis["total_devis_ttc"] . " DH</td>";
    echo '<td><a href="devis.php?id_devis=' . $devis["id_devis"] . '" style="color:#7b2cbf"><i class="fa-solid fa-receipt fa-lg" style="color:#7b2cbf;"></i></a></td>';
    echo '<td><a href="?delete=' . $devis["id_devis"] . '" style="color:#7b2cbf"><i class="fa-solid fa-trash fa-lg" style="color:#7b2cbf;"></i></a></td>';
    echo '<td><a href="devisDetails.php?show=' . $devis["id_devis"] . '" style="color:#7b2cbf"><i class="fa-solid fa-eye fa_lg" style="color:#7b2cbf;"></i></i></a></td>';
    echo "</tr>";
}
