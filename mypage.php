<?php session_start(); 
if(empty($_SESSION['login_id'])){
    // echo "<script>alert('잘못된 접근입니다.');</script>";
    echo "<script>window.location.replace('index.php');</script>";
}
?>

<?php
// 장소 등록 액션
if(isset($_POST['register_spot_action'])){
    $spot_city = $_POST['register_spot_city'];
    $spot_name = $_POST['register_spot_name'];
    include('dbcon.php');
    $sql = "INSERT INTO spot (spot_city,spot_name) VALUES ('$spot_city','$spot_name');";
    $result = mysqli_query($con,$sql);
    // echo "<script>history.back();</script>";
    echo "<script>window.location.replace('mypage.php?mypage_type=1');</script>";
    exit;
}

// 장소 삭제 액션
if(isset($_POST['remove_spot_action'])){
    $spot_city = $_POST['remove_spot_city'];
    $spot_name = $_POST['remove_spot_name'];
    include('dbcon.php');
    $sql = "DELETE FROM spot WHERE spot_city='$spot_city' AND spot_name='$spot_name';";
    $result = mysqli_query($con,$sql);
    // echo "<script>history.back();</script>";
    echo "<script>window.location.replace('mypage.php?mypage_type=1');</script>";
    // mypage.php?mypage_type=1
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header 삽입 -->
    <?php include_once("header.php");?>
</head>
<script>
function default_profile_img(){
    var login_id = document.getElementById('login_id').value;
    $.ajax({
        type: 'post',
        dataType: 'text',
        url: 'ajax.php',
        data: {mode:'default_profile_img', login_id:login_id},
        success: function(data){
            console.log("data: "+data);
            location.reload();
        },
        error: function(request,status,error){
            console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
        }
    });
}

function register_spot_onclick(){
    // alert('adfsaf');
    var spot_city = document.getElementById('register_spot_city').value;
    var spot_name = document.getElementById('register_spot_name').value;
    if (spot_city != '서울' && spot_city != '부산' && spot_city != '제주도' && spot_city != '강원도'
    && spot_city != '경기도' && spot_city != '인천' && spot_city != '경상도' && spot_city != '전라도' && spot_city != '충청도') {
        alert('시/도를 선택해주세요');
        return false;
    }
    if(spot_name.length==0){
        alert('지역명을 입력해주세요');
        return false;
    }

    $.ajax({
        type: 'post',
        dataType: 'text',
        url: 'ajax.php',
        data: {mode:'admin_check_spot', spot_city:spot_city, spot_name:spot_name},
        success: function(data){
            console.log("data: "+data);
            if(data==-1){
                document.register_spot.submit();
            }else{
                alert('동일한 지역명이 있습니다 다른 지역명을 사용해주세요');
                return false;
            }
        },
        error: function(request,status,error){
            console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
        }
    });

}

function remove_spot_onclick(){
    var spot_city = document.getElementById('remove_spot_city').value;
    var spot_name = document.getElementById('remove_spot_name').value;
    if (spot_city != '서울' && spot_city != '부산' && spot_city != '제주도' && spot_city != '강원도'
    && spot_city != '경기도' && spot_city != '인천' && spot_city != '경상도' && spot_city != '전라도' && spot_city != '충청도') {
        alert('시/도를 선택해주세요');
        return false;
    }
    if(spot_name.length==0){
        alert('지역명을 입력해주세요');
        return false;
    }

    $.ajax({
        type: 'post',
        dataType: 'text',
        url: 'ajax.php',
        data: {mode:'admin_check_spot', spot_city:spot_city, spot_name:spot_name},
        success: function(data){
            console.log("data: "+data);
            if(data==0){
                document.remove_spot.submit();
            }else{
                alert('삭제하려는 지역이 없습니다');
                return false;
            }
        },
        error: function(request,status,error){
            console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
        }
    });

    

}
</script>
<body>
    <input type="hidden" value="<?php echo $_SESSION['login_id']; ?>" id="login_id">
<!-- nav 삽입 -->
<?php include_once("nav.php");?>

<br><br><br><br>

    <div class="container">
        <h2>마이 페이지</h2><br>
        <!-- <p>최신의 여행정보를 확인하세요</p> -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <?php
                if($_GET['mypage_type']==0){
                    echo "<a class='nav-link active' href='mypage.php?mypage_type=0'>나의 정보</a>";
                }else{
                    echo "<a class='nav-link' href='mypage.php?mypage_type=0'>나의 정보</a>";
                }
                ?>
                
            </li>
            <?php
            if($_SESSION['login_id']=='admin'){
                ?>
                <li class="nav-item">
                    <?php
                    if($_GET['mypage_type']==1){
                        echo "<a class='nav-link active' href='mypage.php?mypage_type=1'>장소 관리</a>";
                    }else{
                        echo "<a class='nav-link' href='mypage.php?mypage_type=1'>장소 관리</a>";
                    }
                    ?>
                </li>
                <?php
            }
            ?>
            
            <li class="nav-item">
            <?php
                if($_GET['mypage_type']==2){
                    echo "<a class='nav-link active' href='mypage.php?mypage_type=2'>내가 작성한 여행 글</a>";
                }else{
                    echo "<a class='nav-link' href='mypage.php?mypage_type=2'>내가 작성한 여행 글</a>";
                }
                ?>
            </li>
            <li class="nav-item">
            <?php
                if($_GET['mypage_type']==3){
                    echo "<a class='nav-link active' href='mypage.php?mypage_type=3'>내가 좋아하는 여행 글</a>";
                }else{
                    echo "<a class='nav-link' href='mypage.php?mypage_type=3'>내가 좋아하는 여행 글</a>";
                }
                ?>
            </li>
        </ul><br><br>

        <?php 
        $login_id = $_SESSION['login_id'];
        include('dbcon.php');
        $sql = "SELECT * FROM member WHERE member_id='$login_id';";
        $result = mysqli_query($con,$sql);
        $row = mysqli_fetch_assoc($result);
        $member_no = $row['member_no'];
        $member_id = $row['member_id'];
        $member_pw = $row['member_pw'];
        $member_email = $row['member_email'];
        $member_introduce = $row['member_introduce'];
        $member_profile_img = $row['member_profile_img'];
        $member_name = $row['member_name'];
        $member_age = $row['member_age'];
        $member_gender = $row['member_gender'];
        $member_join_time = $row['member_join_time'];
        // $member_address = $row['member_address'];
        mysqli_close($con);


        if($_GET['mypage_type']==0){
        // 나의 정보
        ?>
        
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td colspan="2"><label for="title"><strong>프로필 사진</strong></label></td>
                    <!-- <td>(선택하지 않으면 기본사진이 올라갑니다)</td> -->
                </tr>
                <tr>
                    <form name="upload_form" action="upload.php" method="post"
                        enctype="multipart/form-data">
                        <td style="width:10%"><input type="file" name="fileToUpload" id="fileToUpload"></td>
                        
                        <td><input type="submit" value="프로필사진 등록" class="btn btn-sm btn-secondary" name="profile_img_submit"></td>
                    </form>
                </tr>
                <tr>

                    <td style="width:10%"><label for="title"><strong>등록된 프로필 사진</strong></label><br></td>
                        <td><img src="<?php echo $member_profile_img; ?>" class="rounded-circle" width="250px" height="250px"> <br>
                            <p class=text-right>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="default_profile_img()">기본 프로필 사진 적용</button>
                            </p>
                    </td>
                </tr>
                <tr>
                <script>
                    $(document).ready(function() {
                        $('#introduce_textarea').on('keyup', function() {
                            $('#counter').html("("+$(this).val().length+" / 최대 400자)");    //글자수 실시간 카운팅
                            if($(this).val().length > 400) {
                                $(this).val($(this).val().substring(0, 400));
                                alert("최대 400자까지 입력 가능합니다.");
                                $(this).val(content.substring(0, 400));
                                $('#counter').html("(400 / 최대 400자)");
                            }
                        });
                    });

                    function register_my_introduce(){
                        var introduce_textarea = document.getElementById('introduce_textarea').value;
                        $.ajax({
                            type: 'post',
                            dataType: 'text',
                            url: 'ajax.php',
                            data: {mode:'register_my_introduce', introduce_textarea:introduce_textarea},
                            success: function(data){
                                alert('자기소개를 등록/수정하였습니다');
                                $('#register_introduce_div').css('display','none');
                                location.reload();
                            },
                            error: function(request,status,error){
                                console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
                            }
                        });
                    }
                    
                    function show_register_introduce_div(){
                        $('#register_introduce_div').css('display','');
                    }
                </script>
                    <td style="width:10%"><label for="title"><strong>자기소개</strong></label></td>
                    <td>
                    <label for="content"><?php echo nl2br($member_introduce); ?></label><br>
                    <p class=text-right>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="show_register_introduce_div()">소개 등록/수정</button>
                    </p>
                    <div id="register_introduce_div" style="display:none;">

                        <label for="content">자기 소개</label>&nbsp;&nbsp;<span style="color:#aaa;" id="counter">(0 / 최대 400자)</span>
                        <textarea id="introduce_textarea" rows="5" type="text" class="form-control"  maxlength="400"><?php echo $member_introduce; ?></textarea>
                        <br>
                            <p class=text-right>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="register_my_introduce()">소개 등록/수정</button>
                                
                            </p>
                    </div>
                    </td>
                </tr>

                <tr>
                <td style="width:20%"><label for="title"><strong>아이디</strong></label></td>
                <td><?php echo $member_id; ?></td>
            </tr>
            <tr>
                <td style="width:20%"><label for="title"><strong>이메일</strong></label></td>
                <td><?php echo $member_email; ?></td>
            </tr>
            <!-- <tr>
                <td style="width:20%"><label for="title"><strong>비밀번호</strong></label></td>
                <td><button type="button" class="btn btn-secondary" onclick="alert('비밀번호 수정');">비밀번호 수정</button></td>
            </tr> -->
            <!-- <tr>
                <td style="width:20%"><label for="title"><strong>회원가입 일시</strong></label></td>
                <td><?php echo $member_join_time; ?></td>
            </tr> -->

            </tbody>
        </table>
        <?php

        }elseif($_GET['mypage_type']==1){
        // 장소 관리
        ?>
        
        <div class="mb-3">
            <label for="title"><strong>현재 등록되어있는 장소</strong></label>
            <table class="table table-bordered table-sm">
                <tbody>
                    <?php
                include('dbcon.php');
                $sql = "SELECT spot_city FROM spot GROUP BY spot_city;";
                $result = mysqli_query($con,$sql);
                while($spot = mysqli_fetch_array($result)){
                    $spot_city = $spot['spot_city'];
                    ?>
                    <tr>
                        <!-- <td style="width:10%;"><strong><?php echo $spot_city;?></strong></td> -->
                        <td style="width:10%;"><?php echo $spot_city;?></td>
                        <td>
                        <?php
                            $spot_sql = "SELECT spot_name FROM spot WHERE spot_city='$spot_city';";
                            $spot_result = mysqli_query($con,$spot_sql);
                            $i=0;
                            while($row = mysqli_fetch_array($spot_result)){
                                $spot_name = $row['spot_name'];
                                if($i==0){
                                    echo $spot_name;
                                }else{
                                    echo ',&nbsp;&nbsp;'.$spot_name;
                                }
                                $i++;
                                
                            }
                        ?>
                        </td>
                    </tr>
                    
                    <?php
                }
                ?>
                
                    
                </tbody>
            </table>

        </div><br><br>

        <div class="mb-3">
            <form id="register_spot" name="register_spot" method="post" action="mypage.php">
            <label for="title"><strong>장소 추가</strong></label>
            <table class="table table-bordered table-sm">
    
            <tbody>
            <tr>
                <td width="10%"><label for="title">시/도 선택</label></td>
                <td>
                <select name="register_spot_city" id="register_spot_city" class="custom-select">
                    <option selected>시/도 선택</option>
                    <option value="서울">서울</option>
                    <option value="부산">부산</option>
                    <option value="제주도">제주도</option>
                    <option value="강원도">강원도</option>
                    <option value="경기도">경기도</option>
                    <option value="인천">인천</option>
                    <option value="경상도">경상도</option>
                    <option value="전라도">전라도</option>
                    <option value="충청도">충청도</option>
                </select>
                </td>
            </tr>
            <tr>
                <td>상세지역</td>
                <td>
                <input type="text" class="form-control" name="register_spot_name" id="register_spot_name" placeholder="지역명을 입력해 주세요" maxlength="30">
                <br>
                <p class="text-right">
                    <!-- <button type="button" onclick="register_spot()" class="btn btn-sm btn-secondary">장소 추가</button> -->
                    <button type="button" onclick="register_spot_onclick();" class="btn btn-sm btn-secondary">장소 추가</button>
                </p>
                </td>
                
            </tr>
          
            </tbody>
        </table>
        <input type="hidden" name="register_spot_action" id="register_spot_action" value="true">
        </form>
        </div><br><br>

        <div class="mb-3">
            <form id="remove_spot" name="remove_spot" method="post" action="mypage.php">
            <label for="title"><strong>장소 삭제</strong></label>
            <table class="table table-bordered table-sm">
    
            <tbody>
            <tr>
                <td width="10%"><label for="title">시/도 선택</label></td>
                <td>
                <select name="remove_spot_city" id="remove_spot_city" class="custom-select">
                    <option selected>시/도 선택</option>
                    <option value="서울">서울</option>
                    <option value="부산">부산</option>
                    <option value="제주도">제주도</option>
                    <option value="강원도">강원도</option>
                    <option value="경기도">경기도</option>
                    <option value="인천">인천</option>
                    <option value="경상도">경상도</option>
                    <option value="전라도">전라도</option>
                    <option value="충청도">충청도</option>
                </select>
                </td>
                
            </tr>
            <tr>
                <td>상세지역</td>
                <td>
                <input type="text" class="form-control" name="remove_spot_name" id="remove_spot_name" placeholder="지역명을 입력해 주세요" maxlength="30">
                <br><p class="text-right">
                <button type="button" class="btn btn-sm btn-secondary" onclick="remove_spot_onclick()">장소 삭제</button>
                    </p>
                </td>
                
            </tr>
          
            </tbody>
        </table>
        <input type="hidden" name="remove_spot_action" id="remove_spot_action" value="true">
        </form>
        </div><br>
        
        <?php

        }elseif($_GET['mypage_type']==2){
        // 내가 작성한 여행 글
        include('dbcon.php');
        $sql = "SELECT * FROM trip WHERE trip_write_member_no='$member_no' AND trip_is_remove=0 ORDER BY trip_no DESC;";
        $result = mysqli_query($con,$sql);
        if(mysqli_num_rows($result)>0){
            while($trip = mysqli_fetch_array($result)){
                // 여행 하나씩 가져오기
                $trip_no = $trip['trip_no'];
                $trip_type_from_db = $trip['trip_type'];
                $trip_write_member_no = $trip['trip_write_member_no'];
                // $trip_start_day = $trip['trip_start_day'];
                // $trip_finish_day = $trip['trip_finish_day'];
                $trip_representative_img = $trip['trip_representative_img'];
                $trip_title = $trip['trip_title'];
                // $trip_content = $trip['trip_content'];
                $trip_write_time = $trip['trip_write_time'];
                $trip_location = $trip['trip_location'];
                // $trip_route_arr = $trip['trip_route_arr'];
                $trip_hit_count = $trip['trip_hit_count'];
                
                // 작성자 아이디, 프로필사진 찾기
                $find_write_member_sql = "select * from member where member_no='$trip_write_member_no'";
                $find_write_member_result = mysqli_query($con,$find_write_member_sql);
                $find_write_member_row = mysqli_fetch_array($find_write_member_result);
                $member_id = $find_write_member_row['member_id'];
                $member_profile_img = $find_write_member_row['member_profile_img'];


                // 이 여행을 좋아하는 사람수도 세야함
                $count_like_member_sql = "SELECT count(*) AS c FROM trip_good WHERE trip_good_trip_no='$trip_no';";
                $count_like_member_result = mysqli_query($con,$count_like_member_sql);
                $count_like_member_row = mysqli_fetch_assoc($count_like_member_result);
                $count_like_member = $count_like_member_row['c'];

                // 댓글 갯수 세야함
                $count_reply_sql = "SELECT count(*) AS c FROM trip_reply WHERE trip_no='$trip_no' AND trip_reply_is_remove=0;";
                $count_reply_result = mysqli_query($con,$count_reply_sql);
                $count_reply_row = mysqli_fetch_assoc($count_reply_result);
                $count_reply = $count_reply_row['c'];
                ?>
                <!-- one trip start -->
                
        <div class="col-md-12">
            <div class="blog-entry ftco-animate">
                <div class="text pt-2 mt-5">
                <div class="meta-wrap d-md-flex align-items-center">
                        <div class="author mb-4 d-flex align-items-center">
                            <img src="<?php echo $member_profile_img;?>" class="rounded-circle" alt="Cinque Terre" width="50" height="50">
                            <!-- <img src="images/person_1.jpg" alt="Cinque Terre" width="50" height="50"> -->
                            <div class="ml-3 info">
                                <span>Written by</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php 
                                    if($trip_type_from_db==1){
                                        ?>
                                        <span>[여행후기]</span>
                                        <?php
                                    }elseif($trip_type_from_db==2){
                                        ?>
                                        <span>[여행계획]</span>
                                        <?php
                                    }
                                ?>
                                
                                <h3><a href="#"><?php echo $member_id; ?></a>, <span><?php echo $trip_write_time; ?></span></h3>
                            </div>
                        </div>
                        <div class="half order-md-last text-md-right">
                            <p class="meta">
                                <span><i class="icon-heart"></i><?php echo $count_like_member; ?></span>
                                <span><i class="icon-eye"></i><?php echo $trip_hit_count; ?></span>
                                <span><i class="icon-comment"></i><?php echo $count_reply; ?></span>
                            </p>
                        </div>
                    </div>
                    <img src="<?php echo $trip_representative_img; ?>" width="500" > <br>
                    <br>
                    <h3 class="mb-4"><a href="trip_detail.php?trip_no=<?php echo $trip_no; ?>"><?php echo $trip_title; ?></a></h3>
                    
                    <p class="mb-4">장소 : <?php echo $trip_location; ?></p>
                </div>
            </div>
        </div>
        <!-- one trip end -->

                <?php
                
            }
        }else{
            // 여행 없음
        }
        mysqli_close($con);
        ?>
        <br><br>
    </div>
        

        <?php
        }elseif($_GET['mypage_type']==3){
        // 내가 좋아하는 여행 글
        include('dbcon.php');
        $sql = "SELECT * FROM trip_good WHERE trip_good_member_no='$member_no';";
        $result = mysqli_query($con,$sql);
        if(mysqli_num_rows($result)>0){
            while($row = mysqli_fetch_array($result)){
                $trip_no = $row['trip_good_trip_no'];

                $find_trip_sql = "SELECT * FROM trip WHERE trip_no='$trip_no';";
                $find_trip_result = mysqli_query($con,$find_trip_sql);
                $trip = mysqli_fetch_assoc($find_trip_result);

                // 여행 하나씩 가져오기
                
                $trip_type_from_db = $trip['trip_type'];
                $trip_write_member_no = $trip['trip_write_member_no'];
                // $trip_start_day = $trip['trip_start_day'];
                // $trip_finish_day = $trip['trip_finish_day'];
                $trip_representative_img = $trip['trip_representative_img'];
                $trip_title = $trip['trip_title'];
                // $trip_content = $trip['trip_content'];
                $trip_write_time = $trip['trip_write_time'];
                $trip_location = $trip['trip_location'];
                // $trip_route_arr = $trip['trip_route_arr'];
                $trip_hit_count = $trip['trip_hit_count'];
                $trip_is_remove = $trip['trip_is_remove'];
                if($trip_is_remove==1){
                    continue;
                }
                
                // 작성자 아이디, 프로필사진 찾기
                $find_write_member_sql = "select * from member where member_no='$trip_write_member_no'";
                $find_write_member_result = mysqli_query($con,$find_write_member_sql);
                $find_write_member_row = mysqli_fetch_array($find_write_member_result);
                $member_id = $find_write_member_row['member_id'];
                $member_profile_img = $find_write_member_row['member_profile_img'];


                // 이 여행을 좋아하는 사람수도 세야함
                $count_like_member_sql = "SELECT count(*) AS c FROM trip_good WHERE trip_good_trip_no='$trip_no';";
                $count_like_member_result = mysqli_query($con,$count_like_member_sql);
                $count_like_member_row = mysqli_fetch_assoc($count_like_member_result);
                $count_like_member = $count_like_member_row['c'];

                // 댓글 갯수 세야함
                $count_reply_sql = "SELECT count(*) AS c FROM trip_reply WHERE trip_no='$trip_no' AND trip_reply_is_remove=0;";
                $count_reply_result = mysqli_query($con,$count_reply_sql);
                $count_reply_row = mysqli_fetch_assoc($count_reply_result);
                $count_reply = $count_reply_row['c'];
                ?>
                <!-- one trip start -->
                
        <div class="col-md-12">
            <div class="blog-entry ftco-animate">
                <div class="text pt-2 mt-5">
                <div class="meta-wrap d-md-flex align-items-center">
                        <div class="author mb-4 d-flex align-items-center">
                            <img src="<?php echo $member_profile_img;?>" class="rounded-circle" alt="Cinque Terre" width="50" height="50">
                            <!-- <img src="images/person_1.jpg" alt="Cinque Terre" width="50" height="50"> -->
                            <div class="ml-3 info">
                                <span>Written by</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php 
                                    if($trip_type_from_db==1){
                                        ?>
                                        <span>[여행후기]</span>
                                        <?php
                                    }elseif($trip_type_from_db==2){
                                        ?>
                                        <span>[여행계획]</span>
                                        <?php
                                    }
                                ?>
                                
                                <h3><a href="#"><?php echo $member_id; ?></a>, <span><?php echo $trip_write_time; ?></span></h3>
                            </div>
                        </div>
                        <div class="half order-md-last text-md-right">
                            <p class="meta">
                                <span><i class="icon-heart"></i><?php echo $count_like_member; ?></span>
                                <span><i class="icon-eye"></i><?php echo $trip_hit_count; ?></span>
                                <span><i class="icon-comment"></i><?php echo $count_reply; ?></span>
                            </p>
                        </div>
                    </div>
                    <img src="<?php echo $trip_representative_img; ?>" width="500" > <br>
                    <br>
                    <h3 class="mb-4"><a href="trip_detail.php?trip_no=<?php echo $trip_no; ?>"><?php echo $trip_title; ?></a></h3>
                    
                    <p class="mb-4">장소 : <?php echo $trip_location; ?></p>
                </div>
            </div>
        </div>
        <!-- one trip end -->

                <?php
                
            }
        }else{
            // 여행 없음
        }
        mysqli_close($con);
        ?>
        <br><br>
    </div>
        
        
        <?php
        }
        ?>
        
        
    </div>
  
    
<br><br><br>

<!-- footer 삽입 -->
<?php include_once("footer.php");?>

<!-- loader 삽입 -->
<?php include_once("loader.php");?>
    
</body>
</html>