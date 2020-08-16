<?php
require_once "pdo.php";
session_start();
// Could not load profile
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
        <h1>Profile Information</h1>

        <?php
        if (isset($_GET["profile_id"])) {
            $stmt = $pdo->prepare("select first_name, last_name, email, headline, summary from profile where profile_id=:pid");
            $stmt->execute(
                array(":pid" => $_GET["profile_id"])
            );
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
                $_SESSION["error"] = "Could not load profile";
                header("Location: index.php");
                return;
            } else {
                echo "<p>First Name: " . htmlentities($row["first_name"]) . "</p>";
                echo "<p>Last Name: " . htmlentities($row["last_name"]) . "</p>";
                echo "<p>Email: " . htmlentities($row["email"]) . "</p>";
                echo "<p>Headline: " . htmlentities($row["headline"]) . "</p>";
                echo "<p>Summary: " . htmlentities($row["summary"]) . "</p>";

                echo "<p>Position</p><ul>";
                $stmt = $pdo->prepare("select year, description from position where profile_id=:pid order by rank;");
                $stmt->execute(array(":pid" => $_GET["profile_id"]));
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<li>" . htmlentities($row["year"]) . ": " . htmlentities($row["description"]) . "</li>";
                }
                echo "</ul>";

                echo "<p><a href='index.php'>Done</a></p>";
            }
        } else {
            $_SESSION["error"] = "Missing profile_id";
            header("Location: index.php");
            return;
        }

        ?>
    </div>
</body>

</html>