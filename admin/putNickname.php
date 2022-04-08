<?php
include("../dbConnection.php");

$name1 = urldecode($_GET["name1"]);
$name2 = urldecode($_GET["name2"]);

if(trim($name1) == "" || trim($name2) == "") {
    echo "닉네임을 다시 입력해주세요.";
    exit;
}

$sql_select="SELECT puuid from wp_nickname where nickname='$name1' order by nidx desc LIMIT 0,1";
$result = mysqli_query($mysqli, $sql_select);

if(mysqli_num_rows($result) == 0) {
    echo "해당 닉네임의 휘핑 유저는 없습니다. [$name1]";
    mysqli_close($mysqli);
    exit;
}

$puuidRes = mysqli_fetch_array($result); 

$sql_insert="INSERT INTO wp_nickname (puuid, nickname) VALUE ('{$puuidRes['puuid']}', '$name2')";

if (!mysqli_query($mysqli,$sql_insert)) {
    die('Error: ' . mysqli_error($mysqli));
} else {
    echo "success";
}

mysqli_close($mysqli);
?>