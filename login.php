<?php
require_once "pdo.php";
session_start();
unset($_SESSION["name"]);
unset($_SESSION["user_id"]);

if (isset($_POST["cancel"])) {
    header("Location: index.php");
    return;
}

if (isset($_POST['email']) && isset($_POST['pass'])) {
    $salt = 'XyZzy12*_';
    $stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';
    $check = hash('md5', $salt . $_POST['pass']);
    $stmt = $pdo->prepare("select user_id, name from users where email=:em and password=:pw");
    $stmt->execute(
        array(
            ":em" => $_POST["email"],
            ":pw" => $check
        )
    );
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row !== false) {
        error_log("Login success " . $_POST['email']);
        $_SESSION["name"] = $row['name'];
        $_SESSION["user_id"] = $row["user_id"];
        header("Location: index.php");
        return;
    } else {
        $_SESSION["error"] = "Incorrect password";
        error_log("Login fail " . $_POST['email'] . " $check");
        header("Location: login.php");
        return;
    }
}
?>

<html>

<head>
    <title>Dharmang Gajjar</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1>Please Log In</h1>
        <?php
        if (isset($_SESSION["error"])) {
            echo "<p style='color: red'>" . htmlentities($_SESSION["error"]) . "</p>";
            unset($_SESSION["error"]);
        }
        ?>

        <form method="post" action="login.php">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" />
            <br />
            <label for="pass">Password</label>
            <input type="text" name="pass" id="pass" />
            <br />
            <input type="submit" value="Log In" onclick="return doValidate()" />
            <input type="submit" name="cancel" value="Cancel">
        </form>
    </div>
</body>

<script>
    function doValidate() {
        console.log('Validating...');
        try {
            addr = document.getElementById('email').value;
            pw = document.getElementById('pass').value;
            console.log("Validating addr=" + addr + " pw=" + pw);
            if (addr == null || addr == "" || pw == null || pw == "") {
                alert("Both fields must be filled out");
                return false;
            }
            if (addr.indexOf('@') == -1) {
                alert("Invalid email address");
                return false;
            }
            return true;
        } catch (e) {
            return false;
        }
        return false;
    }
</script>

</html>