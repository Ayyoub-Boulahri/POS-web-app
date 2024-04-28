<?php
    $db = new PDO("mysql:host=localhost;dbname=bibliotheque","root","");
    $db->query("SET NAMES 'utf8'");
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
?>