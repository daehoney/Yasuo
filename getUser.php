<?php
include("./dbConnection.php");

$sql_select="SELECT GROUP_CONCAT(nickname) AS history, 
puuid, (SELECT nickname FROM wp_nickname d WHERE puuid = c.puuid ORDER BY nidx DESC LIMIT 1) AS name,
IFNULL((SELECT GROUP_CONCAT(nickname ORDER BY nidx DESC) 
    FROM wp_nickname a WHERE 
    nidx != (SELECT MAX(nidx) as nidx FROM wp_nickname b 
            where puuid = a.puuid GROUP BY puuid ORDER BY MAX(nidx) DESC) 
            AND puuid = c.puuid GROUP BY puuid ORDER BY MAX(nidx) DESC), '-') AS past 
FROM wp_nickname c GROUP BY puuid ORDER BY 
(CASE WHEN ASCII(SUBSTRING(name,1)) BETWEEN 48 AND 57 THEN 3
WHEN ASCII(SUBSTRING(name,1)) < 128 THEN 2 ELSE 1 END), binary(name)";

$result = mysqli_query($mysqli, $sql_select);

$userList = array();

while($row = mysqli_fetch_array($result)) {
    $data = array();
    $data["puuid"] = $row['puuid'];
    $data["label"] = $row['history'];
    $data["value"] = $row['name'];
    $data["desc"] = $row['past'];
    $userList[] = $data;
}

$jsonString = array("userList" => $userList);

echo json_encode($jsonString);

mysqli_close($mysqli);
?>