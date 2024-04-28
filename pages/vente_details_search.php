<?php
include("../database.php");

$search = $_POST["search"];
$id = $_POST["id"];
$requete = $db->prepare("SELECT c.*, p.libelle FROM contenir c, produit p, vente v WHERE c.barcode = p.barcode AND v.id_vnt = c.id_vnt AND c.id_vnt = :id AND c.barcode LIKE :searchValue");
$requete->bindValue(":id", $id);
$requete->bindValue(":searchValue", $search . '%');
$requete->execute();
$produit = $requete->fetchAll(PDO::FETCH_ASSOC);
echo '
<tr id="header">
                <th>CODE PRODUIT</th>
                <th>LIBELLE</th>
                <th>QUANTITE</th>
                <th>MONTANT</th>
                <th>SUPPRIMER</th>';

if ($_SESSION["type_user"] == "admin")
    echo "<th>MONTANT ACHAT</th>";
echo '</tr>';

foreach ($produit as $produit) {
    echo "<tr>";
    echo "<td>" . $produit["barcode"] . "</td>";
    echo "<td>" . $produit["libelle"] . "</td>";
    echo "<td>" . $produit["quantite"] . "</td>";
    echo "<td>" . $produit["total_ht"] . " DH</td>";
    if($_SESSION["type_user"] == "admin")
        echo "<td>" . $produit["total_achat"] . " DH</td>";
    echo '<td><a href="?deleteVnt=' . $produit["id_vnt"] . '&deletePrd=' . $produit["barcode"] . '&show=' . $id . '&qnt=' . $produit["quantite"] . '" style="color:#7b2cbf"><i class="fa-solid fa-trash fa-lg" style="color:#7b2cbf;"></i></a></td>';
    echo "</tr>";
}
