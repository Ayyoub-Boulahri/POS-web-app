<?php
    include "database.php";
    session_start();
    unset($_SESSION["id_user"]);
    unset($_SESSION["type_user"]);
    unset($_SESSION["log"]);
    session_destroy();
    header("Location:index.php");
?>
