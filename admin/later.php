<?php
include("../dbConnection.php");

$year = $_GET['year'];
$month = $_GET['month'];

$thisMonthStart = "$year-$month-1";
$thisMonthEnd = date("Y-m-d", strtotime("$thisMonthStart +1 month"));
$lastMonthStart = date("Y-m-d", strtotime("$thisMonthStart -1 month"));
$lastMonthEnd = $thisMonthStart;
$beforeMonthStart = date("Y-m-d", strtotime("$thisMonthStart -2 month"));
$beforeMonthEnd = $lastMonthStart;

$laterList = array();


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

function getLaterList($mysqli, $array, $start, $end, $mode) {

    $bonusArray = array();
    $bonus = 0;
    
    switch($mode) {
        case 0 :
            $bonus = 0;
            break;
        case 1 :
            $bonus = 10;
            break;
        case 2 :
            $bonus = 20;
            break;
    }

    $select_later = "SELECT 
    (SELECT nickname from wp_nickname WHERE puuid = a.l_puuid ORDER BY nidx DESC LIMIT 1) as nickname, 
    IFNULL((SELECT COUNT(*) from wp_late WHERE l_puuid = a.l_puuid AND r_puuid = a.l_puuid 
    AND date >= '$start' AND date < '$end' GROUP BY l_puuid, r_puuid, date ORDER BY date DESC), 0) * 2 as reportPoint,
    IFNULL((SELECT COUNT(*) from wp_late WHERE r_puuid = a.l_puuid AND l_puuid != a.l_puuid 
    AND date >= '$start' AND date < '$end'), 0) as reportOther,
    MAX(point) as point, date FROM wp_late a WHERE 
    date >= '$start' AND date < '$end' GROUP BY l_puuid, date ORDER BY date desc";

    $result = mysqli_query($mysqli, $select_later);

    while($rows = mysqli_fetch_array($result)) {
        if(!array_key_exists($rows['nickname'], $bonusArray)) {
            $bonusArray[$rows['nickname']] = $bonus;
            $bonusArray[$rows['nickname']] += $rows['reportOther']; // 다른 사람 리폿한건 최초 한번 적용(기간 내 합한 값임)
        }
        $bonusArray[$rows['nickname']] -= $rows['point'] - $rows['reportPoint']; // 자진 신고는 매번 적용
    }

    foreach($bonusArray as $nickname => $point) {
        if(array_key_exists("$nickname", $array)) {
            if($point < 0) $array[$nickname] -= $point;
        } else {
            if($point < 0) $array[$nickname] = -$point;
        }
    }
    mysqli_close($mysqli);

    return $array;
}

$laterList = getLaterList($mysqli, $laterList, $thisMonthStart, $thisMonthEnd, 0);
$laterList = getLaterList($mysqli, $laterList, $lastMonthStart, $lastMonthEnd, 1);
$laterList = getLaterList($mysqli, $laterList, $beforeMonthStart, $beforeMonthEnd, 2);

$jsonArray = array();

foreach($laterList as $key => $value) {
    $later = array();
    $later['name'] = $key;
    $later['point'] = $value;
    $jsonArray[] = $later;
}

$jsonArray = array_key_init(array_sort($jsonArray, 'point', SORT_DESC));

echo json_encode($jsonArray);

?>