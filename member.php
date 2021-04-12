<?php session_start();  ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header 삽입 -->
    <?php include_once("header.php");?>
</head>
<body>
<!-- nav 삽입 -->
<?php include_once("nav.php");?>

<?php
if(isset($_GET['member_no'])){

    

    $member_no = $_GET['member_no'];
    include('dbcon.php');
    $sql = "SELECT count(*) AS c FROM member WHERE member_no='$member_no';";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    mysqli_close($con);
    if($row['c']==0){
        echo "<script>alert('존재하지 않는 멤버입니다');</script>";
        echo "<script>history.back();</script>";
        exit;
    }
}elseif( !isset($_GET['logout_action']) && !isset($_POST['login_action']) && !isset($_POST['join_action'])){
    echo "<script>alert('잘못된 접근입니다');</script>";
    echo "<script>history.back();</script>";
    exit;
}

$member_no = $_GET['member_no'];
include('dbcon.php');
$sql = "SELECT * FROM member WHERE member_no='$member_no';";
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_assoc($result);
$member_no = $row['member_no'];
$member_id = $row['member_id'];

if($member_id==$_SESSION['login_id']){
    echo "<script>window.location.replace('mypage.php?mypage_type=0');</script>";
    exit;
}

$member_pw = $row['member_pw'];
$member_email = $row['member_email'];
$member_introduce = nl2br($row['member_introduce']);
$member_profile_img = $row['member_profile_img'];
$member_name = $row['member_name'];
$member_age = $row['member_age'];
$member_gender = $row['member_gender'];
$member_join_time = $row['member_join_time'];
mysqli_close($con);
?>
<br><br><br><br>

    <div class="container">
        <h2>
        <img src="<?php echo $member_profile_img; ?>" class="rounded-circle" width="70px" height="70px">&nbsp;
        <?php echo $member_id; ?>님 페이지</h2><br>
        <?php
        if($member_introduce==null || $member_introduce==''){
            
        }else{
            echo "<p><strong>소개</strong><br><br>$member_introduce</p>";
        }
        ?>
        <br>
        <ul class="nav nav-tabs">
            
            <li class="nav-item">
            <?php
                if($_GET['memberpage_type']==2){
                    echo "<a class='nav-link active' href='memberpage.php?member_no=$member_no&memberpage_type=2'>작성한 여행 글</a>";
                }else{
                    echo "<a class='nav-link' href='member.php?member_no=$member_no&memberpage_type=2'>작성한 여행 글</a>";
                }
                ?>
            </li>
            <li class="nav-item">
            <?php
                if($_GET['memberpage_type']==3){
                    echo "<a class='nav-link active' href='member.php?member_no=$member_no&memberpage_type=3'>좋아하는 여행 글</a>";
                }else{
                    echo "<a class='nav-link' href='member.php?member_no=$member_no&memberpage_type=3'>좋아하는 여행 글</a>";
                }
                ?>
            </li>
        </ul><br><br>

        <?php 
        if($_GET['memberpage_type']==2){
        // 내가 작성한 여행 글
        include('dbcon.php');
        // $sql = "SELECT * FROM trip WHERE trip_is_remove=0 ORDER BY trip_no DESC;";
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
        }elseif($_GET['memberpage_type']==3){
        // 내가 좋아하는 여행 글
        include('dbcon.php');
        $sql = "SELECT * FROM trip_good WHERE trip_good_member_no='$member_no';";
        $result = mysqli_query($con,$sql);
        if(mysqli_num_rows($result)>0){
            while($row = mysqli_fetch_array($result)){
                $trip_no = $row['trip_good_trip_no'];

                // $find_trip_sql = "SELECT * FROM trip WHERE trip_no='$trip_no' AND trip_is_remove=0;";
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