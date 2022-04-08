<?php
include("../dbConnection.php");

$sql_select="SELECT * FROM wp_match where 1 order by matchTime desc LIMIT 0,1";
$result = mysqli_query($mysqli, $sql_select);
?>

<div class="container" style="display: flex; flex:1; flex-direction:column; justify-content: center; align-items: center; margin:40px; margin-bottom:10px;">
    <h1 style="display:flex;">
    <?php
            
    if(mysqli_num_rows($result) == 0) {
        echo "업데이트 기록이 없습니다.";
    ?>
    </h1>
    <button id="update">업데이트 하기</button>
    <?php
    } else {
        $rows = mysqli_fetch_array($result);
        $lastMatchTime = $rows["matchTime"];
        echo "마지막 업데이트 : ".date("Y-m-d H:i:s", $lastMatchTime);
    ?>
    </h1>
    <?php
        if($lastMatchTime != strtotime(date("Y-m-d", strtotime("-1 day"))." 23:59:59")) {
    ?>
    <button id="update">업데이트 하기</button>
    <?php
        } else {      
    ?>
    <button id="none">최신 상태입니다</button>
    <?php
        }
    }
    ?>
</div>
<?php
mysqli_close($mysqli);
?>