<?php
include("../dbConnection.php");
include("../apiKey.php");

$startTime = $_GET["startTime"];
$endTime = $_GET["endTime"];
$until = $_GET["until"];
$token = $_GET["token"];
$puuid = $_GET["puuid"];
$userSeq = $_GET["userSeq"];
$userTotal = $_GET["userTotal"];

$term = 1; // 1 request마다 몇 일의 데이터를 가져오는지 설정 

function getToken($var1, $var2) {
    return "token-".($var1 + $var2 * 2 + 1239900024) / 2;
}

if($startTime == "" && $endTime == "" && $token == "") {
    
    // 가장 최근 업데이트 날짜를 가져온다.
    $sql_select="SELECT matchTime FROM wp_match where 1 order by matchTime desc LIMIT 1";
    
    $result = mysqli_query($mysqli, $sql_select);
    $startTime = strtotime("2021-12-01 00:00:00");

    if(mysqli_num_rows($result) == 0) {
        //업데이트 기록이 없는 경우, 2021년 12월 1일 부터 시작한다.
    } else {
        //업데이트 기록이 있는 경우, 마지막 endTime + 1 초부터 시작한다.
        while($rows = mysqli_fetch_array($result))
            $startTime = $rows["matchTime"] + 1;
    }
    //until(yesterday) = 어제 날짜
    $yesterday = strtotime(date("Y-m-d", strtotime("-1 day"))." 23:59:59");
    //endTime = 시작 날짜로부터 $term일 간
    $endTime = $startTime + 3600 * 24 * $term - 1;

    //시작시간이 어제 이후라면 종료
    if($startTime > $yesterday) exit;

    /*
    //탐색 구간이 어제 이후라면 어제까지를 탐색 구간으로 설정 
    if($endTime > $yesterday) {
        $endTime = $yesterday; 
    }

    //탐색 시작시간과 끝시간의 달 수를 가져옴
    $startMonth = date('m', $startTime);
    $endMonth = date('m', $endTime);
    
    //시작시간과 끝시간의 달이 다르다면 끝시간을 시작시간 달의 가장 마지막 날로 설정
    if($startMonth != $endMonth) {
        $endTime = strtotime(date('Y', $startTime)."-".$startMonth."-".date('t', $startTime)." 23:59:59");
    }
    */

    $array = array("status"=>"start", "startTime"=>$startTime, "endTime"=>$endTime, "until"=>$yesterday, "token"=>getToken($startTime, $endTime));

    echo json_encode($array);
} else {
    //토큰 값이 없다면 종료
    if($token != getToken($startTime, $endTime)) {
        exit;
    }
    //탐색 구간의 시작 시간과 끝 시간, until 시간, puuid를 받아온 상태.

    //시작시간이 until 이후라면 업데이트 완료
    if($startTime > $until) {
        $array = array("status"=>"complete");
        echo json_encode($array);
        exit;
    }

    /*
    //탐색 구간이 until 이후라면 until까지를 탐색 구간으로 설정 
    if($endTime > $until) {
        $endTime = $until; 
    }

    //탐색 시작시간과 끝시간의 달 수를 가져옴
    $startMonth = date('m', $startTime);
    $endMonth = date('m', $endTime);
    
    //시작시간과 끝시간의 달이 다르다면 끝시간을 시작시간 달의 가장 마지막 날로 설정
    if($startMonth != $endMonth) {
        $endTime = strtotime(date('Y', $startTime)."-".$startMonth."-".date('t', $startTime)." 23:59:59");
    }
    */

    //매치 데이터 가져오기
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36",
        "Accept-Language: ko-KR,ko;q=0.9,en-US;q=0.8,en;q=0.7",
        "Accept-Charset: application/x-www-form-urlencoded; charset=UTF-8",
        "Origin: http://whippingcream.iptime.org:8081/admin/",
        "X-Riot-Token:".$apiKey
    ));
    curl_setopt($ch, CURLOPT_URL,
    "https://asia.api.riotgames.com/lol/match/v5/matches/by-puuid/".$puuid."/ids?startTime=".$startTime."&endTime=".$endTime."&start=0&count=100&api_key=".$apiKey); // 호출할 주소
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
        $array = array("status"=>"응답 코드 :".$http_code."\n".$server_output);
        echo json_encode($array);
        exit;
    }

    $object = json_decode($server_output);

    //매치 데이터 저장하기
    foreach($object as $matchId) {
        $sql_insert="INSERT INTO wp_match (puuid, matchId, matchTime) VALUES ('$puuid','$matchId','$endTime')";
        mysqli_query($mysqli, $sql_insert);
    }

    if($userSeq + 1 == $userTotal) {
        //startTime, endTime, userSeq 재설정
        $startTime = $endTime + 1;
        $endTime = $startTime + 3600 * 24 * $term - 1;
        $userSeq = 0;
    } else {
        //userSeq 올리기
        $userSeq += 1;
    }

    $array = array("status"=>"updating", "startTime"=>$startTime, "endTime"=>$endTime, "until"=>$until, "userSeq"=>$userSeq, "token"=>getToken($startTime, $endTime));

    echo json_encode($array);
}
mysqli_close($mysqli);
?>