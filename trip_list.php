<?php session_start(); 
/**
 * temp_trip 쿠키 삭제
 */
setcookie("temp_trip","",time()-3600);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header 삽입 -->
    <?php include_once("header.php");?>
</head>

<script>
    $(function(){
        /** */
        $('#search_location').keyup(function(){ // 실시간 추천 검색어
            var search_spot = $('#search_location').val();
            if(search_spot.length!=0){ // 검색어가 있는 경우
                $('#recommend_div').css('display',''); // 검색 가능한 장소를 보여준다
                $.ajax({ // 검색어와 일치하는 장소를 알려준다
                    type: 'post',
                    dataType: 'text',
                    url: 'ajax.php',
                    data: {mode:'search_recommend_check', search_spot:search_spot},
                   success: function(data){
                        console.log("data: "+data);
                        if(data!="-1"){ // 검색어와 일치하는 지역이 있는 경우
                            document.getElementById('recommend_p').innerHTML="[검색 가능장소]<br>";
                            var recommend_spot_arr=data.split(',');
                            recommend_spot_arr.forEach(function(element){
                                document.getElementById('recommend_p').innerHTML = document.getElementById('recommend_p').innerHTML+"<br> #"+element;
                            });
                        }
                    },
                    error: function(request,status,error){
                        console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
                    }
                });
            }else{ // 검색어가 없는 경우
                $('#recommend_div').css('display','none');
                document.getElementById('recommend_p').innerHTML="[검색 가능장소]<br>";
            }
        });
    });
</script>

<?php        
if($_SERVER["REQUEST_METHOD"] == "GET"){ // get 요청인 경우
    $current_page=$_SERVER['PHP_SELF'];
    
    if(isset($_GET['search_location'])){ // search_location get 요청이 있는 경우
        $is_name=0; // 검색 가능한 검색어인지 확인하기위한 변수 (0: 검색 불가, 1: 검색 가능)
        $search_spot = $_GET['search_location'];
        if($search_spot==""){ // 검색어가 없는 경우
            // $is_name++;
            echo "<script>window.location.replace('trip_list.php?trip_type=0');</script>";
            exit;
        }else{ // 검색어가 있는 경우
            include('dbcon.php');
            $sql = "SELECT spot_city, spot_name FROM spot;";
            $result = mysqli_query($con,$sql);
            if(mysqli_num_rows($result)>0){
                // 일단 모든 지역 이름이 다르다는 가정으로 로직을 짜자
                while($row = mysqli_fetch_array($result)){
                    $spot_city = $row['spot_city'];
                    $spot_name = $row['spot_name'];
                    if($search_spot==$spot_name){
                        $is_name++;
                        // 검색어가 시/도와 같은 경우에는 검색어 그대로를 저장, 시/도와 다른 경우에는 검색어에 시/도를 포함해 저장
                        if($spot_city==$spot_name) $search_word = $spot_name;
                        else $search_word = '['.$spot_city.'] '.$spot_name; 
                    }
                }
            }
            if($is_name==0){ // 검색 가능한 지역이 없는 경우
                echo "<script>alert('검색 가능한 장소를 입력해주세요');</script>";
                echo "<script>history.back();</script>";
            }
        }
    }
  
} // get 요청시 끝

?>

<body>
    
<!-- nav 삽입 -->
<?php include_once("nav.php");?>
<section class="ftco-section">
        <div class="container">
            <div class="row">
                <!-- 여행 리스트 화면 -->
                <div class="col-lg-8">
                    <div class="row justify-content-center mb-5">
                        <div class="col-md-12 heading-section text-center ftco-animate">
                            <h2 class="mb-1">여행 이야기</h2>
                            <span class="d-block mb-4">Traveler</span>
                            <p>우리의 여행을 나누어 보아요</p>
                            <br>
                            <ul class="nav nav-tabs nav-justified">
                                <?php
                                if($_GET['search_location']){ // 검색어가 있는 경우
                                    $search_location = $_GET['search_location'];
                                    if($_GET['trip_type']==0){ // 전체
                                        ?>
                                        <li class="nav-item">
                                        <a class="nav-link active" id="trip_all" href="trip_list.php?trip_type=0&search_location=<?php echo $search_location; ?>">전체</a>
                                        </li>
                                        <li class="nav-item">
                                        <a class="nav-link" id="trip_review" href="trip_list.php?trip_type=1&search_location=<?php echo $search_location; ?>">여행 후기</a>
                                        </li>
                                        <li class="nav-item">
                                        <a class="nav-link" id="trip_plan" href="trip_list.php?trip_type=2&search_location=<?php echo $search_location; ?>">여행 계획</a>
                                        </li>
                                        <?php
                                    }elseif($_GET['trip_type']==1){ // 여행 후기
                                        ?>
                                        <li class="nav-item">
                                        <a class="nav-link" id="trip_all" href="trip_list.php?trip_type=0&search_location=<?php echo $search_location; ?>">전체</a>
                                        </li>
                                        <li class="nav-item">
                                        <a class="nav-link active" id="trip_review" href="trip_list.php?trip_type=1&search_location=<?php echo $search_location; ?>">여행 후기</a>
                                        </li>
                                        <li class="nav-item">
                                        <a class="nav-link" id="trip_plan" href="trip_list.php?trip_type=2&search_location=<?php echo $search_location; ?>">여행 계획</a>
                                        </li>
                                        <?php
                                    }elseif($_GET['trip_type']==2){ // 여행 계획
                                        ?>
                                        <li class="nav-item">
                                        <a class="nav-link" id="trip_all" href="trip_list.php?trip_type=0&search_location=<?php echo $search_location; ?>">전체</a>
                                        </li>
                                        <li class="nav-item">
                                        <a class="nav-link" id="trip_review" href="trip_list.php?trip_type=1&search_location=<?php echo $search_location; ?>">여행 후기</a>
                                        </li>
                                        <li class="nav-item">
                                        <a class="nav-link active" id="trip_plan" href="trip_list.php?trip_type=2&search_location=<?php echo $search_location; ?>">여행 계획</a>
                                        </li>
                                        <?php
                                    }
                                }else{ // 검색어가 없는 경우

                                    if($_GET['trip_type']==0){ // 전체
                                        ?>
                                        <li class="nav-item">
                                        <a class="nav-link active" id="trip_all" href="trip_list.php?trip_type=0">전체</a>
                                        </li>
                                        <li class="nav-item">
                                        <a class="nav-link" id="trip_review" href="trip_list.php?trip_type=1">여행 후기</a>
                                        </li>
                                        <li class="nav-item">
                                        <a class="nav-link" id="trip_plan" href="trip_list.php?trip_type=2">여행 계획</a>
                                        </li>
                                        <?php
                                    }elseif($_GET['trip_type']==1){ // 여행 후기
                                        ?>
                                        <li class="nav-item">
                                        <a class="nav-link" id="trip_all" href="trip_list.php?trip_type=0">전체</a>
                                        </li>
                                        <li class="nav-item">
                                        <a class="nav-link active" id="trip_review" href="trip_list.php?trip_type=1">여행 후기</a>
                                        </li>
                                        <li class="nav-item">
                                        <a class="nav-link" id="trip_plan" href="trip_list.php?trip_type=2">여행 계획</a>
                                        </li>
                                        <?php
                                    }elseif($_GET['trip_type']==2){ // 여행 계획
                                        ?>
                                        <li class="nav-item">
                                        <a class="nav-link" id="trip_all" href="trip_list.php?trip_type=0">전체</a>
                                        </li>
                                        <li class="nav-item">
                                        <a class="nav-link" id="trip_review" href="trip_list.php?trip_type=1">여행 후기</a>
                                        </li>
                                        <li class="nav-item">
                                        <a class="nav-link active" id="trip_plan" href="trip_list.php?trip_type=2">여행 계획</a>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <!-- trip list start -->
                    <div class="row">                       
                        <?php

                        /**
                         * << 페이징을 하는 코드 >>
                         * 
                         * $page (현재 페이지) : get요청으로 알수있음
                         * $block (현재 블럭) = ceil($page/$block_set);
                         * $page_set (한 페이지 줄 수) = 10;
                         * $block_set (한 페이지 블럭 수) = 5;
                         * $total_idx (전체 데이터 수) : 디비에 접근하여 알 수 있음
                         * $total_page (전체 페이지) = ceil($total/$page_set);
                         * $total_block (전체 블럭) = ceil($total_page/$block_set);
                         * $start_idx (현재 페이지에서 시작하는 데이터 번호) = ($page - 1)*$page_set;
                         * $prev_block (이전 블럭) = $block-1;
                         * $prev_block_page (이전 블럭 첫 페이지) = ($prev_block-1)*$block_set+1;
                         * $next_block (다음 블럭) = $block+1;
                         * $next_block_page (다음 블럭 첫 페이지) = ($next_block-1)*$block_set+1;
                         * $first_page (해당 블럭에서 첫번째 페이지번호) = (($block - 1) * $block_set) + 1;
                         * $last_page (해당 블럭에서 마지막 페이지 번호) = min ($total_page, $block * $block_set);
                         */

                        $page_set = 3; // 한페이지 줄수
                        $block_set = 5; // 한페이지 블럭수
                        include('dbcon.php');
                        $total_page=0;
                        $block=0;
                        $last_page_data_count=0; // 마지막 페이지 데이터 수

                        if(!empty($_GET['search_location'])){ // 검색어가 있는 경우
                            if($_GET['trip_type']==0){ // 전체                                
                                $sql = "SELECT count(*) c FROM trip WHERE trip_location='$search_word' AND trip_is_remove=0;";
                                $result = mysqli_query($con,$sql);
                                $row = mysqli_fetch_assoc($result);
                                $total = $row['c'];

                                $total_page=ceil($total/$page_set);
                                $total_block=ceil($total_page/$block_set);
                                $last_page_data_count = $total % $page_set; // 마지막 페이지 데이터 수 구하기

                                $page = $_GET['page'];
                                if(!$page) $page=1;

                                $block=ceil($page/$block_set); 
                                $start_idx=($page-1)*$page_set;

                                $sql = "SELECT * FROM trip WHERE trip_is_remove=0 AND trip_location='$search_word' ORDER BY trip_no DESC LIMIT $start_idx,$page_set;";
                                
                            }elseif($_GET['trip_type']==1){ // 후기
                                $sql = "SELECT count(*) c FROM trip WHERE trip_type=1 AND trip_location='$search_word' AND trip_is_remove=0;";
                                $result = mysqli_query($con,$sql);
                                $row = mysqli_fetch_assoc($result);
                                $total = $row['c'];

                                $total_page=ceil($total/$page_set);
                                $total_block=ceil($total_page/$block_set);
                                $last_page_data_count = $total % $page_set; // 마지막 페이지 데이터 수 구하기

                                $page = $_GET['page'];
                                if(!$page) $page=1;

                                $block=ceil($page/$block_set); 
                                $start_idx=($page-1)*$page_set;
                                $sql = "SELECT * FROM trip WHERE trip_type=1 AND trip_is_remove=0 AND trip_location='$search_word' ORDER BY trip_no DESC LIMIT $start_idx,$page_set;";
                            }elseif($_GET['trip_type']==2){ // 계획
                                $sql = "SELECT count(*) c FROM trip WHERE trip_type=2 AND trip_location='$search_word' AND trip_is_remove=0;";
                                $result = mysqli_query($con,$sql);
                                $row = mysqli_fetch_assoc($result);
                                $total = $row['c'];

                                $total_page=ceil($total/$page_set);
                                $total_block=ceil($total_page/$block_set);
                                $last_page_data_count = $total % $page_set; // 마지막 페이지 데이터 수 구하기

                                $page = $_GET['page'];
                                if(!$page) $page=1;

                                $block=ceil($page/$block_set); 
                                $start_idx=($page-1)*$page_set;
                                $sql = "SELECT * FROM trip WHERE trip_type=2 AND trip_is_remove=0 AND trip_location='$search_word' ORDER BY trip_no DESC LIMIT $start_idx,$page_set;";
                            }
                        }else{ // 검색어가 없는 경우
                            if($_GET['trip_type']==0){ // 전체
                                $sql = "SELECT count(*) c FROM trip WHERE trip_is_remove=0;";
                                $result = mysqli_query($con,$sql);
                                $row = mysqli_fetch_assoc($result);
                                $total = $row['c'];

                                $total_page=ceil($total/$page_set);
                                $total_block=ceil($total_page/$block_set);
                                $last_page_data_count = $total % $page_set; // 마지막 페이지 데이터 수 구하기

                                $page = $_GET['page'];
                                if(!$page) $page=1;

                                $block=ceil($page/$block_set); 
                                $start_idx=($page-1)*$page_set;
                                // $sql = "SELECT * FROM trip WHERE trip_type=2 AND trip_is_remove=0 AND trip_location='$search_word' ORDER BY trip_no DESC LIMIT $start_idx,$page_set;";
                                $sql = "SELECT * FROM trip WHERE trip_is_remove=0 ORDER BY trip_no DESC LIMIT $start_idx,$page_set;";
                            }elseif($_GET['trip_type']==1){ // 후기
                                $sql = "SELECT count(*) c FROM trip WHERE trip_type=1 AND trip_is_remove=0;";
                                $result = mysqli_query($con,$sql);
                                $row = mysqli_fetch_assoc($result);
                                $total = $row['c'];

                                $total_page=ceil($total/$page_set);
                                $total_block=ceil($total_page/$block_set);
                                $last_page_data_count = $total % $page_set; // 마지막 페이지 데이터 수 구하기

                                $page = $_GET['page'];
                                if(!$page) $page=1;

                                $block=ceil($page/$block_set); 
                                $start_idx=($page-1)*$page_set;
                                $sql = "SELECT * FROM trip WHERE trip_type=1 AND trip_is_remove=0 ORDER BY trip_no DESC LIMIT $start_idx,$page_set;";
                            }elseif($_GET['trip_type']==2){ // 계획
                                $sql = "SELECT count(*) c FROM trip WHERE trip_type=2 AND trip_is_remove=0;";
                                $result = mysqli_query($con,$sql);
                                $row = mysqli_fetch_assoc($result);
                                $total = $row['c'];

                                $total_page=ceil($total/$page_set);
                                $total_block=ceil($total_page/$block_set);
                                $last_page_data_count = $total % $page_set; // 마지막 페이지 데이터 수 구하기

                                $page = $_GET['page'];
                                if(!$page) $page=1;

                                $block=ceil($page/$block_set); 
                                $start_idx=($page-1)*$page_set;

                                $sql = "SELECT * FROM trip WHERE trip_type=2 AND trip_is_remove=0 ORDER BY trip_no DESC LIMIT $start_idx,$page_set;";
                            }
                        }
                        
                        $result = mysqli_query($con,$sql);
                        if(mysqli_num_rows($result)>0){
                            while($trip = mysqli_fetch_array($result)){
                                /**
                                 * 디비에서 여행 정보 가지고오기
                                 */
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
                                
                                /**
                                 * 디비에서 작성자 id, 프로필사진 찾기
                                 */
                                $find_write_member_sql = "select * from member where member_no='$trip_write_member_no'";
                                $find_write_member_result = mysqli_query($con,$find_write_member_sql);
                                $find_write_member_row = mysqli_fetch_array($find_write_member_result);
                                $member_no = $find_write_member_row['member_no'];
                                $member_id = $find_write_member_row['member_id'];
                                $member_profile_img = $find_write_member_row['member_profile_img'];

                                /**
                                 * 이 여행을 좋아하는 사람들 숫자 세기
                                 */
                                $count_like_member_sql = "SELECT count(*) AS c FROM trip_good WHERE trip_good_trip_no='$trip_no';";
                                $count_like_member_result = mysqli_query($con,$count_like_member_sql);
                                $count_like_member_row = mysqli_fetch_assoc($count_like_member_result);
                                $count_like_member = $count_like_member_row['c'];

                                /**
                                 * 댓글 갯수 세기
                                 */
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
                                                <h3><a href="member.php?member_no=<?php echo $member_no; ?>"><?php echo $member_id; ?></a>, <span><?php echo $trip_write_time; ?></span></h3>
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
                                    <img src="<?php echo $trip_representative_img; ?>" width="85%" > <br>
                                    <br>
                                    <script>
                                        function tempTripSetCookie(name,value,day){
                                            var date = new Date();
                                            date.setDate(date.getDate()+day);
    
                                            var willCookie ='';
                                            willCookie += name + '=' + encodeURIComponent(value) +';';
                                            willCookie +='expires='+date.toUTCString()+'';
    
                                            document.cookie = willCookie;
                                        }
                                    </script>
                                    
                                    <?php $value = $_GET['trip_type'].'/'.$_GET['search_location'].'/'.$page.'/'.$trip_no.'/'.$total_page.'/'.$last_page_data_count ?>
                                    <h3 class="mb-4">
                                    <a href="trip_detail.php?trip_no=<?php echo $trip_no;?>" onclick="
                                    tempTripSetCookie('temp_trip','<?php echo $value;?>',1);
                                    "><?php echo $trip_title;?></a></h3>
                                    <p class="mb-4">장소 : <?php echo $trip_location; ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- one trip end -->
                                <?php
                            }
                        }else{
                            // 여행 글이 없음
                            if($page==1){
                                echo "<p class='text-center'><br><br><br><br><br><br><br><h4>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        없 음</h4></p>";
                            }else{
                                echo "<script>alert('없는 페이지 입니다');</script>";
                                echo "<script>history.back();</script>";
                                exit;
                            }
                            
                        }
                        mysqli_close($con);
                        ?>
                        <br><br>
                        
                    </div>
                    <!-- trip list end -->
                    <div >    
                        <ul class="pagination justify-content-center">
                            <?php
                            /**
                             * 블럭 설정
                             */
                            $trip_type=$_GET['trip_type'];
                            $first_page = (($block - 1) * $block_set) + 1; // 첫번째 페이지번호
                            $last_page = min ($total_page, $block * $block_set); // 마지막 페이지번호
                            $prev_block = $block -1; // 이전 블럭
                            $prev_block_page = ($prev_block-1)*$block_set+1; // 이전 블럭 페이지
                            $next_block = $block +1; // 다음 블럭
                            $next_block_page = ($next_block-1)*$block_set+1; // 다음 블럭 페이지
                            if(isset($_GET['search_location'])){ // 검색어가 있는 경우
                                $search_location = $_GET['search_location'];
                                echo ($prev_block > 0) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?trip_type=$trip_type&search_location=$search_location&page=1'> << </a></li>" : "";
                                echo ($prev_block > 0) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?trip_type=$trip_type&search_location=$search_location&page=$prev_block_page'><</a></li>" : "";
                                for($i=$first_page; $i<=$last_page;$i++){
                                    echo ($i!=$page) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?trip_type=$trip_type&search_location=$search_location&page=$i'>$i</a></li>" 
                                                        : "<li class='page-item active'><a class='page-link' href='#'>$i</a></li>";
                                }
                                echo ($next_block <= $total_block) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?trip_type=$trip_type&search_location=$search_location&page=$next_block_page'>></a></li>" : "";
                                echo ($next_block <= $total_block) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?trip_type=$trip_type&search_location=$search_location&page=$total_page'> >> </a></li>" : "";
                            }else{ // 검색어가 없는 경우
                                echo ($prev_block > 0) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?trip_type=$trip_type&page=1'> << </a></li>" : "";
                                echo ($prev_block > 0) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?trip_type=$trip_type&page=$prev_block_page'><</a></li>" : "";
                                for($i=$first_page; $i<=$last_page;$i++){
                                    echo ($i!=$page) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?trip_type=$trip_type&page=$i'>$i</a></li>" 
                                                        : "<li class='page-item active'><a class='page-link' href='#'>$i</a></li>";
                                }
                                echo ($next_block <= $total_block) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?trip_type=$trip_type&page=$next_block_page'>></a></li>" : "";
                                echo ($next_block <= $total_block) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?trip_type=$trip_type&page=$total_page'> >> </a></li>" : "";
                            }
                            ?>
                        </ul>
                    </div>
                </div><!-- END-->

                <!-- 사이드 화면 --> 
                <div class="col-lg-4 sidebar ftco-animate">
                    <!-- 검색 창 -->
                    <div class="sidebar-box">
                        <form action="trip_list.php" class="search-form" >
                            <div class="form-group">
                                <span class="icon icon-search"></span>
                                <input type="hidden" name="trip_type" value="0">
                                <input maxlength="10" type="text" id="search_location" name="search_location" class="form-control" placeholder="장소를 검색해주세요" value="<?php echo $search_word;?>" autocomplete="off">
                            </div>
                        </form>
                    </div>

                    <!-- 검색어 추천 창 -->
                    <div id="recommend_div" class="sidebar-box" style="border:1px solid; display:none">
                        <p id="recommend_p" style="font-weight:bold;">
                            [검색 가능 장소]<br>
                        </p>
                    </div>
                    
                    <!-- 베스트 여행 글 -->
                    <!-- ############## -->
                    <div class="sidebar-box ftco-animate">
                    <!-- <div class="sidebar-box"> -->
                        <h3>베스트 여행글</h3>
                        <ul class="categories">
                            <?php
                            include('dbcon.php');
                            $sql = "SELECT trip_no, trip_title, count(trip_good.trip_good_trip_no) count_like_member
                                    FROM trip
                                    LEFT JOIN trip_good
                                    ON trip.trip_no=trip_good.trip_good_trip_no
                                    WHERE trip_is_remove=0
                                    GROUP BY trip.trip_no
                                    ORDER BY count_like_member DESC, trip.trip_no DESC;";
                            $result = mysqli_query($con,$sql);
                            if(mysqli_num_rows($result)>0){
                                $i = 0;
                                while($row = mysqli_fetch_array($result)){
                                    $i++;
                                    if($i==7){
                                        break;
                                    }
                                    $trip_no = $row['trip_no'];
                                    $trip_title = $row['trip_title'];
                                    $trip_count_like_member = $row['count_like_member'];
                                    ?>
                                    <?php
                                    if(mb_strlen($trip_title,'utf-8')>15){
                                        ?>
                                        <li><a href="trip_detail.php?trip_no=<?php echo $trip_no;?>"><?php echo mb_substr($trip_title,0,15).'...'; ?> <span><i class="icon-heart"></i>&nbsp;&nbsp;<?php echo $trip_count_like_member; ?></span></a></li>
                                        <?php
                                    }else{
                                        ?>
                                        <li><a href="trip_detail.php?trip_no=<?php echo $trip_no;?>"><?php echo $trip_title; ?> <span><i class="icon-heart"></i>&nbsp;&nbsp;<?php echo $trip_count_like_member; ?></span></a></li>
                                        <?php
                                    }
                                    ?>
                                    
                                    <?php
                                }
                            }else{
                                // 여행 글이 없음
                            }
                            mysqli_close($con);
                            
                            ?>
                            <!-- <li><span href="#">제목 <span>(00)</span></span></li> -->
						
                        </ul>
                    </div>
                    <!-- ############## -->

                    <!-- ############## -->
                    <!-- 최근 본 여행 글 -->
                    <div class="sidebar-box ftco-animate">
                        <h3>최근 본 여행</h3>
                        <?php
                        if(isset($_SESSION['login_id'])){
                            $value = $_COOKIE[$_SESSION['login_id'].'_read_trip'];
                        }else{
                            $value = $_COOKIE['no_login_read_trip'];
                        }
                        $read_trip_arr = explode("|",$value);
                        include('dbcon.php');
                        if(count($read_trip_arr)==1 && $read_trip_arr[0]==''){
                            ?>
                            <p class="text-center">없 음</p>
                            <?php
                        }else{

                            $i=0;
                            foreach($read_trip_arr as $read_trip){
                                $i++;
                                if($i==11){
                                    break;
                                }
                                // $sql = "SELECT * FROM trip WHERE trip_no='$read_trip' AND trip_is_remove=0;";
                                $sql = "SELECT * FROM trip WHERE trip_no='$read_trip';";
                                $result = mysqli_query($con,$sql);
                                $row = mysqli_fetch_assoc($result);
                                $trip_is_remove = $row['trip_is_remove'];
                                if($trip_is_remove==1){
                                    continue;
                                }
                                $trip_no = $row['trip_no'];
                                $trip_representative_img = $row['trip_representative_img'];
                                $trip_title = $row['trip_title'];
                                $trip_write_time = $row['trip_write_time'];
                                $trip_write_member_no = $row['trip_write_member_no'];
    
                                $sql = "SELECT member_id FROM member WHERE member_no='$trip_write_member_no';";
                                $result = mysqli_query($con,$sql);
                                $row = mysqli_fetch_assoc($result);
                                $trip_write_member_id = $row['member_id'];
                                
                                ?>
                                <div class="block-21 mb-4 d-flex">
                                    <img src="<?php echo $trip_representative_img; ?>" width="100px" style="margin:10px;"> <br>
                                    <div class="text">
                                        <h3 class="heading"><a href="trip_detail.php?trip_no=<?php echo $trip_no;?>"><?php echo $trip_title; ?></a></h3>
                                        <div class="meta">
                                            <div><span class="icon-calendar"></span> <?php echo $trip_write_time; ?></div>
                                            <div><span class="icon-person"></span> <?php echo $trip_write_member_id; ?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        mysqli_close($con);
                        ?>

                    </div><!-- END COL -->
                    <!-- ############## -->
                    
                </div>
            </div>
        </div>
    </section>

<!-- footer 삽입 -->
<?php include_once("footer.php");?>

<!-- loader 삽입 -->
<?php include_once("loader.php");?>    
</body>

</html>
