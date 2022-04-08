$(function() {

    var userList= new Array();
    var picker;

    $("#content").css("display", "none");

    getUserList();

    $(document).on("click","#submit",function(){
      $("#submit").attr("disabled", true);
      $("#submit").css("background-color", "gray");
      $("#submit").html("제출중");
      var point = $('input[name="radio"]:checked').val();
      var laterName = $("#later").val();
      var laterPuuid = $("#later_puuid").val();
      var reporterName = $("#reporter").val();
      var reporterPuuid = $("#reporter_puuid").val();
      var lateDate = picker.toString('YYYY-MM-DD');
      if(laterPuuid == undefined || laterPuuid == "" || reporterPuuid == undefined || reporterPuuid == "") {
        alert("지각자와 제보자를 입력해주세요.\n[닉네임 입력 후 목록에서 선택]");
        $("#submit").attr("disabled", false);
        $("#submit").css("background-color", "pink");
        $("#submit").html("제출하기");
        return;
      } else if(point == undefined || point == "") {
        alert("얼마나 늦었는지 선택해주세요.");
        $("#submit").attr("disabled", false);
        $("#submit").css("background-color", "pink");
        $("#submit").html("제출하기");
        return;
      }
      putLate(laterPuuid, reporterPuuid, point, lateDate);
    });

    $(document).on("click","#later_redo",function(){
      $("#later_res").css("display", "none");
      $("#later").css("display", "inline-block");
      $("#later").val("");
      $("#later_name").html("");
      $("#later_puuid").val("");
    });

    $(document).on("click","#reporter_redo",function(){
      $("#reporter_res").css("display", "none");
      $("#reporter").css("display", "inline-block");
      $("#reporter").val("");
      $("#reporter_name").html("");
      $("#reporter_puuid").val("");
    });

    //지각 제보 등록 함수
    function putLate(l_puuid, r_puuid, point, date) {
        $.ajax({
            url:'./putLate.php',
            type:'get',
            data: {
              l_puuid : l_puuid,
              r_puuid : r_puuid,
              point : point,
              date : date
            },
            success:function(data){
              if(data == "success") {
                alert("지각 제보 완료");
              } else {
                alert(data);
              }
              $("#submit").attr("disabled", false);
              $("#submit").css("background-color", "pink");
              $("#submit").html("제출하기");
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
                userList = result.userList;

                $("#subtitle").html("");
                $("#content").css("display", "block");
 
                $( "#later" ).autocomplete({
                  minLength: 0,
                  source: userList,
                  focus: function( event, ui ) {
                    return false;
                  },
                  select: function( event, ui ) {
                    $("#later").val( ui.item.value );
                    $("#later_name").html(ui.item.value);
                    $("#later_puuid").val(ui.item.puuid);
                    $("#later").css("display", "none");
                    $("#later_res").css("display", "inline-block");
                    return false;
                  }
                })
                .autocomplete( "instance" )._renderItem = function( ul, item ) {
                  return $( "<li>" )
                    .append( "<div><b><font style='font-size:large;'>" + item.value + "</font></b><br><font style='font-size:small;'>" + item.desc + "</font></div>" )
                    .appendTo( ul );
                };
                
                $( "#reporter" ).autocomplete({
                  minLength: 0,
                  source: userList,
                  focus: function( event, ui ) {
                    return false;
                  },
                  select: function( event, ui ) {
                    $("#reporter").val( ui.item.value );
                    $("#reporter_name").html(ui.item.value);
                    $("#reporter_puuid").val(ui.item.puuid);
                    $("#reporter").css("display", "none");
                    $("#reporter_res").css("display", "inline-block");
                    return false;
                  }
                })
                .autocomplete( "instance" )._renderItem = function( ul, item ) {
                  return $( "<li>" )
                    .append( "<div><b><font style='font-size:large;'>" + item.value + "</font></b><br><font style='font-size:small;'>" + item.desc + "</font></div>" )
                    .appendTo( ul );
                };
                
                picker = new Pikaday({ 
                field: document.getElementById('datepicker'),
                format: 'yyyy-MM-dd',
                keyboardInput : false,
                minDate: new Date(Date.now() - 864e5 * 2),
                maxDate: new Date(),
                toString(date, format) {
                    let day = ("0" + date.getDate()).slice(-2);
                    let month = ("0" + (date.getMonth() + 1)).slice(-2);
                    let year = date.getFullYear();
                    return `${year}-${month}-${day}`;
                }
                });
                picker.setDate(new Date());
            }
        });
    }
  });