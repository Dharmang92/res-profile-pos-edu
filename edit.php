<?php
require_once "pdo.php";
session_start();

if (!isset($_SESSION["name"])) {
    die("User not logged in");
}

$stmt = $pdo->prepare("select profile_id, first_name, last_name, headline, email, summary from profile where profile_id=:pid and user_id=:uid");
$stmt->execute(array(":pid" => $_GET["profile_id"], ":uid" => $_SESSION["user_id"]));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header('Location: index.php');
    return;
}
$fn = htmlentities($row["first_name"]);
$ln = htmlentities($row["last_name"]);
$e = htmlentities($row["email"]);
$h = htmlentities($row["headline"]);
$s = htmlentities($row["summary"]);
$id = htmlentities($row["profile_id"]);

if (isset($_POST["cancel"])) {
    header("Location: index.php");
    return;
}

if (isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["email"]) && isset($_POST["headline"]) && isset($_POST["summary"])) {
    if (strlen($_POST["first_name"]) < 1 || strlen($_POST["last_name"]) < 1 || strlen($_POST["email"]) < 1 || strlen($_POST["headline"]) < 1 || strlen($_POST["summary"]) < 1) {
        $_SESSION["fail"] = "All fields are required";
        header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
        return;
    } else {
        for ($i = 1; $i <= 9; $i++) {
            if (!isset($_POST['year' . $i])) continue;
            if (!isset($_POST['desc' . $i])) continue;
            $year = $_POST['year' . $i];
            $desc = $_POST['desc' . $i];
            if (strlen($year) == 0 || strlen($desc) == 0) {
                $_SESSION["fail"] = "All fields are required";
                header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
                return;
            }
            if (!is_numeric($year)) {
                $_SESSION["fail"] = "Year must be numeric";
                header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
                return;
            }
        }

        for ($i = 1; $i <= 9; $i++) {
            if (!isset($_POST['edu_year' . $i])) continue;
            if (!isset($_POST['edu_school' . $i])) continue;
            $year = $_POST['edu_year' . $i];
            $school = $_POST['edu_school' . $i];
            if (strlen($year) == 0 || strlen($school) == 0) {
                $_SESSION["fail"] = "All fields are required";
                header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
                return;
            }
            if (!is_numeric($year)) {
                $_SESSION["fail"] = "Year must be numeric";
                header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
                return;
            }
        }

        if (strpos($_POST["email"], "@") !== false) {
            $stmt = $pdo->prepare("update profile set first_name=:fn, last_name=:ln, email=:em, headline=:hd, summary=:sm where profile_id=:pid;");
            $stmt->execute(array(
                ":pid" => $_POST["profile_id"],
                ":fn" => $_POST["first_name"],
                ":ln" => $_POST["last_name"],
                "em" => $_POST["email"],
                ":hd" => $_POST["headline"],
                ":sm" => $_POST["summary"]
            ));

            $profile_id = $_POST["profile_id"];
            $stmt = $pdo->prepare("DELETE FROM Position WHERE profile_id=:pid");
            $stmt->execute(array(':pid' => $_GET['profile_id']));

            $rank = 1;
            for ($i = 1; $i <= 9; $i++) {
                if (!isset($_POST['year' . $i])) continue;
                if (!isset($_POST['desc' . $i])) continue;
                $year = $_POST['year' . $i];
                $desc = $_POST['desc' . $i];

                $stmt = $pdo->prepare("INSERT INTO Position (profile_id, rank, year, description) VALUES( :pid, :rank, :year, :desc)");
                $stmt->execute(array(
                    ':pid' => $_REQUEST['profile_id'],
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desc' => $desc
                ));
                $rank++;
            }

            $profile_id = $_POST["profile_id"];
            $stmt = $pdo->prepare("DELETE FROM education WHERE profile_id=:pid");
            $stmt->execute(array(':pid' => $_GET['profile_id']));

            $rank = 1;
            for ($i = 1; $i <= 9; $i++) {
                if (isset($_POST['edu_year' . $i]) && isset($_POST['edu_school' . $i])) {
                    $stmt = $pdo->prepare("SELECT * FROM institution WHERE name = :n");
                    $stmt->execute(array(':n' => $_POST['edu_school' . $i]));
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($row === false) {
                        $stmt = $pdo->prepare("INSERT INTO institution (name) VALUES (:name)");
                        $stmt->execute(array(':name' => $_POST['edu_school' . $i]));
                        $iid = $pdo->lastInsertId();
                    } else {
                        $iid = $row['institution_id'];
                    }

                    $stmt = $pdo->prepare("INSERT INTO education VALUES ( :pid, :iid, :rank, :year)");
                    $stmt->execute(array(
                        ':pid' => $profile_id,
                        ':iid' => $iid,
                        ':rank' => $rank,
                        ':year' => $_POST['edu_year' . $i]
                    ));
                    $rank++;
                }
            }

            $_SESSION["success"] = "Profile edited";
            header("Location: index.php");
            return;
        } else {
            $_SESSION["fail"] = "Email address must contain @";
            header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
            return;
        }
    }
}

?>

<html>

<head>
    <title>Dharmang Gajjar</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css" />
</head>

<body>
    <div class="container">
        <h1>Editing Profile for <?= htmlentities($_SESSION["name"]) ?></h1>
        <?php
        if (isset($_SESSION["fail"])) {
            echo "<p style='color: red'>" . htmlentities($_SESSION["fail"]) . "</p>";
            unset($_SESSION["fail"]);
        }
        ?>
        <form method="post">
            <p>First Name: <input type="text" name="first_name" size="60" value="<?= $fn ?>" /></p>
            <p>Last Name: <input type="text" name="last_name" size="60" value="<?= $ln ?>" /></p>
            <p>Email: <input type="text" name="email" size="30" value="<?= $e ?>" /></p>
            <p>Headline: <br /><input type="text" name="headline" size="80" value="<?= $h ?>" /></p>
            <p>Summary: <br /><textarea name="summary" cols="80" rows="8"><?= $s ?></textarea>
                <p>Education: <input type="submit" value="+" id="addEdu"></p>
                <div id="education_fields">
                    <?php
                    $countEdu = 0;
                    $stmt = $pdo->prepare("select year, name from education join institution on education.institution_id=institution.institution_id where profile_id=:pid order by rank;");
                    $stmt->execute(array(':pid' => $_GET['profile_id']));
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $countEdu++;
                        echo '<div id="edu' . $countEdu . '">' . "\n";
                        echo '<p>Year: <input type="text" value="' . htmlentities($row['year']) . '" name="year' . $countEdu . '"> ';
                        echo '<input type="button" value="-" onclick="$(\'#edu' . $countEdu . '\').remove();">' . "</p>\n";
                        echo '<p><input class="school" size=80 type="text" name="edu_school' . $countEdu . '"' . "value='" . htmlentities($row["name"]) . '"'  . '/></p>';
                        echo "</div>\n";
                    }
                    ?>
                </div>

                <p>Position: <input type="submit" value="+" id="addPos"></p>
                <div id="position_fields">
                    <?php
                    $countPos = 0;
                    $stmt = $pdo->prepare("SELECT year, description FROM position WHERE profile_id = :pid");
                    $stmt->execute(array(':pid' => $_GET['profile_id']));
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $countPos++;
                        echo '<div id="position' . $countPos . '">' . "\n";
                        echo '<p>Year: <input type="text" value="' . htmlentities($row['year']) . '" name="year' . $countPos . '"> ';
                        echo '<input type="button" value="-" onclick="$(\'#position' . $countPos . '\').remove();">' . "</p>\n";
                        echo '<p><textarea name="desc' . $countPos . '" rows="8" cols="80">' . htmlentities($row['description']) . '</textarea></p>';
                        echo "</div>\n";
                    }
                    ?>
                </div>
                <input type="hidden" name="profile_id" value="<?= $id ?>" />
                <p><input type="submit" value="Save"> <input type="submit" name="cancel" value="Cancel"></p>
        </form>
    </div>
</body>

</html>

<script>
    countPos = <?= $countPos ?>;
    countEdu = <?= $countEdu ?>;
    $(document).ready(function() {
        $("#addPos").click(function(event) {
            event.preventDefault();
            if (countPos >= 9) {
                alert("Maximum of nine positions entries exceeded");
                return;
            }
            countPos++;
            $("#position_fields").append(
                `<div id="position${countPos}"><p>Year: <input type='text' name='year${countPos}' value="" /> 
                <input type='button' value='-' onclick="$(\'#position${countPos}\').remove();return false;"></p>
                <textarea name='desc${countPos}' rows='8' cols='80'></textarea></div>`
            );
        });

        $("#addEdu").click(function(event) {
            event.preventDefault();
            if (countEdu >= 9) {
                alert("Maximum of nine Education entries exceeded");
                return;
            }
            countEdu++;
            $("#education_fields").append(
                `<div id="edu${countEdu}"><p>Year: <input type='text' name='edu_year${countEdu}' value="" /> 
                <input type='button' value='-' onclick="$(\'#edu${countEdu}\').remove();return false;"></p>
                <p>School: <input class='school' size=80 type='text' name='edu_school${countEdu}'></input></p></div>`
            );

            $('.school').autocomplete({
                source: "school.php"
            });
        });
    });
</script>