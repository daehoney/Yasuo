$(function(){
    var flag = false; // 로딩중 메뉴 이동 금지 플래그 (로딩 중 이동 시 오류 발생 가능성 있음)
    var userList= new Array(); // 유저 목록 데이터
    var userHash = new Array(); // puuid로 name을 가져올 수 있는 테이블(key:value -> puuid:name)
    var pickerStart;
    var pickerEnd;

    function getStartDate() {
        if(pickerStart == "undefinded" || pickerStart == null) {
            return "";
        }
        return pickerStart.toString('YYYY-MM-DD');
    }

    function getEndDate() {
        if(pickerEnd == "undefinded" || pickerEnd == null) {
            return "";
        }
        return pickerEnd.toString('YYYY-MM-DD');
    }

    //유닉스 시간 -> string 변환 함수
    function Unix_timestamp(t){
        var date = new Date(t*1000);
        var year = date.getFullYear();
        var month = "0" + (date.getMonth()+1);
        var day = "0" + date.getDate();
        var hour = "0" + date.getHours();
        var minute = "0" + date.getMinutes();
        var second = "0" + date.getSeconds();
        return year + "-" + month.substr(-2) + "-" + day.substr(-2) + " " + hour.substr(-2) + ":" + minute.substr(-2) + ":" + second.substr(-2);
    }

    function showLoadingUI(message) {
        $('.main').html("<div class='container' style='display:flex; flex:1'><div class='loading-container'><div class='loading'></div><div id='loading-text'>"+ message + "</div></div></div>");
    }

    $.fn.changeStatus = function(index) {
        $.ajax({
            url:'./putStatus.php',
            type:'get',
            data: {
                puuid : userList[index].puuid
            },
            success:function(data){
                if(data == "Y" || data == "N" || data == "B") {
                    getUserList();
                } else alert(data);
            }
        });
    }

    //유저 목록 조회 함수
    function getUserList() {
        $.ajax({
            url:'./getUser.php',
            type:'get',
            success:function(data){
                var result = JSON.parse(data);
                var content = result.content;
                userList = result.userList;
                var totalUser = "휘핑유저: " + userList.length + "명";

                for(var i=0; i<userList.length; i++) {
                    userHash[userList[i].puuid] = userList[i].name;
                }

                $('.leaderboardWrapper').html(content);
                $('#total').html(totalUser);
                flag = true;
            }
        });
    }

    //신규 유저 입력 함수
    function putUser() {
        if($('#newName').val().trim() == "") {
            alert("닉네임을 다시 입력해주세요.");
            return;
        }

        $.ajax({
            url:'./putUser.php',
            type:'get',
            data: {
                name: $('#newName').val()
            },
            success:function(data){
                if(data == "success") {
                    alert("유저 등록 성공");
                    getUserList();
                } else alert(data);
            }
        });
    }

    //닉네임 변경 함수
    function putNickname() {
        if($('#beforeName').val().trim() == "" || $('#afterName').val().trim() == "") {
            alert("닉네임을 다시 입력해주세요.");
            return;
        }

        $.ajax({
            url:'./putNickname.php',
            type:'get',
            data: {
                name1: $('#beforeName').val(),
                name2: $('#afterName').val()
            },
            success:function(data){
                if(data == "success") {
                    alert("닉네임 변경 성공");
                    getUserList();
                } else alert(data);
            }
        });
    }

    //유저 관리 메뉴
    $(document).on("click","#menu1",function(){
        if(flag == false) return;
        flag = false;
        $('.main').html("");
        $.ajax({
            url:'./user.php',
            type:'get',
            success:function(data){
                $('.main').html(data);
                getUserList();
            }
        });
    });

    //업데이트 메뉴
    $(document).on("click","#menu2",function(){
        if(flag == false) return;
        flag = false;
        showLoadingUI("업데이트 기록 찾는 중");
        $.ajax({
            url:'./update.php',
            type:'get',
            success:function(data){
                flag = true;
                $('.main').html(data);
            }
        });
    });

    //휴면 유저 찾기 화면 테이블 생성
    function createSleepTable(seq, name, total, listStr, startDate, endDate) {
        if(seq == 0) {
            $('.main').html("<div id='sub' style='display:flex; flex:1; flex-direction:column; justify-content: center; align-items: center;'></div>");
            $('#sub').html("<div id='input' style='display:flex; flex-direction:row; justify-content: center; align-items: center;'>'<input id='datepicker1' class='date'> <font color='white' size='20px'>~</font> <input id='datepicker2' class='date'> <button id='find' style='margin-right:20px;'>해당 기간 찾기</button></div>");
            $('#sub').append("<div class='scrollbar sleepUserWrapper' id='style-6'style='display:flex; align-items: center;'><div class='table' id='table' style='margin:auto;'></div></div>");
            $('.table').append("<div class='table-row'>");
            $('.table').append("<div class='table-cell-center' style='width:200px;'>닉네임</div>");
            $('.table').append("<div class='table-cell-center'>같이 게임한 휘핑러 수</div>");
            $('.table').append("<div class='table-cell-center'>같이 게임한 휘핑러 목록</div></div>");
            pickerStart = new Pikaday({ 
                field: document.getElementById('datepicker1'),
                format: 'yyyy-MM-dd',
                toString(date, format) {
                    let day = ("0" + date.getDate()).slice(-2);
                    let month = ("0" + (date.getMonth() + 1)).slice(-2);
                    let year = date.getFullYear();
                    return `${year}-${month}-${day}`;
                }
            });
            pickerEnd = new Pikaday({ 
                field: document.getElementById('datepicker2'),
                format: 'yyyy-MM-dd',
                toString(date, format) {
                    let day = ("0" + date.getDate()).slice(-2);
                    let month = ("0" + (date.getMonth() + 1)).slice(-2);
                    let year = date.getFullYear();
                    return `${year}-${month}-${day}`;
                }
            });

            pickerStart.setDate(startDate);
            pickerEnd.setDate(endDate);
        }
        $('.table').append("<div class='table-row'>");
        $('.table').append("<div class='table-cell-center'>"+name+"</div>");
        $('.table').append("<div class='table-cell-center' id='total"+seq+"'>"+total+"</div></div>");
        $('.table').append("<div class='table-cell' id='arr"+seq+"'>"+listStr+"</div></div>");
    }

    //휴면 유저 출력(매치 데이터 기반 함께 플레이한 소환사 출력)
    function findSleep() {
        $.ajax({
            url:'./sleep.php',
            type:'post',
            data: {
                userList: JSON.stringify(userList),
                startDate: getStartDate(),
                endDate: getEndDate()
            },
            success:function(data){
                var sleepObj = JSON.parse(data);
                var startDate = sleepObj.startDate;
                var endDate = sleepObj.endDate;
                var array = sleepObj.array;
                for(var j = 0; j<array.length; j++) {
                    var sleepArray = array[j].sleepArray;
                    var sleepArrayStr = "";
                    var sleepArrayForSort = new Array();
                    for(var i = 0; i<sleepArray.length; i++) {
                        sleepArrayForSort.push(userHash[sleepArray[i]]);
                    }
                    sleepArrayForSort.sort();
                    for(var i = 0; i<sleepArrayForSort.length; i++) {
                        sleepArrayStr += sleepArrayForSort[i];
                        if(i + 1 < sleepArrayForSort.length) {
                            sleepArrayStr += ", ";
                        }
                    }
                    createSleepTable(j, userHash[array[j].puuid], array[j].total, sleepArrayStr, startDate, endDate);
                }
                flag = true;
            }
        });
    }

    //지각자 리스트 출력
    function printLater() {
        $.ajax({
            url:'./later.php',
            type:'get',
            data: {
                year : new Date().getFullYear(),
                month : new Date().getMonth() + 1
            },
            success:function(data){
                flag = true;
                $('.main').html("<div class='scrollbar sleepUserWrapper' id='style-6'style='display:flex; align-items: center;'><div class='table' id='table' style='margin:auto;'></div></div>");
                $('.table').append("<div class='table-row'>");
                $('.table').append("<div class='table-cell-center' style='width:200px;'>닉네임</div>");
                $('.table').append("<div class='table-cell-center'>잔여 지각 점수</div>");
                
                var laterObj = JSON.parse(data);

                for(var i = 0; i < laterObj.length; i++) {
                    $('.table').append("<div class='table-row'>");
                    $('.table').append("<div class='table-cell-center'>"+laterObj[i].name+"</div>");
                    $('.table').append("<div class='table-cell-center'>"+laterObj[i].point+"</div></div>");
                }
            }
        });
    }

    //휴면 유저 찾기 메뉴
    $(document).on("click","#menu3",function(){
        if(flag == false) return;
        flag = false;
        showLoadingUI("매치 데이터 분석중");
        findSleep();
    });

    //휴면 유저 찾기 메뉴
    $(document).on("click","#menu4",function(){
        if(flag == false) return;
        flag = false;
        showLoadingUI("지각자 리스트 생성중");
        printLater();
    });

    //riot API 에서 매치 데이터 불러오기
    function update(startTime, endTime, until, token, puuid, userSeq) {
        showLoadingUI(Unix_timestamp(startTime)+"~"+Unix_timestamp(endTime)+"<br>"+userList[userSeq].name+" 매치 기록 받는 중");
        setTimeout(function() {
            $.ajax({
                url:'./getMatch.php',
                type:'get',
                data: {
                    "startTime" : startTime,
                    "endTime" : endTime,
                    "until" : until,
                    "token" : token,
                    "puuid" : puuid,
                    "userSeq" : userSeq,
                    "userTotal" : userList.length
                },
                success:function(data){
                    var res = JSON.parse(data);
                    switch(res.status) {
                        case "updating":
                                update(res.startTime, res.endTime, res.until, res.token, userList[res.userSeq].puuid, res.userSeq);
                                break;
                        case "complete":
                            showLoadingUI("업데이트 기록 찾는 중");
                            $.ajax({
                                url:'./update.php',
                                type:'get',
                                success:function(data){
                                    flag = true;
                                    $('.main').html(data);
                                }
                            });
                            break;
                        default :
                            flag = true;
                            alert(res.status);
                    }
                }
            });
        }, 2000);
    }

    //업데이트 버튼 클릭 이벤트
    $(document).on("click","#update",function(){
        if(flag == false) return;
        flag = false;
        showLoadingUI("유저 목록 불러오는 중");
        $.ajax({
            url:'./getMatch.php',
            type:'get',
            success:function(data){
                var res = JSON.parse(data);
                update(res.startTime, res.endTime, res.until, res.token, userList[0].puuid, 0);
            }
        });
    });

    //신규 유저 등록 버튼 클릭 이벤트
    $(document).on("click","#register",function(){
        putUser();
    });

    //닉네임 변경 버튼 클릭 이벤트
    $(document).on("click","#change",function(){
        putNickname();
    });

    //기간 내 휴면 유저 찾기 버튼
    $(document).on("click","#find",function(){
        showLoadingUI("매치 데이터 분석중");
        findSleep();
    });

    //최초 화면 진입 시 유저 목록 호출
    getUserList();
});