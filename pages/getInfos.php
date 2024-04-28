<?php
include "../database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['barcode'])) {
        $barcode = $_POST['barcode'];
        $sql = $db->prepare("SELECT libelle, prix, stock, prix_achat FROM produit WHERE barcode = ?");
        $sql->bindValue(1, $barcode);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(null);
        }
    }
}
?>