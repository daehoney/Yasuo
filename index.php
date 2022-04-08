<!DOCTYPE html>
<html lang="ko">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>지각 신고</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.6.1/css/pikaday.css" />
<link rel="stylesheet" href="/late.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.6.1/pikaday.js"></script>
<script src="https://kit.fontawesome.com/986d2b827f.js" crossorigin="anonymous"></script>
<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
  <script src="/late.js"></script>
</head>

<body>
<h1 id="title" style="margin:auto 0; text-align:center;">지각 제보</h1>
<h4 style="margin:auto 0; text-align:center;">지각 관련 문의는 [잠탱이다]에게 부탁드립니다.</h1>
<h4 style="margin:auto 0; text-align:center;">지각은 [게임종류 상관없이] 제보해주세요.</h1>
<hr><br>
<h4 style="margin:auto 0; text-align:center;">***닉네임 입력 후 목록에서 선택***</h1>
<br>
<br>
<h4 id="subtitle" style="margin:auto 0; text-align:center;">닉네임 목록 로딩중</h1>
<div id="content" style="margin:auto 0; text-align:center;">

<div id="later-label" style="margin:auto 0; text-align:center;">지각자:</div>
<input id="later" class="ui-autocomplete-input" autocomplete="off" style="margin:auto 0; text-align:center;" /><br>
<div id="later_res" style="display: none !important;">
<i id="later_redo" class="fa-solid fa-circle-left fa-2x" style="display: inline-block;"></i>
<div id="later_name" style="color: red; font-size:xx-large; padding:7.5px; display:inline-block;"></div>
<input id="later_puuid" type="hidden"/>
</div>
<br><br>
<div id="reporter-label" style="margin:auto 0; text-align:center;">제보자:</div>
<input id="reporter" class="ui-autocomplete-input" autocomplete="off" style="margin:auto 0; text-align:center;" /><br>
<div id="reporter_res" style="display: none !important;">
<i id="reporter_redo" class="fa-solid fa-circle-left fa-2x" style="display: inline-block;"></i>
<div id="reporter_name" style="color: green; font-size:xx-large; padding:7.5px; display:inline-block;"></div>
<input id="reporter_puuid" type="hidden"/>
</div>
<br><br>
<div id="reporter-label" style="margin:auto 0; text-align:center;">지각 일자:(새벽에 한 게임은 하루 전 날짜)</div>
<input type="text" id='datepicker' readonly>
<br><br><br>
<div class="wrap">
  <input type="radio" name="radio" id="radio0" class="checkbox" value="5">
  <label for="radio0" class="input-label radio">5분 이내</label>
  <input type="radio" name="radio" id="radio1" class="checkbox" value="10">
  <label for="radio1" class="input-label radio">10분 이내</label>
  <input type="radio" name="radio" id="radio2" class="checkbox" value="15">
  <label for="radio2" class="input-label radio">10분 이상(결석)</label>  
</div>
<br>
↑
<br>
*대타 구해졌을 경우, 대타 구해진 기준*
<br><br><br>
<button id="submit" style="background-color:pink; color:black;">제출하기</button>
<br><br><br><br><br><br><br><br><br>
</div>
</body>

</html>