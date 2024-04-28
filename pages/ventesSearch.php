<?php
include("../database.php");
session_start();

$searchBy = $_POST["searchBy"];
$search = $_POST["search"];

$requete = $db->prepare("SELECT v.*, u.username, SUM(c.total_achat) AS total_achat FROM vente v, contenir c, user u WHERE u.id_user = v.serveur AND v.id_vnt = c.id_vnt AND v.$searchBy LIKE :searchValue GROUP BY id_vnt ORDER BY id_vnt ASC");
$requete->bindValue(':searchValue', $search . '%');
$requete->execute();
$ventes = $requete->fetchAll(PDO::FETCH_ASSOC);

echo '
<tr id="header">
    <th>N°</th>
    <th>SERVEUR</th>
    <th>DATE</th>
    <th>TOTAL HT</th>
    <th>TOTAL TTC</th>
    <th>REDUCTION</th>';
if ($_SESSION["type_user"] == "admin")
    echo "<th>MONTANT ACHAT</th>";
echo '<th>Facture</th>
    <th>Bon</th>
    <th>SUPPRIMER</th>
    <th>DETAILS</th>
</tr>';


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
    echo '<td><a href="facture.php?id_vnt=' . $vente["id_vnt"] . '" style="color:#7b2cbf"><i class="fa-solid fa-receipt fa-lg" style="color:#7b2cbf;"></i></a></td>';
    echo '<td><a href="boune.php?id_vnt=' . $vente["id_vnt"] . '" style="color:#7b2cbf"><i class="fa-solid fa-receipt fa-lg" style="color:#7b2cbf;"></i></a></td>';
    echo '<td><a href="?delete=' . $vente["id_vnt"] . '" style="color:#7b2cbf"><i class="fa-solid fa-trash fa-lg" style="color:#7b2cbf;"></i></a></td>';
    echo '<td><a href="?show=' . $vente["id_vnt"] . '" style="color:#7b2cbf"><i class="fa-solid fa-eye fa_lg" style="color:#7b2cbf;"></i></i></a></td>';
    echo "</tr>";
}
