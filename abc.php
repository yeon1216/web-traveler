<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header 삽입 -->
    <?php include_once("header.php");?>
</head>

<script>
    $(function(){
        // 실시간 추천 검색어
        $('#search_location').keyup(function(){
            var search_spot = $('#search_location').val();
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
                            // 검색어와 일치하는 지역이 있는 경우
                            document.getElementById('recommend_p').innerHTML="[검색 가능장소]<br>";
                            var recommend_spot_arr=data.split(',');
                            recommend_spot_arr.forEach(function(element){
                                document.getElementById('recommend_p').innerHTML = document.getElementById('recommend_p').innerHTML+"<br> #"+element;
                            });
                        }else{
                            // 검색어와 일치하는 지역이 없는 경우
                        }
                    },
                    error: function(request,status,error){
                        console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
                    }
                });
            }else{
                $('#recommend_div').css('display','none');
                document.getElementById('recommend_p').innerHTML="[검색 가능장소]<br>";
            }
        });
    });
</script>

<?php        

// get 요청 시작
if($_SERVER["REQUEST_METHOD"] == "GET"){
    $current_page=$_SERVER['PHP_SELF'];
    
    if(empty($_GET['search_location'])){
        // 검색어가 없는 경우
        $search_word="";
    }else{
        // 검색어가 있는 경우
        $search_spot = $_GET['search_location'];
        // 올바른 검색인지 검사좀 해보자
        include('dbcon.php');
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
                        $search_word = $spot_name;
                        // echo $spot_name;
                    }else{
                        $search_word = '['.$spot_city.'] '.$spot_name;
                    }
                }
            }
        }
        if($is_name==0){
            // 불합격
            echo "<script>alert('검색 가능한 장소를 입력해주세요');</script>";
            echo "<script>history.back();</script>";
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
                                if($_GET['search_location']){
                                    $search_location = $_GET['search_location'];
                                    if($_GET['trip_type']==0){
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
                                    }elseif($_GET['trip_type']==1){
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
                                    }elseif($_GET['trip_type']==2){
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
                                }else{

                                    if($_GET['trip_type']==0){
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
                                    }elseif($_GET['trip_type']==1){
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
                                    }elseif($_GET['trip_type']==2){
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
                        include('dbcon.php');
                        if(isset($_GET['search_location'])){
                            // 검색어가 있는 경우
                            if($_GET['trip_type']==0){
                                // 전체
                                $sql = "SELECT * FROM trip WHERE trip_is_remove=0 AND trip_location='$search_word' ORDER BY trip_no DESC;";
                            }elseif($_GET['trip_type']==1){
                                // 후기
                                $sql = "SELECT * FROM trip WHERE trip_type=1 AND trip_is_remove=0 AND trip_location='$search_word' ORDER BY trip_no DESC;";
                            }elseif($_GET['trip_type']==2){
                                // 계획
                                $sql = "SELECT * FROM trip WHERE trip_type=2 AND trip_is_remove=0 AND trip_location='$search_word' ORDER BY trip_no DESC;";
                            }
                        }else{
                            // 검색어가 없는 경우
                            if($_GET['trip_type']==0){
                                // 전체
                                $sql = "SELECT * FROM trip WHERE trip_is_remove=0 ORDER BY trip_no DESC;";
                            }elseif($_GET['trip_type']==1){
                                // 후기
                                $sql = "SELECT * FROM trip WHERE trip_type=1 AND trip_is_remove=0 ORDER BY trip_no DESC;";
                            }elseif($_GET['trip_type']==2){
                                // 계획
                                $sql = "SELECT * FROM trip WHERE trip_type=2 AND trip_is_remove=0 ORDER BY trip_no DESC;";
                            }
                        }
                        
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
                                
                                // 작성자 아이디 찾기
                                $find_write_member_sql = "select member_id from member where member_no='$trip_write_member_no'";
                                $find_write_member_result = mysqli_query($con,$find_write_member_sql);
                                $find_write_member_row = mysqli_fetch_array($find_write_member_result);
                                $member_id = $find_write_member_row['member_id'];

                                // 이 여행을 좋아하는 사람수도 세야함
                                $count_like_member_sql = "SELECT count(*) AS c FROM trip_good WHERE trip_good_trip_no='$trip_no';";
                                $count_like_member_result = mysqli_query($con,$count_like_member_sql);
                                $count_like_member_row = mysqli_fetch_assoc($count_like_member_result);
                                $count_like_member = $count_like_member_row['c'];
                                ?>
                                <!-- one trip start -->
                        <div class="col-md-12">
                            <div class="blog-entry ftco-animate">
                                <div class="text pt-2 mt-5">
                                <div class="meta-wrap d-md-flex align-items-center">
                                        <div class="author mb-4 d-flex align-items-center">
                                            <img src="images/person_1.jpg" class="rounded-circle" alt="Cinque Terre" width="50" height="50">
                                            <!-- <a href="#" class="img" style="background-image: url(images/person_1.jpg);"></a> -->
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
                                                <span><i class="icon-comment"></i>00</span>
                                            </p>
                                        </div>
                                    </div>
                                    <img src="<?php echo $trip_representative_img; ?>" width="85%" > <br>
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
                        ?>
                        <br><br>
                    </div>
                    <!-- trip list end -->

                    <div class="row mt-5">
                        <div class="col text-center">
                            <div class="block-27">
                                <ul>
                                    <li><a href="#">&lt;</a></li>
                                    <li class="active"><span>1</span></li>
                                    <li><a href="#">2</a></li>
                                    <li><a href="#">3</a></li>
                                    <li><a href="#">4</a></li>
                                    <li><a href="#">5</a></li>
                                    <li><a href="#">&gt;</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div><!-- END-->

                <!-- 사이드 화면 --> 
                <div class="col-lg-4 sidebar ftco-animate">
                    <!-- 검색 창 -->
                    <div class="sidebar-box">
                        <form action="trip_list.php" class="search-form" >
                            <div class="form-group">
                                <span class="icon icon-search"></span>
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
                    <!-- <div class="sidebar-box ftco-animate"> -->
                    <div class="sidebar-box">
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
                                while($row = mysqli_fetch_array($result)){
                                    // $row = mysqli_fetch_assoc($result);
                                    $trip_no = $row['trip_no'];
                                    $trip_title = $row['trip_title'];
                                    $trip_count_like_member = $row['count_like_member'];
                                    ?>
                                    <li><a href="#"><?php echo $trip_title; ?> <span>(<?php echo $trip_count_like_member; ?>)</span></a></li>
                                    <?php
                                }
                            }else{
                                // 여행 글이 없음
                            }
                            mysqli_close($con);
                            
                            ?>
                            <!-- <li><a href="#">제목 <span>(00)</span></a></li> -->
						
                        </ul>
                    </div>
                    <!-- ############## -->

                                        <!-- ############## -->
                    <!-- 최근 본 여행 글 -->
                    <div class="sidebar-box">
                        <h3>최근 본 여행</h3>
                        <?php
                        if(isset($_SESSION['login_id'])){
                            $value = $_COOKIE[$_SESSION['login_id'].'_read_trip'];
                        }else{
                            $value = $_COOKIE['no_login_read_trip'];
                        }
                        $read_trip_arr = explode("|",$value);
                        
                        // 데이터베이스 연결
                        $con=mysqli_connect("127.0.0.1","root","1216","traveler"); 
                        // 연결에 실패할 경우 예외처리
                        if(!$con){ die("연결 실패 : ".mysqli_connect_error()); }
                        
                        if(count($read_trip_arr)==1 && $read_trip_arr[0]==''){
                            echo "<p class='text-center'>없 음</p>";
                        }else{
                            $i=0;
                            foreach($read_trip_arr as $read_trip){
                                $i++;
                                if($i==11){
                                    break;
                                }
                                $sql = "SELECT * FROM trip WHERE trip_no='$read_trip';";
                                $result = mysqli_query($con,$sql);
                                $row = mysqli_fetch_assoc($result);
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
                                        <h3 class="heading"><a href="#"><?php echo $trip_title; ?></a></h3>
                                        <div class="meta">
                                            <div><a href="#"><span class="icon-calendar"></span> <?php echo $trip_write_time; ?></a></div>
                                            <div><a href="#"><span class="icon-person"></span> <?php echo $trip_write_member_id; ?></a></div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        mysqli_close($con);
                        ?>

                    </div>

                    </div>
                    <!-- END COL -->
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
