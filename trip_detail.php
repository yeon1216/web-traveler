<?php session_start(); ?>
<?php 
if(isset($_GET['trip_no'])){
    $trip_no = $_GET['trip_no'];
    if(isset($_SESSION['login_id'])){
        // 로그인 한 경우
        $login_id = $_SESSION['login_id'];
        if(isset($_COOKIE[$login_id.'_read_trip'])){
            $value = $trip_no.'|'.$_COOKIE[$login_id.'_read_trip'];
        }else{
            $value = $trip_no;
        }
        $read_trip_arr = explode("|",$value);
        $read_trip_arr=array_unique($read_trip_arr);
        $value='';
        foreach($read_trip_arr as $read_trip){
            if($value==''){
                $value = $read_trip;
            }else{
                $value = $value.'|'.$read_trip;
            }
        }
        setcookie($login_id.'_read_trip',$value,time()+86400);
        setcookie($login_id.'_hit_trip',$value,time()+86400);
    }else{
        // 로그인 안한 경우
        
        if(isset($_COOKIE['no_login_read_trip'])){
            $value = $trip_no.'|'.$_COOKIE['no_login_read_trip'];
        }else{
            $value = $trip_no;
        }
        $read_trip_arr = explode("|",$value);
        $read_trip_arr=array_unique($read_trip_arr);
        $value='';
        foreach($read_trip_arr as $read_trip){
            if($value==''){
                $value = $read_trip;
            }else{
                $value = $value.'|'.$read_trip;
            }
        }
        setcookie('no_login_read_trip',$value,time()+86400);
        setcookie('no_login_hit_trip',$value,time()+86400);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header 삽입 -->
    <?php include_once("header.php");?>
    <!-- 아이콘을 위해서 -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
</head>
<script>




/**
    여행 글 삭제
 */
function tripDelete() {
    document.trip_delete_form.submit();
}

/**
    여행 댓글 작성
 */
function tripReplyWrite(){  
    var login_id = <?php
                        if(empty($_SESSION['login_id'])){
                            echo 0;
                        }else{
                            echo 1;
                        }
                    ?>;
    // 로그인을 안했으면 댓글 안써지도록
    if(login_id==0){
        alert('로그인을 해주세요');
        return false;
    }
    if(document.getElementById('trip_reply_content').value.length==0){
        alert('댓글을 입력해주세요');
        document.getElementById('trip_reply_content').focus();
        return false;
    }
    document.trip_reply_write_form.submit();
}
</script>

<body>
<?php
/**
 * 잘못된 접근 예외처리
 */
if(isset($_GET['trip_no'])){
    $trip_no = $_GET['trip_no'];
    include('dbcon.php');
    $sql = "SELECT count(*) AS c FROM trip WHERE trip_no='$trip_no' AND trip_is_remove=0;";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    mysqli_close($con);
    if($row['c']==0){
        echo "<script>alert('없는 여행글입니다');</script>";
        echo "<script>window.location.replace('trip_list.php');</script>";
        exit;
    }
}elseif( !isset($_GET['logout_action']) && !isset($_POST['login_action']) && !isset($_POST['join_action']) && !isset($_POST['trip_reply_write_action'])
            && !isset($_POST['trip_write_action'])&& !isset($_POST['trip_update_action'])&& !isset($_POST['trip_delete_action'])&& !isset($_POST['trip_reply_delete_action'])){
    // echo "<script>alert('잘못된 접근입니다');</script>";
    echo "<script>window.location.replace('trip_list.php');</script>";
    exit;
}
?>
    <!-- nav 삽입 -->
    <?php include_once("nav.php");?>
    <?php
// 여행 글 작성
if(isset($_POST['trip_write_action'])){
    $trip_type = $_POST['trip_type'];
    $trip_write_member_no = $_POST['trip_write_member_no'];
    $trip_title = $_POST['trip_title'];

    $file = $_FILES['fileToUpload'];
    if(empty($file['tmp_name'])){
        $trip_representative_img = 'upload/2019-08-20 08:38:55.png';
    }else{
        $target_dir = "upload/";
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $check = getimagesize($file["tmp_name"]);
        $upload_img_dir =  $target_dir . date('Y-m-d H:i:s').'.'.$imageFileType;
        move_uploaded_file($file["tmp_name"], $upload_img_dir);
        $trip_representative_img = $upload_img_dir;
    }

    // $trip_representative_img = $_POST['trip_representative_img']; // 이거 다시 확인하기
    // if(empty($trip_representative_img)){
    //     $trip_representative_img='images/image_5.jpg';
    // }



    $trip_start_day = $_POST['trip_start_day'];
    $trip_finish_day = $_POST['trip_finish_day'];
    $trip_location = $_POST['trip_location'];
    $trip_route_arr = $_POST['trip_route_arr'];
    $trip_content = $_POST['trip_content'];

    include('dbcon.php');
    $sql = "INSERT INTO trip (trip_type, trip_write_member_no, trip_title, trip_content, trip_representative_img, trip_start_day, trip_finish_day, trip_location, trip_route_arr)
            VALUES ('$trip_type','$trip_write_member_no','$trip_title','$trip_content','$trip_representative_img','$trip_start_day','$trip_finish_day','$trip_location','$trip_route_arr');";
    $result = mysqli_query($con,$sql);

    $sql = "SELECT MAX(trip_no) FROM trip;";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $trip_no = $row['MAX(trip_no)'];

    mysqli_close($con);
    echo "<script>window.location.replace('trip_detail.php?trip_no=$trip_no');</script>";
    exit;
}

// 여행 글 수정
if(isset($_POST['trip_update_action'])){
    echo "<script>console.log('aaaa');</script>";
    $trip_no = $_POST['trip_no'];
    echo "<script>console.log('$trip_no');</script>";
    $trip_type = $_POST['trip_type'];
    // $trip_write_member_no = $_POST['trip_write_member_no'];
    $trip_title = $_POST['trip_title'];


    $file = $_FILES['fileToUpload'];
    if(empty($file['tmp_name'])){
        $trip_representative_img = $_POST['trip_representative_img'];
    }else{
        $target_dir = "upload/";
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $check = getimagesize($file["tmp_name"]);
        $upload_img_dir =  $target_dir . date('Y-m-d H:i:s').'.'.$imageFileType;
        move_uploaded_file($file["tmp_name"], $upload_img_dir);
        $trip_representative_img = $upload_img_dir;
    }

    $trip_start_day = $_POST['trip_start_day'];
    $trip_finish_day = $_POST['trip_finish_day'];
    $trip_location = $_POST['trip_location'];
    $trip_route_arr = $_POST['trip_route_arr'];
    $trip_content = $_POST['trip_content'];

    include('dbcon.php');
    $sql = "UPDATE trip SET trip_type='$trip_type', trip_title='$trip_title',trip_representative_img='$trip_representative_img',
                            trip_start_day='$trip_start_day', trip_finish_day='$trip_finish_day',trip_location='$trip_location', 
                            trip_route_arr='$trip_route_arr', trip_content='$trip_content' 
            WHERE trip_no='$trip_no';";
    $result = mysqli_query($con,$sql);

    mysqli_close($con);
    echo "<script>window.location.replace('trip_detail.php?trip_no=$trip_no');</script>";
    exit;
}

// 여행 글 삭제시
if($_POST['trip_delete_action']){
    $trip_no = $_POST['trip_no'];
    include('dbcon.php');
    $sql = "SELECT trip_type FROM trip WHERE trip_no='$trip_no';";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $trip_type = $row['trip_type'];
    $sql = "UPDATE trip SET trip_is_remove=1 WHERE trip_no='$trip_no';";
    $result = mysqli_query($con,$sql);
    
    if(isset($_COOKIE['temp_trip'])){ // temp_trip 쿠키가 있는 경우

        /**
         * $total_page 알아보기
         */
        $page_set = 5; // 한페이지 줄수

        $sql = "SELECT count(*) c FROM info WHERE info_is_remove=0;";
        $result = mysqli_query($con,$sql);
        $row = mysqli_fetch_assoc($result);
        $total = $row['c'];

        $total_page=ceil($total/$page_set);

        /**
         * temp_trip 쿠키에서 필요한 정보 얻어오기
         */
        $value = $_COOKIE['temp_trip'];
        $values = explode('/',$value);
        $trip_type = $values[0];
        $search_location = $values[1];
        $page = $values[2];
        $cookie_trip_no = $values[3];
        $total_page = $values[4];
        $last_page_data_count = $values[5];

        // /**
        //  * temp_trip 쿠키 삭제
        //  */
        // setcookie("temp_trip","",time()-3600);

        /**
         * 상황에 맞게 페이지 이동
         */
        if($trip_no!=$cookie_trip_no){ // 현재 페이지와 쿠키에 저장되있는 페이지가 다른 경우
            echo "<script>alert('여행이 삭제되었습니다');</script>";
            echo "<script>window.location.replace('trip_list.php?trip_type=$trip_type&page=1');</script>";
            exit;
        }else{
            if($search_location==''){ // 검색어가 없는 경우
                if($page==$total_page){
                    if($last_page_data_count==1){
                        $page--;
                        echo "<script>alert('여행이 삭제되었습니다');</script>";
                        echo "<script>window.location.replace('trip_list.php?trip_type=$trip_type&page=$page');</script>";
                        exit;    
                    }else{
                        echo "<script>alert('여행이 삭제되었습니다');</script>";
                        echo "<script>window.location.replace('trip_list.php?trip_type=$trip_type&page=$page');</script>";
                        exit;    
                    }
                }else{
                    echo "<script>alert('여행이 삭제되었습니다');</script>";
                    echo "<script>window.location.replace('trip_list.php?trip_type=$trip_type&page=$page');</script>";
                    exit;
                }
            }else{ // 검색어가 있는 경우
                if($page==$total_page){ // 현재 페이지가 마지막 페이지인 경우
                    if($last_page_data_count==1){ // 이 게시글이 삭제되면 현재 페이지의 글이 없는 경우
                        if($page!=1) $page--;
                        echo "<script>alert('여행이 삭제되었습니다');</script>";
                        echo "<script>window.location.replace('trip_list.php?trip_type=$trip_type&search_location=$search_location&page=$page');</script>";
                        exit;
                    }else{ // 이 게시글이 삭제되어도 현재 페이지에 게시글이 있는 경우
                        echo "<script>alert('여행이 삭제되었습니다');</script>";
                        echo "<script>window.location.replace('trip_list.php?trip_type=$trip_type&search_location=$search_location&page=$page');</script>";
                        exit;
                    }
                }else{ // 현재 페이지가 마지막 페이지가 아닌 경우
                    echo "<script>alert('여행이 삭제되었습니다');</script>";
                    echo "<script>window.location.replace('trip_list.php?trip_type=$trip_type&search_location=$search_location&page=$page');</script>";
                    exit;
                }
            }
        }
    }else{ // temp_trip 쿠키가 없는 경우
        echo "<script>alert('여행이 삭제되었습니다');</script>";
        echo "<script>window.location.replace('trip_list.php?trip_type=$trip_type');</script>";
        exit;
    }
    mysqli_close($con);
}

if($_POST['trip_reply_write_action']){ // 여행글 댓글 작성시
    include('dbcon.php');
    $trip_no = $_POST['trip_no'];
    $trip_reply_write_member_no = $_POST['trip_reply_write_member_no'];
    $trip_reply_content = $_POST['trip_reply_content'];
    
    $sql = "INSERT INTO trip_reply (trip_no, trip_reply_write_member_no, trip_reply_content)
            VALUES ($trip_no,$trip_reply_write_member_no,'$trip_reply_content');";
    $result = mysqli_query($con,$sql);
    
    echo "<script>window.location.replace('trip_detail.php?trip_no=$trip_no');</script>";
    exit;
}


if($_POST['trip_reply_delete_action']){ // 여행글 댓글 삭제시
    include('dbcon.php');
    $trip_no = $_POST['trip_no'];
    $trip_reply_no = $_POST['trip_reply_no'];    
    $sql = "UPDATE trip_reply SET trip_reply_is_remove=1 WHERE trip_reply_no='$trip_reply_no';";
    $result = mysqli_query($con,$sql);
    echo "<script>alert('댓글이 삭제되었습니다')</script>";
    echo "<script>window.location.replace('trip_detail.php?trip_no=$trip_no');</script>";
    exit;
}
?>

    <?php
    if(isset($_GET['trip_no'])){
        $trip_no = $_GET['trip_no'];
        include('dbcon.php');

        /**
         * 조회수 늘리는 코드
         */
        if(isset($_SESSION['login_id'])){
            $login_id = $_SESSION['login_id'];
            if(empty($_COOKIE[$login_id.'_hit_trip'])){
                $sql = "UPDATE trip SET trip_hit_count=trip_hit_count+1 WHERE trip_no='$trip_no';";
                $result = mysqli_query($con,$sql);
            }else{
                $value=$_COOKIE[$login_id.'_hit_trip'];
                $hit_trip_arr = explode("|",$value);
                $i=0;
                foreach($hit_trip_arr as $hit_trip){
                    if($hit_trip==$trip_no){
                        $i=1;
                    }
                }
                if($i==0){
                    $sql = "UPDATE trip SET trip_hit_count=trip_hit_count+1 WHERE trip_no='$trip_no';";
                    $result = mysqli_query($con,$sql);
                }
            }
        }else{
            if(empty($_COOKIE['no_login_hit_trip'])){
                $sql = "UPDATE trip SET trip_hit_count=trip_hit_count+1 WHERE trip_no='$trip_no';";
                $result = mysqli_query($con,$sql);
            }else{
                $value=$_COOKIE['no_login_hit_trip'];
                $hit_trip_arr = explode("|",$value);
                $i=0;
                foreach($hit_trip_arr as $hit_trip){
                    if($hit_trip==$trip_no){
                        $i=1;
                    }
                }
                if($i==0){
                    $sql = "UPDATE trip SET trip_hit_count=trip_hit_count+1 WHERE trip_no='$trip_no';";
                    $result = mysqli_query($con,$sql);
                }
            }
        }
    
        /**
         * 디비에서 여행정보 꺼내기
         */
        $sql = "SELECT * FROM trip WHERE trip_no=$trip_no;";
        $result = mysqli_query($con,$sql);
        $trip = mysqli_fetch_assoc($result);
        $trip_no = $trip['trip_no'];
        $trip_type_from_db = $trip['trip_type'];
        $trip_write_member_no = $trip['trip_write_member_no'];
        $trip_start_day = $trip['trip_start_day'];
        $trip_finish_day = $trip['trip_finish_day'];
        $trip_representative_img = $trip['trip_representative_img'];
        $trip_title = $trip['trip_title'];
        $trip_content = $trip['trip_content'];
        $trip_write_time = $trip['trip_write_time'];
        $trip_location = $trip['trip_location'];
        $trip_route_arr = $trip['trip_route_arr'];
        $trip_hit_count = $trip['trip_hit_count'];

        /**
         * 작성자 아이디 찾기
         */
        $find_write_member_sql = "SELECT * FROM member WHERE member_no='$trip_write_member_no'";
        $find_write_member_result = mysqli_query($con,$find_write_member_sql);
        $find_write_member_row = mysqli_fetch_assoc($find_write_member_result);
        $trip_write_member_id = $find_write_member_row['member_id'];
        $trip_write_member_profile_img = $find_write_member_row['member_profile_img'];

        /**
         * 여행 좋아하는 사람 수 세는 코드
         */
        $sql = "SELECT count(*) AS c FROM trip_good WHERE trip_good_trip_no='$trip_no';";
        $result = mysqli_query($con,$sql);
        $row = mysqli_fetch_assoc($result);
        $count_like_member = $row['c'];

        /**
         * 댓글 갯수 세는 코드
         */
        $count_reply_sql = "SELECT count(*) AS c FROM trip_reply WHERE trip_no='$trip_no' AND trip_reply_is_remove=0;";
        $count_reply_result = mysqli_query($con,$count_reply_sql);
        $count_reply_row = mysqli_fetch_assoc($count_reply_result);
        $count_reply = $count_reply_row['c'];
    }

?>
    <div class="container">
        <!-- one trip start -->
        
        <div>
            <div class="blog-entry ftco-animate">
                <div class="text pt-2 mt-5">
                    <h3 class="mb-4"><?php echo $trip_title; ?></h3>
                    <div class="meta-wrap d-md-flex align-items-center">
                        <div class="author mb-4 d-flex align-items-center">
                            <img src="<?php echo $trip_write_member_profile_img;?>" class="rounded-circle" alt="Cinque Terre" width="50"
                                height="50">
                            <!-- <a href="#" class="img" style="background-image: url(images/person_1.jpg);"></a> -->
                            <div class="ml-3 info">
                                <span>Written by</span>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
                                <h3><a href="#"><?php echo $trip_write_member_id; ?></a>,
                                    <span><?php echo $trip_write_time; ?></span></h3>
                            </div>
                        </div>
                        <div class="half order-md-last text-md-right">
                            <p class="meta">
                                <span><i class="icon-heart"></i><span id="trip_good_count"><?php echo $count_like_member; ?></span></span>
                                <span><i class="icon-eye"></i><?php echo $trip_hit_count; ?></span>
                                <span><i class="icon-comment"></i><?php echo $count_reply; ?></span>
                            </p>
                        </div>
                    </div>
                    <br>
                    <p>
                        <img src="<?php echo $trip_representative_img; ?>" width="400px" height="400px">
                    </p><br>

                    <label><strong>일정</strong> : <?php echo $trip_start_day;?> ~
                        <?php echo $trip_finish_day;?></label><br>
                    <label><strong>장소</strong> : <?php echo $trip_location;?></label><br>
                    <label><strong>경로</strong> : <?php echo $trip_route_arr;?></label><br>

                    <br>
                    <label><strong>내 용</strong></label><br>
                    <p class="mb-4"><?php echo $trip_content; ?></p>
                </div>
            </div>


            <!-- 여기에 추천을 만들자 -->
            <?php
            if(isset($_SESSION['login_id'])){
                // 아이디로 멤버 번호 찾기
                $login_id = $_SESSION['login_id'];
                $sql = "SELECT member_no FROM member WHERE member_id='$login_id';";
                $result = mysqli_query($con,$sql);
                $row = mysqli_fetch_assoc($result);
                $member_no = $row['member_no'];
                $sql = "SELECT count(*) AS c FROM trip_good WHERE trip_good_member_no='$member_no' AND trip_good_trip_no='$trip_no';";
                $result = mysqli_query($con,$sql);
                $row = mysqli_fetch_assoc($result);
                $is_already_action = $row['c'];
                if($is_already_action==0){
                    ?>
                    <button class="btn btn-sm btn-secondary" onclick="trip_good_action()">좋아요</button>
                    <?php
                }else{
                    ?>
                    <button class="btn btn-sm btn-primary" onclick="trip_good_action()">좋아요 취소</button>
                    <?php
                }
            }
            ?>
            <div class="col text-right">
                <?php
                    if((isset($_SESSION['login_id']) && $_SESSION['login_id']===$trip_write_member_id) || 
                            (isset($_SESSION['login_id']) && $_SESSION['login_id']==='admin')){
                        ?>
                <a href='trip_update.php?trip_no=<?php echo $_GET['trip_no']; ?>'
                    class='btn btn btn-secondary'>수정</a>

                <a href='#' class='btn btn btn-secondary' data-toggle="modal" data-target="#trip-delete-check-modal">삭제</a>
                <?php
                    }

                    if(isset($_COOKIE['temp_trip'])){ // 쿠키가 있는 경우
                        ?>
                        <a href="#" onclick="go_list();" class="btn btn btn-secondary">목록</a>
                        <?php
                    }else{ // 쿠키가 없는 경우
                        ?>
                        <a href="trip_list.php?trip_type=0&page=1" class="btn btn btn-secondary">목록</a>
                        <?php
                    }
                ?>
                <!-- <a href="#" onclick="go_list();" class="btn btn btn-secondary">목록</a> -->
                <script>
                    /**
                        목록으로 가는 함수
                     */
                    function go_list(){
                        var name = 'temp_trip';
                        /**
                            temp_trip 쿠키 읽기
                         */
                        var value = '';
                        var cookies = document.cookie.split(';');
                        for(var i in cookies){
                            if(cookies[i].search(name)!=-1){
                                value = decodeURIComponent(cookies[i].replace(name+'=',''));
                            }
                        }
                        var values = value.split('/');

                        var trip_type = values[0].trim();
                        var search_location = values[1];
                        var page = values[2];
                        var trip_no = values[3];
                        var current_trip_no = <?php echo $_GET['trip_no']; ?>;
                        if(trip_no!=current_trip_no){
                            window.location.replace('trip_list.php?trip_type=0&page=1');
                        }else{
                            if(search_location==''){ // 검색어가 없는 경우
                                window.location.replace('trip_list.php?trip_type='+trip_type+'&page='+page);
                            }else{ // 검색어가 있는 경우
                                window.location.replace('trip_list.php?trip_type='+trip_type+'&search_location='+search_location+'&page='+page);
                            }
                        }

                        /**
                            temp_trip 쿠키 삭제
                         */
                        var date = new Date();
                        date.setDate(date.getDate()-1);

                        var willCookie ='';
                        willCookie += name + '=remove;';
                        willCookie +='expires='+date.toUTCString();

                        document.cookie = willCookie;
                    }
                    
                </script>
                
            </div>
        </div>
        <script>
        function trip_good_action(){
            // 좋아요 버튼을 누름
            // alert('좋아요');
            var login_id = '<?php echo $_SESSION['login_id'];?>';
            var trip_no = <?php echo $_GET['trip_no'];?>;
            $.ajax({
                type: 'post',
                dataType: 'text',
                url: 'ajax.php',
                data: {mode:'trip_good_action', login_id:login_id, trip_no},
                success: function(data){
                    console.log("data: "+data);
                    if(data==-1){
                        // 이미 좋아요 함
                        alert('좋아요를 취소하였습니다');
                        location.reload();
                        // document.getElementById('trip_good_heart_count').innerHTML = document.getElementById('trip_good_heart_count').innerHTML-1;
                    }else if(data==0){
                        // 좋아요 누름
                        // alert('여행글에 좋아요를 눌렀습니다');
                        location.reload();
                        // document.getElementById('trip_good_heart_count').innerHTML = document.getElementById('trip_good_heart_count').innerHTML+1;
                    }
                    
                },
                error: function(request,status,error){
                    console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
                }
            });
        }
        </script>

        <hr>
        <?php
            include('dbcon.php');
            $login_id = $_SESSION['login_id'];
            $sql = "select member_no from member where member_id='$login_id';";
            $result = mysqli_query($con,$sql);
            $row = mysqli_fetch_assoc($result);
            $login_member_no = $row['member_no'];
            mysqli_close($con);
        ?>
        <script>
            $(document).ready(function() {
                $('#trip_reply_content').on('keyup', function() {
                    $('#counter').html("("+$(this).val().length+" / 최대 200자)");    //글자수 실시간 카운팅
                    if($(this).val().length > 200) {
                        $(this).val($(this).val().substring(0, 200));
                        alert("최대 200자까지 입력 가능합니다.");
                        $(this).val(content.substring(0, 200));
                        $('#counter').html("(200 / 최대 200자)");
                    }
                });
            });
        </script>
        <form name="trip_reply_write_form" id="trip_reply_write_form" role="form" method="post" action="trip_detail.php">
            <div class="mb-3">
                <label for="content">댓글 작성</label>&nbsp;&nbsp;<span style="color:#aaa;" id="counter">(0 / 최대 200자)</span>
                <textarea class="form-control" rows="5" name="trip_reply_content" id="trip_reply_content"></textarea>
            </div>
            <input type="hidden" id="trip_no" name="trip_no" value="<?php echo $trip_no; ?>"/>
            <input type="hidden" id="trip_reply_write_member_no" name="trip_reply_write_member_no" value="<?php echo $login_member_no; ?>"/>
            <input type="hidden" id="trip_reply_write_action" name="trip_reply_write_action" value="true"/>
        </form> 
        <p class="text-right">
            <a href="#" class="btn btn-sm btn-secondary" onclick="tripReplyWrite()">댓글 등록</a>
        </p>
        <hr>
        <?php
         include('dbcon.php');
         $sql = "SELECT * FROM trip_reply WHERE trip_reply_is_remove=0 AND trip_no='$trip_no' ORDER BY trip_reply_no DESC;";
         $result = mysqli_query($con,$sql);
         if(mysqli_num_rows($result)>0){
             ?>
            <br>
            <h2>댓 글</h2>
            <br><br>
        <?php
             while($trip_reply = mysqli_fetch_array($result)){
                 // 댓글 하나씩 가져오기
                 $trip_reply_no = $trip_reply['trip_reply_no'];
                 $trip_no = $trip_reply['trip_no'];
                 $trip_reply_write_member_no = $trip_reply['trip_reply_write_member_no'];
                 $trip_reply_content = $trip_reply['trip_reply_content'];
                 $trip_reply_content = nl2br($trip_reply_content); // /n 을 <br> 로 치환
                 $trip_reply_write_time = $trip_reply['trip_reply_write_time'];
                 // 멤버 번호로 멤버 아이디 찾기
                 $find_write_member_sql = "select * from member where member_no='$trip_reply_write_member_no'";
                 $find_write_member_result = mysqli_query($con,$find_write_member_sql);
                 $find_write_member_row = mysqli_fetch_array($find_write_member_result);
                 $member_id = $find_write_member_row['member_id'];
                 $member_profile_img = $find_write_member_row['member_profile_img'];
                 ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th><span>
                            <img src="<?php echo $member_profile_img;?>" class="rounded-circle" alt="Cinque Terre" width="30" height="30">

                            </span><span class="text">&nbsp;&nbsp;<?php echo $member_id; ?></span></th>
                            <th class="text-right"><?php echo $trip_reply_write_time; ?></th>
                            
                        </tr>
                        
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2"><?php echo $trip_reply_content; ?></td>
                        </tr>
                        <tr>
                        <?php
                        if($member_id==$_SESSION['login_id'] ||'admin'==$_SESSION['login_id']){
                            ?>
                                <td class="text-right" colspan="2">
                                    <form name="trip_reply_delete_form_<?php echo $trip_reply_no;?>" id="trip_reply_delete_form_<?php echo $trip_reply_no;?>" role="form" method="post"
                                        action="<?php echo $_SERVER['PHP_SELF'];?>">
                                        <input type="hidden" name="trip_no" value="<?php echo $trip_no; ?>" />
                                        <input type="hidden" name="trip_reply_no" id="trip_reply_no" value="<?php echo $trip_reply_no; ?>" />
                                        <input type="hidden" name="trip_reply_delete_action" value="true" />
                                        <button type="submit" class="btn btn-sm btn-danger" >삭제</button>
                                    </form>                                    
                                    
                                </td>


                                <?php
                        }
                        ?>
                        </tr>
                    </tbody>
                </table>
                <br>
            <?php
             }
         }else{
             // 댓글글이 없음
             ?>
             <br>
            <h2>댓 글 : 없 음</h2>
            <br>
            
             <?php
         }
         mysqli_close($con);
        ?>
        <br>


    </div><!-- ㅁㅁ-->


    <!-- footer 삽입 -->
    <?php include_once("footer.php");?>

    <!-- loader 삽입 -->
    <?php include_once("loader.php");?>

</body>

<!-- The Modal -->

<div class="modal fade" id="trip-delete-check-modal">
    <div class="modal-dialog">
        <!-- <div class="modal-dialog modal-lg"> -->
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h2 class="modal-title">Traveler</h2>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form name="trip_delete_form" id="trip_delete_form" role="form" method="post"
                    action="<?php echo $_SERVER['PHP_SELF'];?>">
                    <h4>정말로 삭제하시겠어요?</h4>
                    <input type="hidden" name="trip_no" value="<?php echo $trip_no; ?>" />
                    <input type="hidden" name="trip_delete_action" value="true" />
                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <p>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-toggle="modal"
                        data-target="#">취소</button><br />
                </p>
                <p>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="tripDelete()">삭제</button>
                </p>
                
            </div>

        </div>
    </div>
</div>

</html>