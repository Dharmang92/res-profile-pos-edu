<?php
require_once "pdo.php";
session_start();

if (!isset($_SESSION["name"])) {
    die("User not logged in");
}

if (isset($_POST["cancel"])) {
    header("Location: index.php");
    return;
}

if (isset($_POST["profile_id"])) {
    $stmt = $pdo->prepare("delete from profile where profile_id=:pid");
    $stmt->execute(array(":pid" => $_POST["profile_id"]));
    $_SESSION["success"] = "Profile deleted";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("select profile_id, first_name, last_name, headline, email, summary from profile where profile_id=:pid");
$stmt->execute(array(":pid" => $_GET["profile_id"]));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Missing profile_id';
    header('Location: index.php');
    return;
}

$id = htmlentities($row["profile_id"]);
$fn = htmlentities($row["first_name"]);
$ln = htmlentities($row["last_name"]);
?>

<html>

<head>
    <title>Dharmang Gajjar</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div class="container">
        <h1>Deleting Profile</h1>
        <p>First Name: <?= $fn ?></p>
        <p>Last Name: <?= $ln ?></p>
        <form method="post">
            <input type="hidden" name="profile_id" value="<?= $id ?>" />
            <input type="submit" value="Delete" />
            <input type="submit" name="cancel" value="Cancel" />
        </form>
    </div>
</body>

</html>