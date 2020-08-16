<?php
require_once("pdo.php");
header("Content-Type: application/json; charset=utf-8");
$stmt = $pdo->prepare("select name from institution where name like :prefix");
$stmt->execute(array(
    ":prefix" => ($_REQUEST["term"] . "%")
));

$vals = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $vals[] = $row["name"];
}

echo (json_encode($vals, JSON_PRETTY_PRINT));
