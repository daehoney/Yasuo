<?php
include("../dbConnection.php");

$userList = json_decode($_POST['userList']);
$startDate = $_POST['startDate'];
$endDate = $_POST['endDate'];

function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

function array_key_init($array) {
    $new_array = array();
    $i = 0;
    foreach ($array as $k => $v) {
        $new_array[$i] = $array[$k];
        $i++;
    }
    return $new_array;
}

if($startDate == "" || $endDate == "") {
    $startDate = date("Y-m-d", strtotime("-1 day"));
    $endDate = date("Y-m-d", strtotime("-1 day"));
}

$startTime = strtotime($startDate." 00:00:00");
$endTime = strtotime($endDate." 23:59:59");

$jsonObj = array();
$array = array();

foreach($userList as $user) {
    if ($user->status != "Y") {
        continue;
    }
    $element = array();
    $element['puuid'] = $user->puuid; 
    $sql_select="SELECT distinct(puuid) FROM wp_match 
                where matchId in 
                (SELECT matchId FROM wp_match where matchTime > $startTime and matchTime <= $endTime and puuid='$user->puuid')
                and puuid != '$user->puuid' and puuid in (SELECT puuid FROM wp_user where status='Y')";
        
    $result = mysqli_query($mysqli, $sql_select);
    $sleepArray = array();
    while($row = mysqli_fetch_array($result)) {
        $sleepArray[] = $row['puuid'];
    }
    $element['sleepArray'] = $sleepArray;
    $element['total'] = count($sleepArray);
    $array[] = $element;
}

$array = array_key_init(array_sort($array, 'total', SORT_DESC));
$jsonObj["startDate"] = $startDate;
$jsonObj["endDate"] = $endDate;
$jsonObj["array"] = $array;

mysqli_close($mysqli);

echo json_encode($jsonObj);
?>