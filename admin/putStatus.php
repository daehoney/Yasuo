<?php
include("../dbConnection.php");

$puuid = urldecode($_GET["puuid"]);

$sql_select="SELECT status from wp_user where puuid='$puuid'";
$result = mysqli_query($mysqli, $sql_select);

$statusRes = mysqli_fetch_array($result); 

$status = 'Y';

if($statusRes['status'] == 'Y') {
    $status = 'N';
} else if ($statusRes['status'] == 'N') {
    $status = 'B';
} else if ($statusRes['status'] == 'B') {
    $status = 'Y';
}

$sql_update="UPDATE wp_user SET status = '$status' WHERE puuid= '$puuid'";

if (!mysqli_query($mysqli,$sql_update)) {
    die('Error: ' . mysqli_error($mysqli));
} else {
    echo $status;
}

mysqli_close($mysqli);
?>