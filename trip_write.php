<?php session_start(); 
if(empty($_SESSION['login_id'])){
    // echo "<script>alert('잘못된 접근입니다.');</script>";
    echo "<script>window.location.replace('index.php');</script>";
}else{
    echo "<script>
    var before_page = document.referrer;
    var before_page_arr = before_page.split('/');
    console.log(before_page_arr[4]);
    if(before_page_arr[4]!='update.php'){
        var name = 'temp_img_upload';
        var date = new Date();
        date.setDate(date.getDate()-1);

        var willCookie ='';
        willCookie += name + '=remove;';
        willCookie +='expires='+date.toUTCString();

        document.cookie = willCookie;
        console.log('temp_img_upload 쿠키 삭제');
    }
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header 삽입 -->
    <?php include_once("header.php");?>

    <!-- 썸머노트 에디터 -->
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script>
    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">
    <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>


    <!-- datepicker -->
    <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.8.18/themes/base/jquery-ui.css" /> -->
    <!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script> -->
    <!-- <script src="//code.jquery.com/ui/1.8.18/jquery-ui.min.js"></script> -->
</head>

<script>
function back() {
    history.back();
}

function tripWrite() {
    // 여행 유형 검사
    var trip_type = document.getElementById('trip_type').value;
    if (trip_type != 1 && trip_type != 2) {
        alert('여행 유형을 선택해주세요');
        document.getElementById('trip_type').focus();
        return false;
    }

    // 제목 검사
    var trip_title = document.getElementById('trip_title').value;
    if (trip_title.length == 0) {
        alert('제목을 입력해주세요');
        document.getElementById('trip_title').focus();
        return false;
    }

    // 여행 일정 검사
    var trip_day_check = document.getElementById('trip_day_check').value;
    if (trip_day_check == 0) {
        alert('여행 일정을 선택해주세요');
        document.getElementById('trip_start_day').focus();
        return false;
    }

    var trip_start_day = document.getElementById('trip_start_day').value;
    var trip_finish_day = document.getElementById('trip_finish_day').value;

    console.log('trip_start_day: ' + trip_start_day);
    console.log('trip_finish_day: ' + trip_finish_day);

    if (trip_start_day == "") {
        alert('시작일을 선택해주세요');
        document.getElementById('trip_start_day').focus();
        return false;
    }
    if (trip_finish_day == "") {
        alert('마지막일을 선택해주세요');
        document.getElementById('trip_finish_day').focus();
        return false;
    }

    // 장소 검사 
    var trip_location = document.getElementById('trip_location').value;
    if(trip_location==""){
        alert('장소를 등록해주세요');
        document.getElementById('trip_finish_day').focus();
        return false;
    }

    // 경로 검사
    if(document.getElementById('route_arr').innerHTML=="없 음"){
        document.getElementById('trip_route_arr').value="등록 안함";
    }else{
        document.getElementById('trip_route_arr').value=document.getElementById('route_arr').innerHTML;
    }

    // 내용 검사


    // 요청
    document.trip_write_form.submit();

}
</script>
<script>
    $(function(){
        $('#search_location').keyup(function(){
            var search_spot = $('#search_location').val();
            // console.log('search_spot: '+search_spot);
            // document.getElementById('recommend_p').innerHTML="[검색 가능장소]<br>";
            if(search_spot.length!=0){
                $('#recommend_div').css('display','');
                $.ajax({
                    type: 'post',
                    dataType: 'text',
                    url: 'ajax.php',
                    data: {mode:'search_recommend_check', search_spot:search_spot},
                    success: function(data){
                        console.log("data: "+data);
                        if(data!="-1"){
                            document.getElementById('recommend_p').innerHTML="[등록 가능장소]<br>";
                            // console.log("data: "+document.getElementById('recommend_p').innerHTML);
                            var recommend_spot_arr=data.split(',');
                            
                            recommend_spot_arr.forEach(function(element){
                                document.getElementById('recommend_p').innerHTML = document.getElementById('recommend_p').innerHTML+"<br> #"+element;
                            });
                        }else{
                                // document.getElementById('recommend_p').innerHTML = document.getElementById('recommend_p').innerHTML+"<br>           ※ 없음";
                        }
                    },
                    error: function(request,status,error){
                        console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
                    }
                });
            }else{
                $('#recommend_div').css('display','none');
                document.getElementById('recommend_p').innerHTML="[등록 가능장소]<br>";
            }
        });
    });


$(document).ready(function() {
    // 썸머노트
    $('#summernote').summernote({
        heigh: 500,
        minHeight: 300,
        maxHeight: null,
    });
    
});



function trip_type_change(obj) {
    console.log('trip_type_change() 실행');
    document.getElementById('trip_start_day').value = '0000-00-00';
    document.getElementById('trip_finish_day').value = '0000-00-00';
    $("#trip_day_alert").html("");
}

// 여행 시작일 선택시
function trip_start_day_change(obj) {
    console.log('trip_start_day_change() 실행');
    // 마지막 일 초기화
    document.getElementById('trip_finish_day').value = '0000-00-00';
    document.getElementById('trip_day_check').value = "0";
    var trip_type = document.getElementById('trip_type').value;
    if (trip_type != 1 && trip_type != 2) {
        alert('여행 유형을 선택해주세요');
        document.getElementById('trip_type').focus();
        obj.value = '0000-00-00';
        return false;
    }

    $.ajax({
        type: 'post',
        dataType: 'text',
        url: 'ajax.php',
        data: {
            mode: 'trip_start_day_check',
            trip_start_day: obj.value,
            trip_type: trip_type
        },
        success: function(data) {
            console.log("data: " + data);
            if (trip_type == 1) {
                // <여행 후기> 0: 정상, 1: 미래를 선택한 경우
                if (data == 0) {
                    // 정상
                    $("#trip_day_alert").html("");
                    document.getElementById('trip_finish_day').focus();
                } else if (data == 1) {
                    // 안됨 오류 만들기
                    $("#trip_day_alert").html("※ 여행 후기글 작성시에는 오늘보다 미래를 선택할수 없습니다");
                    document.getElementById('trip_start_day').focus();
                    obj.value = '0000-00-00';
                    document.getElementById('trip_day_check').value = "0";
                }
            } else if (trip_type == 2) {
                // <여행 계획> 0: 정상, 1: 과거를 선택한 경우
                if (data == 0) {
                    // 정상
                    $("#trip_day_alert").html("");
                    document.getElementById('trip_finish_day').focus();

                } else if (data == 1) {
                    // 안됨 오류 만들기
                    $("#trip_day_alert").html("※ 여행 계획글 작성시에는 오늘보다 과거를 선택할수 없습니다");
                    document.getElementById('trip_start_day').focus();
                    obj.value = '0000-00-00';
                    document.getElementById('trip_day_check').value = "0";
                }
            }
        },
        error: function(request, status, error) {
            console.log('code: ' + request.status + "\n" + 'message: ' + request.responseText + "\n" +
                'error: ' + error);
        }
    });
}

// 마지막 일 선택 검사
function trip_finish_day_change(obj) {
    console.log('trip_finish_day_change() 실행');
    var trip_type = document.getElementById('trip_type').value;
    if (trip_type != 1 && trip_type != 2) {
        alert('여행 유형을 선택해주세요');
        document.getElementById('trip_type').focus();
        obj.value = '0000-00-00';
        return false;
    }
    var trip_start_day = document.getElementById('trip_start_day').value;
    if (trip_start_day == "") {
        alert('시작일을 선택해주세요');
        document.getElementById('trip_start_day').focus();
        return false;
    }
    $.ajax({
        type: 'post',
        dataType: 'text',
        url: 'ajax.php',
        data: {
            mode: 'trip_finish_day_check',
            trip_start_day: trip_start_day,
            trip_finish_day: obj.value,
            trip_type: trip_type
        },
        success: function(data) {
            console.log("data: " + data);
            if (data == 0) {
                // 통과
                $("#trip_day_alert").html("");
                document.getElementById('trip_day_check').value = "1";
            } else if (data == 1) {
                // 시작일보다 마지막일이 과거일 경우
                $("#trip_day_alert").html("※ 시작일 이후의 날짜를 선택해주세요");
                document.getElementById('trip_finish_day').focus();
                obj.value = '0000-00-00';
                document.getElementById('trip_day_check').value = "0";
            } else if (data == 2) {
                // 여행 후기을 작성중인데 마지막일이 오늘보다 미래일 경우
                $("#trip_day_alert").html("※ 여행 후기 작성시에는 마지막일을 오늘 또는 과거를 선택해주세요");
                document.getElementById('trip_finish_day').focus();
                obj.value = '0000-00-00';
                document.getElementById('trip_day_check').value = "0";
            }
        },
        error: function(request, status, error) {
            console.log('code: ' + request.status + "\n" + 'message: ' + request.responseText + "\n" +
                'error: ' + error);
        }
    });

}

// 경로 추가
function addRoute(){
    var add_route = document.getElementById('add_route').value;
    if(add_route.length==0){
        alert('경로를 입력해주세요');
        document.getElementById('add_route').focus();
        return false;
    }
    document.getElementById('add_route').value="";
    document.getElementById('add_route').focus();
    console.log(document.getElementById('route_arr').innerHTML);
    if(document.getElementById('route_arr').innerHTML=="없 음"){
        document.getElementById('route_arr').innerHTML=add_route;    
    }else{
        document.getElementById('route_arr').innerHTML=document.getElementById('route_arr').innerHTML+', '+add_route;
    }
}

// 경로 지우기
function initRoute(){
    document.getElementById('route_arr').innerHTML="없 음";
    document.getElementById('trip_route_arr').value ="";
}

// 검색 장소 등록
function register_spot(){
    var search_spot = document.getElementById('search_location').value;
    if(search_spot.length==0){
        alert('장소를 입력해주세요');
        return false;
    }

    $.ajax({
        type: 'post',
        dataType: 'text',
        url: 'ajax.php',
        data: {mode:'check_spot', search_spot:search_spot},
        success: function(data){
            console.log("지금 data: "+data);
            if(data==-1){
                document.getElementById('search_location').focus();
                document.getElementById('search_location').value="";
                $('#recommend_div').css('display','none');
                document.getElementById('recommend_p').innerHTML="[등록 가능장소]<br>";
                alert('등록 가능한 장소를 입력해주세요');
                return false;
            }else{
                document.getElementById('register_location').innerHTML = data;
                document.getElementById('trip_location').value = data;
                document.getElementById('search_location').value="";
                $('#recommend_div').css('display','none');
                document.getElementById('recommend_p').innerHTML="[등록 가능장소]<br>";
            }
        },
        error: function(request,status,error){
            console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
        }
    });

    
}


// 이미지 미리보기 및 조건검사
$(document).ready(function() {

$("#fileToUpload").change(function() {
    readURL(this);
});

});
// 이미지 미리보기 및 조건검사
function readURL(input) {
var fileNm = $("#fileToUpload").val();
if (fileNm != "") { 
    var ext = fileNm.slice(fileNm.lastIndexOf(".") + 1).toLowerCase();
    if (!(ext == "gif" || ext == "jpg" || ext == "png")) {
        alert("이미지파일 (.jpg, .png, .gif ) 만 업로드 가능합니다.");
        $("#fileToUpload").val("");
        return false;
    }
}
if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
        $('#image_section_span').css('display','none');
        $('#image_section').css('display','');
        $('#image_section').attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
}
}
</script>

<body>

    <?php

include('dbcon.php');
$login_id = $_SESSION['login_id'];
$sql = "select member_no from member where member_id='$login_id';";
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_assoc($result);
$login_member_no = $row['member_no'];
mysqli_close($con);

?>

    <!-- nav 삽입 -->
    <?php //include_once("nav.php");?>
    <br><br><br>
    <div class="container">
        <h2>나의 여행 남기기</h2><br><br>

        


        <br><br><br>
        <form name="trip_write_form" id="trip_write_form" method="post" action="trip_detail.php" enctype="multipart/form-data">
        <div class="mb-3">
                <label for="title"><strong>대표사진</strong> </label>(선택하지 않으면 기본사진이 올라갑니다)
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td colspan="2"><input type="file" name="fileToUpload" id="fileToUpload"> </td> </tr>
                        <tr>
                            <td  style="width:20%"><label for="title">선택한 대표사진</label><br></td>
                            <td>
                                <span id="image_section_span"> 없 음 </span>
                                <img id="image_section" src="#" width="300px" height="300px" style="display:none">
                                
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br><br>
        <div class="mb-3">
                <label for="title">제목</label>
                <input type="text" class="form-control" name="trip_title" id="trip_title" placeholder="제목을 입력해 주세요" maxlength="30">
            </div>
            <br><br>
            <div class="mb-3">
                <label for="content">내용</label>
                <textarea class="summernote" name="trip_content" id="summernote"></textarea>
            </div>
            <br><br>
            <div class="mb-3">
                <label for="title">여행 유형</label>
                <select name="trip_type" id="trip_type" class="custom-select" onchange="trip_type_change(this);">
                    <option selected>여행 후기인가요? 여행 계획인가요?</option>
                    <option value="1">여행 후기</option>
                    <option value="2">여행 계획</option>
                </select>
            </div>
            <br><br><br>

            <div class="mb-3">
                <label for="title">여행 일정</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span>시작 일&nbsp;</span>
                <input type="date" name="trip_start_day" id="trip_start_day" onchange="trip_start_day_change(this);">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <span>마지막 일&nbsp;</span>
                <input type="date" name="trip_finish_day" id="trip_finish_day" onchange="trip_finish_day_change(this);">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
                <p class="text-right">
                    <span id="trip_day_alert" name="trip_day_alert" style="color:red;"></span><br>
                </p>
            </div>
            
            <br>
            <div class="mb-3">
                <label for="title">장소&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</label><span id="register_location" name="register_location">등록 안함</span>

                <input maxlength="15" type="text" id="search_location" name="search_location" class="form-control" 
                    placeholder="지역명을 입력해주세요" autocomplete="off"><br>
                    
                <!-- <input type="text" class="form-control" name="search_location" id="search_location"
                    placeholder="장소를 입력해 주세요" maxlength="15" autocomplete="off"> -->
                    <p class="text-right">
                        <!-- <button class="btn btn-sm btn-secondary" onclick="register_spot()">장소등록</button> -->
                        <a class="btn btn-sm btn-secondary" onclick="register_spot()">장소등록</a>
                    </p>
                    
                    <div id="recommend_div" style="border:1px solid; display:none; padding:30px">
                        <p id="recommend_p" style="font-weight:bold;">
                            [등록 가능 장소]<br>
                        </p>
                    </div>  
                <!-- <input type="text" class="form-control" name="board_title" id="board_title"> -->
            </div>
            <br><br>

            <div class="mb-3">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width:10%"><label for="title">여행 경로</label></td>
                            <td>
                                <input type="text" id="add_route" name="add_route" maxlength="15" style="width:300px;">
                                
                            </td>
                            <td style="width:20%"><button onclick="addRoute()" type="button" class="btn btn-sm btn-secondary">경로 추가</button></td>
                        </tr>
                        <tr>
                            <td><label for="title">추가된 경로</label><br></td>
                            <td >
                                <p id="route_arr">없 음</p>
                            </td>
                            <td>
                                <!-- <button onclick="initRoute()" type="button" class="btn btn-sm btn-secondary">경로 저장</button><br><br> -->
                                <button onclick="initRoute()" type="button" class="btn btn-sm btn-danger">추가된 경로 지우기</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br><br><br><br>



            <!-- <div class="mb-3">
                <label for="title">제목</label>
                <input type="text" class="form-control" name="trip_title" id="trip_title" placeholder="제목을 입력해 주세요" maxlength="30">
            </div>
            <br><br>
            <div class="mb-3">
                <label for="content">내용</label>
                <textarea class="summernote" name="trip_content" id="summernote"></textarea>
            </div> -->
            <input type="hidden" id="trip_location" name="trip_location" value="" />
            <input type="hidden" id="trip_route_arr" name="trip_route_arr" value="" />

            <!-- <input type="hidden" id="trip_representative_img" name="trip_representative_img"
                value="<?php echo $_COOKIE['temp_img_upload'];?>" /> -->
            <input type="hidden" id="trip_write_member_no" name="trip_write_member_no"
                value="<?php echo $login_member_no; ?>" />
            <input type="hidden" id="trip_write_action" name="trip_write_action" value="true" />
            <input type="hidden" id="trip_day_check" name="trip_day_check" value="0" />
        </form>
        

        <div class="col text-right">
            <a href="#" class="btn btn btn-secondary" data-toggle="modal" data-target="#back-check-modal">취소</a>
            <a href="#" onclick=" self.close(); tripWrite()" class="btn btn btn-secondary">여행남기기</a>
        </div>
        
    </div>

    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>


    <!-- footer 삽입 -->
    <?php //include_once("footer.php");?>

    <!-- loader 삽입 -->
    <!-- loader -->
    <!-- <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px">
            <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
            <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10"
                stroke="#F96D00" /></svg></div> -->


    <!-- <script src="js/jquery.min.js"></script>  -->
    <script src="js/jquery-migrate-3.0.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.easing.1.3.js"></script>
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.stellar.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/aos.js"></script>
    <script src="js/jquery.animateNumber.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <!-- <script src="js/jquery.timepicker.min.js"></script> -->
    <script src="js/scrollax.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false">
    </script>
    <!-- <script src="js/google-map.js"></script> -->
    <script src="js/main.js"></script>

</body>


<!-- The Modal -->
<div class="modal fade" id="back-check-modal">
    <div class="modal-dialog">
        <!-- <div class="modal-dialog modal-lg"> -->
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Traveler</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <h4>
                    메인화면으로 이동하시겠습니까?
                </h4><br>
                <span>(※ 작성중이던 글이 사라집니다)</span>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <p>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-toggle="modal"
                        data-target="#">아니요</button><br />
                </p>
                <p>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="window.location.replace('index.php'); self.close();">네</button>
                </p>
            </div>

        </div>
    </div>
</div>
</div>




</html>