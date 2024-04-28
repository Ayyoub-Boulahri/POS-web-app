<?php
include("../database.php");

$search = $_POST["search"];
$id = $_POST["id"];
$requete = $db->prepare("SELECT * FROM service WHERE id_devis = :id AND description LIKE :searchValue");
$requete->bindValue(":id", $id);
$requete->bindValue(":searchValue", $search . '%');
$requete->execute();
$services = $requete->fetchAll(PDO::FETCH_ASSOC);
echo '
<tr id="header">
                <th>CODE PRODUIT</th>
                <th>LIBELLE</th>
                <th>QUANTITE</th>
                <th>MONTANT</th>
                <th>SUPPRIMER</th>
            </tr>';

foreach ($services as $service) {
    echo "<tr>";
    echo "<td>" . $service["description"] . "</td>";
    echo "<td>" . $service["quantite"] . "</td>";
    echo "<td>" . $service["prix_unit_service"] . "</td>";
    echo "<td>" . $service["total_ht_service"] . " DH</td>";
    echo '<td><a href="?deleteS=' . $service["id_service"] . '" style="color:#7b2cbf"><i class="fa-solid fa-trash fa-lg" style="color:#7b2cbf;"></i></a></td>';
    echo "</tr>";
}
