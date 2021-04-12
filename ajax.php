<?php
header("Content-Type:application/json");
  
// 데이터베이스에서 데이터를 가져옴
include('dbcon.php');

// 어떤 요청인지 받기
$mode = $_POST['mode'];

/**
 * 회원가입시 아이디 중복 체크
 */
if(!strcmp($mode,'join_id_check')){
    $join_id = $_POST['join_id'];
    $sql = "SELECT COUNT(*) AS count FROM member WHERE member_id='$join_id';";
    $result = mysqli_query($con,$sql);
    $used_id = mysqli_fetch_assoc($result); // 0: 아이디 사용 가능, 1: 아이디 사용 불가
    echo $used_id['count'];
}

/**
 * 회원가입시 이메일 중복 체크
 */
if(!strcmp($mode,'join_email_check')){
    $join_email = $_POST['join_email'];
    $sql = "SELECT COUNT(*) AS count FROM member WHERE member_email='$join_email';";
    $result = mysqli_query($con,$sql);
    $used_email = mysqli_fetch_assoc($result);
    echo $used_email['count'];
}

/**
 * 여행 시작일 체크
 */
if(!strcmp($mode, 'trip_start_day_check')){
    // 요청 데이터 받기
    $trip_type = $_POST['trip_type'];
    $trip_start_day = $_POST['trip_start_day'];

    // 현재 시간 받기
    date_default_timezone_set("Asia/Seoul");
    $dateStr = date("Y-m-d",time());
    $current_time = strtotime($dateStr." +0 day");
    $trip_start_day_time = strtotime($trip_start_day);

    if($trip_type==1){
        // 여행 후기 선택
        if($current_time>=$trip_start_day_time){
            // 통과
            echo 0;
        }else{
            // 안됨
            echo 1;
        }
    }elseif($trip_type==2){
        // 여행 계획 선택
        if($current_time<=$trip_start_day_time){
            // 통과
            echo 0;
        }else{
            // 안됨
            echo 1;
        }
    }else{
        echo 2;
        // echo "<script>alert('여행 유형을 선택하지 않았습니다');</script>";
    }
}


/**
 * 여행 마지막 일 체크
 */
if(!strcmp($mode,'trip_finish_day_check')){
    
    // 요청 데이터 받기
    $trip_type = $_POST['trip_type'];
    $trip_start_day = $_POST['trip_start_day'];
    $trip_finish_day = $_POST['trip_finish_day'];

    // 현재 시간 받기
    date_default_timezone_set("Asia/Seoul");
    $dateStr = date("Y-m-d",time());
    $current_time = strtotime($dateStr." +0 day");
    

    $trip_start_day_time = strtotime($trip_start_day);
    $trip_finish_day_time = strtotime($trip_finish_day);
    
    if($trip_type==1){
        if($trip_finish_day_time>$current_time){
            echo 2;
            exit;
        }
    }

    if($trip_start_day_time<=$trip_finish_day_time){
        echo 0;
    }else{
        echo 1;
    }
}

/**
 * 검색어 추천
 */
if(!strcmp($mode,'search_recommend_check')){
    // 요청 데이터 받기
    $search_spot = $_POST['search_spot'];
    $sql = "SELECT spot_name FROM spot WHERE spot_name LIKE '%$search_spot%';";
    $result = mysqli_query($con,$sql);
    $recommend_spot='';
    if(mysqli_num_rows($result)>0){
        while($row = mysqli_fetch_array($result)){
            $spot_name = $row['spot_name'];
            if($recommend_spot==''){
                $recommend_spot = $spot_name;
            }else{
                $recommend_spot = $recommend_spot.','.$spot_name;
            }
        }
        echo $recommend_spot;
    }else{
        echo -1;
    }
}



/**
 * 여행 좋아요
 */
if(!strcmp($mode,'trip_good_action')){
    // echo 11111;
    $trip_no = $_POST['trip_no'];
    $login_id=$_POST['login_id'];
    // 아이디로 멤버 번호 찾기
    $sql = "SELECT member_no FROM member WHERE member_id='$login_id';";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $member_no = $row['member_no'];

    
    $sql = "SELECT count(*) AS c FROM trip_good WHERE trip_good_member_no='$member_no' AND trip_good_trip_no='$trip_no';";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $is_already_action = $row['c'];
    // echo '이거는 !!!  '  .$is_already_action;
    if($is_already_action!=0){
        // 이미 좋아요 누름
        $sql = "DELETE FROM trip_good WHERE trip_good_member_no='$member_no' AND trip_good_trip_no='$trip_no';";
        $result = mysqli_query($con,$sql);
        echo -1;
    }else{
        $sql = "INSERT INTO trip_good (trip_good_member_no, trip_good_trip_no)
                VALUES ('$member_no','$trip_no')";
        $result = mysqli_query($con,$sql);
        echo 0;
    }
}

/**
 * 기본 프로필 사진 적용
 */
if(!strcmp($mode,'default_profile_img')){
    $login_id = $_POST['login_id'];
    $sql = "UPDATE member SET member_profile_img='upload/2019-08-19 14:37:19.png' WHERE member_id='$login_id';";
    $result = mysqli_query($con,$sql);
}

/**
 * 자기소개 등록
 */
if(!strcmp($mode,'register_my_introduce')){
    $login_id = $_SESSION['login_id'];
    $introduce_textarea = $_POST['introduce_textarea'];
    $sql = "UPDATE member SET member_introduce='$introduce_textarea' WHERE member_id='$login_id';";
    $result = mysqli_query($con,$sql);
}

/**
 * 오픈 채팅 새로고침을 위한 채팅 갯수 체크
 */
if(!strcmp($mode,'check_chat')){
    $count_chat = $_POST['count_chat'];
    $sql = "SELECT * FROM chat ORDER BY chat_no DESC; ";
    $result = mysqli_query($con,$sql);
    if($count_chat==mysqli_num_rows($result)){
        echo 0;
    }else{
        echo 1;
    }
}

/**
 * 오픈 채팅 작성
 */
if(!strcmp($mode,'chat_write_action')){
    // echo "chat_write_action";
    $chat_write_member_id = $_SESSION['login_id'];
    $chat_content = $_POST['chat_content'];

    /**
     * 채팅 작성자 아이디로 작성자 멤버 번호 찾기
     */
    $sql = "SELECT member_no FROM member WHERE member_id='$chat_write_member_id';";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $chat_write_member_no = $row['member_no'];
    // echo "chat_write_member_id: ".$chat_write_member_id;
    // echo "chat_write_member_no: ".$chat_write_member_no;

    $sql = "INSERT INTO chat (chat_write_member_no, chat_content)
            VALUES ($chat_write_member_no,'$chat_content');";
    $result = mysqli_query($con,$sql);
}

/**
 * 등록가능 장소인지 체크
 */
if(!strcmp($mode,'check_spot')){
    $search_spot = $_POST['search_spot'];
    $sql = "SELECT spot_city, spot_name FROM spot;";
    $result = mysqli_query($con,$sql);
    $is_name=0;
    if(mysqli_num_rows($result)>0){
        // 일단 모든 지역 이름이 다르다는 가정으로 로직을 짜자
        while($row = mysqli_fetch_array($result)){
            $spot_city = $row['spot_city'];
            $spot_name = $row['spot_name'];
            if($search_spot==$spot_name){
                $is_name++;
                if($spot_city==$spot_name){
                    echo $spot_name;
                }else{
                    echo '['.$spot_city.'] '.$spot_name;
                }
            }
        }
    }
    
    if($is_name==0){
        echo -1;
    }
}

/**
 * 관리자의 장소관리(장소 추가, 장소 삭제)를 위한 장소 체크
 */
if(!strcmp($mode,'admin_check_spot')){
    
    $check_spot_city = $_POST['spot_city'];
    $check_spot_name = $_POST['spot_name'];
    $sql = "SELECT spot_city, spot_name FROM spot;";
    $result = mysqli_query($con,$sql);
    $is_name=0;
    if(mysqli_num_rows($result)>0){
        // 일단 모든 지역 이름이 다르다는 가정으로 로직을 짜자
        while($row = mysqli_fetch_array($result)){
            $spot_city = $row['spot_city'];
            $spot_name = $row['spot_name'];
            if($check_spot_name==$spot_name){
                $is_name++;
            }
        }
    }
    
    if($is_name==0){
        // 없는 지역
        // 등록 가능
        // 제거 불가
        echo -1;
    }else{
        // 이미 있는 지역
        // 등록 불가
        // 제거 가능
        echo 0;
    }
}

/**
 * 관리자의 장소관리(장소 추가, 장소 삭제)를 위한 장소 체크
 */
if(!strcmp($mode,'chat_write_action')){
    $chat_write_member_id = $_POST['chat_write_member_id'];
    $chat_content = $_POST['chat_content'];

    $sql = "SELECT member_no FROM member WHERE member_id='$chat_write_member_id';";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $chat_write_member_no = $row['member_no'];

    $sql = "INSERT INTO chat (chat_write_member_no, chat_content)
            VALUES ($chat_write_member_no,'$chat_content');";
    $result = mysqli_query($con,$sql);
}

/**
 * 채팅 더보기
 */
if(!strcmp($mode,'more_chat')){
    $chat_count = $_POST['chat_count'];
    echo $chat_count + 10;
}

mysqli_close($con); // 데이터베이스 접속 종료
?>