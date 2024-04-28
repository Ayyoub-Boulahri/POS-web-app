<?php
include("../database.php");
session_start();

$searchBy = $_POST["searchBy"];
$search = $_POST["search"];
$searchByDispo = $_POST["searchByDispo"];

if($searchByDispo == 2) {
    $requete = $db->prepare("SELECT * FROM produit WHERE $searchBy LIKE :searchValue AND barcode IN (SELECT barcode FROM produit)");
} else if($searchByDispo == 1) {
    $requete = $db->prepare("SELECT * FROM produit WHERE $searchBy LIKE :searchValue AND barcode IN (SELECT barcode FROM produit WHERE stock != 0)");
} else {
    $requete = $db->prepare("SELECT * FROM produit WHERE $searchBy LIKE :searchValue AND barcode IN (SELECT barcode FROM produit WHERE stock = 0)");
}


// Assuming $db is your database connection object (e.g., PDO)
$requete->bindValue(':searchValue', $search . '%');
$requete->execute();
$produits = $requete->fetchAll(PDO::FETCH_ASSOC);

echo '<table class="table">
<tr id="header">
    <th>CODE PRODUIT</th>
    <th>LIBELLE</th>';
if($_SESSION["type_user"] == "admin"){
     echo "<th>PRIX ACHAT</th>";
}
echo '<th>PRIX VENTE</th>
    <th>STOCK</th>';
    if($_SESSION["type_user"] == "admin"){
        echo "<th>MODIFIER</th>";
    }
echo '</tr>';

foreach ($produits as $produit) {
                echo "<tr>";
                echo "<td>" . $produit["barcode"] . "</td>";
                echo "<td>" . $produit["libelle"] . "</td>";
		if($_SESSION["type_user"] == "admin"){
                    echo "<td>" . $produit["prix_achat"] . " DH</td>";
                }
                echo "<td>" . $produit["prix"] . " DH</td>";
                echo "<td>" . $produit["stock"] . "</td>";
                if($_SESSION["type_user"] == "admin"){
                    echo '<td><a href="?edit=' . $produit["barcode"] . '" style="color:#7b2cbf"><i class="fa-solid fa-pen-to-square fa-lg "></i></a></td>';
                }
                echo "</tr>";
            }
