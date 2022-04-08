<?php
include("./dbConnection.php");

$l_puuid = $_GET["l_puuid"];
$r_puuid = $_GET["r_puuid"];
$point = $_GET["point"];
$date = $_GET["date"];

if($l_puuid == "" || $r_puuid == "" || $point == "" || $date == "") {
    echo "필수 파라미터가 없습니다.";
    exit;
}

$insert_select="INSERT INTO wp_late (l_puuid, r_puuid, point, date) VALUES ('$l_puuid', '$r_puuid', $point, '$date')";

if (!mysqli_query($mysqli,$insert_select)) {
    die('Error: ' . mysqli_error($mysqli));
} else {
    echo "success";
}

mysqli_close($mysqli);
?>