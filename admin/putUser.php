<?php
include("../dbConnection.php");
include("../apiKey.php");

$name = urldecode($_GET["name"]);

if(trim($name) == "") {
    echo "닉네임을 다시 입력해주세요.";
    exit;
}

$sql_select="SELECT * FROM wp_user where name = '$name'";

$result = mysqli_query($mysqli, $sql_select);

if(mysqli_num_rows($result) > 0) {
    echo "동일한 닉네임이 존재합니다.";
    mysqli_close($mysqli);
    exit;
}

$ch = curl_init();

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36",
    "Accept-Language: ko-KR,ko;q=0.9,en-US;q=0.8,en;q=0.7",
    "Accept-Charset: application/x-www-form-urlencoded; charset=UTF-8",
    "Origin: http://whippingcream.iptime.org:8081/admin/",
    "X-Riot-Token:".$apiKey
));
curl_setopt($ch, CURLOPT_URL,
"https://kr.api.riotgames.com/lol/summoner/v4/summoners/by-name/".urlencode($name)."?api_key=".$apiKey); // 호출할 주소
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);

//$post = array('username' => 'testuser', 'password' => '1234567'); // post로 요청할 데이터
//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

$server_output = curl_exec($ch); // http request 수행

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if($http_code != 200) {
    echo "응답 코드 :".$http_code."\n".$server_output;
    exit;
}

$object = json_decode($server_output);



$sql_select="SELECT * FROM wp_nickname where puuid = '$object->puuid' order by nidx desc LIMIT 0, 1";

$result = mysqli_query($mysqli, $sql_select);

if(mysqli_num_rows($result) > 0) {
    $nicknameRes = mysqli_fetch_array($result);
    $nickName = $nicknameRes["nickname"];
    echo "동일한 유저가 이미 존재합니다. [$nickName]";
    mysqli_close($mysqli);
    exit;
}


$sql_insert_user="INSERT INTO wp_user (id, accountId, puuid, profileIconId, revisionDate, summonerLevel)
VALUES
('$object->id','$object->accountId','$object->puuid', 
 $object->profileIconId, '$object->revisionDate', $object->summonerLevel)";
 
$sql_insert_nickname="INSERT INTO wp_nickname (puuid, nickname)
VALUES
('$object->puuid', '$object->name')";

if (!mysqli_query($mysqli,$sql_insert_user) || !mysqli_query($mysqli,$sql_insert_nickname)) {
    die('Error: ' . mysqli_error($mysqli));
} else {
    echo "success";
}

mysqli_close($mysqli);
?>