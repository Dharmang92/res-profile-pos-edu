<?php
require_once "pdo.php";
session_start();
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
        <h1>Dharmang Gajjar's Resume Registry</h1>

        <?php
        if (isset($_SESSION["user_id"])) {
            if (isset($_SESSION["success"])) {
                echo "<p style='color: green'>" . htmlentities($_SESSION["success"]) . "</p>";
                unset($_SESSION["success"]);
            }
            if (isset($_SESSION["error"])) {
                echo "<p style='color: red'>" . htmlentities($_SESSION["error"]) . "</p>";
                unset($_SESSION["error"]);
            }
            echo "<p><a href='logout.php'>Logout</a></p>";
            viewTable();
            echo "<p></p>";
            echo "<p><a href='add.php'>Add New Entry</a></p>";
        } else {
            echo "<p><a href='login.php'>Please log in</a></p>";
            viewTable();
        }

        function viewTable()
        {
            global $pdo;
            $check = $pdo->query("select * from profile");
            $temp = $check->fetch(PDO::FETCH_ASSOC);
            if ($temp === false) {
                echo "<p style='color:red'>No resumes inserted by the user</p>";
            } else {
                // we need to again execute query in order to get the pointer reset.
                $stmt = $pdo->query("select profile_id, first_name, last_name, headline, profile_id from profile");
                echo "<table border=1>";
                echo "<tr><td><b>Name</b></td><td><b>Headline</b></td><td><b>Action</b></td></tr>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td><a href=view.php?profile_id=" . htmlentities($row["profile_id"]) . ">" . htmlentities($row["first_name"]) . " " . htmlentities($row["last_name"]) . "</a></td>";
                    echo  "<td>" . htmlentities($row["headline"]) . "</td>";
                    echo  "<td>" . "<a href='edit.php?profile_id=" . $row['profile_id'] . "'>Edit</a> <a href='delete.php?profile_id=" . $row['profile_id'] . "'>Delete</a>" . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        ?>
    </div>
</body>

</html>