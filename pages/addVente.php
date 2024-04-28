<?php
    include "../database.php";
    session_start();
    $requete = $db->prepare("INSERT INTO vente(date_vnt, serveur) VALUES (CURDATE(),?)");
    $requete->bindValue(1, $_SESSION["id_user"]);
    $requete->execute();
    $id = $db->lastInsertId();
    header("Location:caise.php?id_vnt=" . $id);
?>