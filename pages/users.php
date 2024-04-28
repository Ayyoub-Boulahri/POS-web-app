<!DOCTYPE html>
<html lang="en">

<?php
include "../database.php";
session_start();
$cmp = 0;
if ($_SESSION["log"] == 1) {
    $cmp = 1;
} else {
    echo $cmp;
    header("Location:../index.php");
}

if (isset($_POST["ajouter"])) {
    if (!empty($_POST["id_user"]) && !empty($_POST["username"]) && !empty($_POST["password"])) {
        $id_user = $_POST["id_user"];
        $username = $_POST["username"];
        $type_user = $_POST["type_user"];
        $password = $_POST["password"];

        $requete = $db->prepare("SELECT COUNT(*) as count FROM user WHERE id_user = ?");
        $requete->bindValue(1, $id_user);
        $requete->execute();
        $count = $requete->fetchColumn();
        $is_valid = $count["count"] == 0 ? true : false;

        if ($is_valid) {
            $requete = $db->prepare("INSERT INTO user VALUES (?,?,?,?)");
            $requete->bindValue(1, $id_user);
            $requete->bindValue(2, $type_user);
            $requete->bindValue(3, $username);
            $requete->bindValue(4, $password);
            $requete->execute();
        }
    }
    header("Location:users.php");
}

if (isset($_POST["modifier"])) {
    if (!empty($_POST["id_user"]) && !empty($_POST["username"]) && !empty($_POST["password"])) {
        $id_user = $_POST["id_user"];
        $username = $_POST["username"];
        $type_user = $_POST["type_user"];
        $password = $_POST["password"];
        $old_id = $_POST["old_id"];

        $is_valid = true;
        if ($old_id != $id_user) {
            $requete = $db->prepare("SELECT COUNT(*) as count FROM user WHERE id_user = ?");
            $requete->bindValue(1, $id_user);
            $requete->execute();
            $count = $requete->fetchColumn();
            $is_valid = $count == 0 ? true : false;
        }
        if ($is_valid) {
            $requete = $db->prepare("UPDATE user set id_user = ?, type_user = ?, username = ?, password = ? WHERE id_user=?");
            $requete->bindValue(1, $id_user);
            $requete->bindValue(2, $type_user);
            $requete->bindValue(3, $username);
            $requete->bindValue(4, $password);
            $requete->bindValue(5, $old_id);
            $requete->execute();
        }
    }
    header("Location:users.php");
}

if (isset($_GET["delete"])) {
    $id_user = $_GET["delete"];
    $requete = $db->prepare("DELETE FROM user WHERE id_user = ?");
    $requete->bindValue(1, $id_user);
    $requete->execute();
    header("Location:users.php");
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/icon.png">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v6.0.0-beta3/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/stock.css">
    <title>USERS</title>
    <style>
        #users .nav-link {
            color: black;
        }

        #users {
            background-color: white;
            border-radius: 4px;
        }

        .fixed-button {
            position: fixed;
            bottom: 40px;
            right: 40px;
            background-color: #7b2cbf;
            color: white;
            transition: .3s;
            border: 2px solid #7b2cbf;
            box-shadow: 2px 2px 12px rgba(0, 0, 0, 0.2), -1px -1px 8px rgba(0, 0, 0, 0.2);
            z-index: 10000;
        }

        .fixed-button:hover {
            background-color: white;
            color: #7b2cbf;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <?php
        include "../includes/adminMenu.php";
        ?>
    </nav>

    <?php
    if (isset($_GET["edit"])) {
        $idE = $_GET["edit"];
        $requete = $db->prepare("SELECT * FROM user WHERE id_user = ?");
        $requete->bindValue(1, $idE);
        $requete->execute();
        $user = $requete->fetch();
    }
    ?>
    <button onclick="showAddDialog()" class="btn fixed-button"><i class="fa-solid fa-plus fa-lg"></i></button>


    <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form action="" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-centre" id="exampleModalLongTitle">MODIFICATION</h5>
                    </div>
                    <input type="hidden" name="old_id" value="<?php echo $user["id_user"] ?>">
                    <div class="modal-body">
                        <div class="cotainer">
                            <div class="row">
                                <div class="col-25 text-left">ID USER</div>
                                <div class="col-75">
                                    <input type="text" class="form-control" name="id_user" id="" placeholder="id user" value="<?php echo $user["id_user"] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-25 text-left">TYPE USER</div>
                                <div class="col-75">
                                    <select class="form-select" name="type_user">
                                        <option value="admin" <?php echo $user["type_user"] == "admin" ? "selected" : "" ?>>admin</option>
                                        <option value="caise" <?php echo $user["type_user"] == "caise" ? "selected" : "" ?>>caise</option>
                                        <option value="service" <?php echo $user["type_user"] == "service" ? "selected" : "" ?>>service</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-25 text-left" id="multiple">USERNAME</div>
                                <div class="col-75">
                                    <input type="text" min=0 class="form-control" name="username" id="" placeholder="USERNAME" value="<?php echo $user["username"] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-25 text-left" id="multiple">PASSOWRD</div>
                                <div class="col-75">
                                    <input type="text" min=0 class="form-control" name="password" id="" placeholder="PASSWORD" value="<?php echo $user["password"] ?>">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn text-white" value="Modifier" role="button" name="modifier" style="background-color:#7b2cbf">
                        <input type="button" class="btn btn-secondary" value="Annuler" id="stop" role="button" name="annuler" onclick=window.location.href='users.php'>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form action="" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-centre" id="exampleModalLongTitle">MODIFICATION</h5>
                    </div>
                    <div class="modal-body">
                        <div class="cotainer">
                            <div class="row">
                                <div class="col-25 text-left">ID USER</div>
                                <div class="col-75">
                                    <input type="text" class="form-control" name="id_user" id="" placeholder="id user">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-25 text-left">TYPE USER</div>
                                <div class="col-75">
                                    <select class="form-select" name="type_user">
                                        <option value="admin">admin</option>
                                        <option value="caise">caise</option>
                                        <option value="service">service</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-25 text-left" id="multiple">USERNAME</div>
                                <div class="col-75">
                                    <input type="text" min=0 class="form-control" name="username" id="" placeholder="USERNAME">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-25 text-left" id="multiple">PASSOWRD</div>
                                <div class="col-75">
                                    <input type="text" min=0 class="form-control" name="password" id="" placeholder="PASSWORD">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn text-white" value="Ajouter" role="button" name="ajouter" style="background-color:#7b2cbf">
                        <input type="button" class="btn btn-secondary" value="Annuler" id="stop" role="button" name="annuler" onclick=window.location.href='users.php'>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <div class="container mt-5">
        <table class="table">
            <tr id="header">
                <th>ID USER</th>
                <th>TYPE USER</th>
                <th>USERNAME</th>
                <th>PASSWORD</th>
                <th>MODIFIER</th>
                <th>SUPPRIMER</th>
                <?php
                $requete = $db->prepare("SELECT * FROM user");
                $requete->execute();
                $users = $requete->fetchAll(PDO::FETCH_ASSOC);
                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td>" . $user["id_user"] . "</td>";
                    echo "<td>" . $user["type_user"] . "</td>";
                    echo "<td>" . $user["username"] . "</td>";
                    echo "<td>" . $user["password"] . "</td>";
                    echo '<td><a href="?edit=' . $user["id_user"] . '" style="color:#7b2cbf"><i class="fa-solid fa-pen-to-square fa-lg "></i></a></td>';
                    echo '<td><a href="?delete=' . $user["id_user"] . '" style="color:#7b2cbf"><i class="fa-solid fa-trash fa-lg "></i></a></td>';
                    echo "</tr>";
                }
                ?>

        </table>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script>
        var url = window.location.href;
        var str = url.substring(url.indexOf("?") + 1);
        if (str.includes("edit")) {
            $("#exampleModalCenter2").modal()
        }

        function showAddDialog() {
            $("#exampleModalCenter").modal()
        }
    </script>
</body>

</html>