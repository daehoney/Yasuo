<?php
include("../dbConnection.php");

$sql_select="SELECT puuid, profileIconId, summonerLevel, ( 
    SELECT nickname FROM wp_nickname n where n.puuid = u.puuid order by nidx desc LIMIT 0, 1 
 ) as nickname, status FROM wp_user u where 1 ORDER BY 
(CASE WHEN ASCII(SUBSTRING(nickname,1)) BETWEEN 48 AND 57 THEN 3
WHEN ASCII(SUBSTRING(nickname,1)) < 128 THEN 2 ELSE 1 END), binary(nickname)";

$result = mysqli_query($mysqli, $sql_select);

$content =  "<article class='leaderboard'>
<main class='leaderboard__profiles'>
";

$userList = array();

$index = 0;

while($row = mysqli_fetch_array($result)) {
    $data = array();
    $data["puuid"] = $row['puuid'];
    $data["name"] = $row['nickname'];
    $data["status"] = $row['status'];
    $userList[] = $data;

    $bgColor = "white";

    if($row['status'] == "Y") {
        $bgColor = "white";
    } else if($row['status'] == "N") {
        $bgColor = "gray";
    } else if($row['status'] == "B") {
        $bgColor = "red";
    }

    $content .= "<article class='leaderboard__profile' style='background-color:".$bgColor.";' onClick='$(this).changeStatus($index)'>
    <img src='http://ddragon.leagueoflegends.com/cdn/11.24.1/img/profileicon/".$row['profileIconId'].".png' class='leaderboard__picture'>
    <span class='leaderboard__name'>".$row['nickname']."</span>
    <span class='leaderboard__value'>Lv.".$row['summonerLevel']."</span>
    </article>";

    $index++;
}

$content .= "</main>
</article>";

$jsonString = array("userList" => $userList, "content" => $content);

echo json_encode($jsonString);

mysqli_close($mysqli);
?>