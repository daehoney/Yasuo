<?php
include("./dbConnection.php");
include("./apiKey.php");

function getPuuid($apiKey, $name) {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36",
        "Accept-Language: ko-KR,ko;q=0.9,en-US;q=0.8,en;q=0.7",
        "Accept-Charset: application/x-www-form-urlencoded; charset=UTF-8",
        "X-Riot-Token:".$apiKey
    ));
    curl_setopt($ch, CURLOPT_URL,
    "https://kr.api.riotgames.com/lol/summoner/v4/summoners/by-name/".urlencode($name)."?api_key=".$apiKey); // 호출할 주소
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);

    $server_output = curl_exec($ch); // http request 수행

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if($http_code != 200) {
        echo "응답 코드 :".$http_code."<br>".$server_output."<br>";
    }

    $object = json_decode($server_output);

    return $object->puuid;
}

function getName($pastPuuid) {
    $host = 'localhost';
    $user = 'root';
    $pw = 'a1122qq';
    $dbName = 'whippingsleeping';
    $mysqli = new mysqli($host, $user, $pw, $dbName);
    $sql="SELECT nickname FROM wp_nickname WHERE puuid = '$pastPuuid' ORDER BY nidx DESC LIMIT 1";
    $result = mysqli_query($mysqli, $sql);
    $row = mysqli_fetch_array($result);
    mysqli_close($mysqli);
    return $row['nickname'];
}

function update($pastPuuid, $newPuuid) {
    $host = 'localhost';
    $user = 'root';
    $pw = 'a1122qq';
    $dbName = 'whippingsleeping';
    $mysqli = new mysqli($host, $user, $pw, $dbName);
    $sql1="UPDATE wp_nickname SET puuid = '$newPuuid' WHERE puuid = '$pastPuuid'";
    $sql2="UPDATE wp_user SET puuid = '$newPuuid' WHERE puuid = '$pastPuuid'";
    $sql3="UPDATE wp_late SET l_puuid = '$newPuuid' WHERE l_puuid = '$pastPuuid'";
    $sql4="UPDATE wp_late SET r_puuid = '$newPuuid' WHERE r_puuid = '$pastPuuid'";
    $sql5="UPDATE wp_match SET puuid = '$newPuuid' WHERE puuid = '$pastPuuid'";
    $result = mysqli_query($mysqli, $sql1);
    $result = mysqli_query($mysqli, $sql2);
    $result = mysqli_query($mysqli, $sql3);
    $result = mysqli_query($mysqli, $sql4);
    $result = mysqli_query($mysqli, $sql5);
    mysqli_close($mysqli);
}

$sql_select="SELECT * from wp_user where 1";

$result = mysqli_query($mysqli, $sql_select);

$userList = array();

while($row = mysqli_fetch_array($result)) {
    $pastPuuid = $row['puuid'];
    $name = getName($pastPuuid);
    $newPuuid = getPuuid($apiKey, $name);
    if($newPuuid == $pastPuuid) {
        echo $name." success";
    } else {
        //update($pastPuuid, $newPuuid);
        echo $name." update [".$pastPuuid."] [".$newPuuid."]<br>";
    }
}

mysqli_close($mysqli);
?>