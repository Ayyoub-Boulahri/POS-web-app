<?php
    include("../database.php");
    $barcode = $_POST["barcode"];

    $requete = $db->prepare("SELECT * FROM produit WHERE barcode='$barcode'");
    $requete->execute();
    $produit = $requete->fetch();

    $count = $requete->rowCount();

    $libelle = "";
    $prix = 0.0;
    $stock = 0;
    $prix_achat = 0;

    if($count > 0) {
        $libelle = $produit["libelle"];
        $prix = $produit["prix"];
        $stock = $produit["stock"];
        $prix_achat = $produit["prix_achat"];
    }

    echo '
    <div class="rowDiv">
        <label for="libelle">Libelle</label>
        <input type="text" class="form-control" name="libelle" id="libelle" value="' . $libelle . '">
    </div>
    <div class="rowDiv">
        <label for="prix_achat">Prix d\'achat</label>
        <input type="number" min=0 class="form-control" step="any" name="prix_achat" id="prix_achat" value="' . $prix_achat . '" onchange="changePrixVente()">
    </div>
    <div class="rowDiv">
        <label for="prix">Prix de vente</label>
        <input type="number" min=0 class="form-control" step="any" name="prix" id="prix" value="' . $prix . '">
    </div>
    ';

    if($count > 0) {
        echo '<div class="rowDiv">
                <label for="disponible">Stock Disponible</label>
                <input type="number"  min=0 class="form-control" step="any" name="disponible" id="disponible" value="' . $stock . '">
            </div>';
    }

    echo '<div class="rowDiv">
        <label for="stock">nouveau Stock</label>
        <input type="number"  min=0 class="form-control" name="stock" id="stock">
    </div>
    ';
?>