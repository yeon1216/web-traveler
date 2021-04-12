<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header 삽입 -->
    <?php include_once("header.php");?>
</head>

<body>

    <!-- nav 삽입 -->
    <?php include_once("nav.php");?>

    <section class="home-slider js-fullheight owl-carousel">
        <div class="slider-item js-fullheight" style="background-image:url(images/web_bg_1.jpg);">
            <div class="overlay"></div>
            <div class="container-fluid">
                <div class="row no-gutters slider-text slider-text-2 js-fullheight align-items-center justify-content-center"
                    data-scrollax-parent="true">
                    <div class="col-md-6 ftco-animate text-center" data-scrollax=" properties: { translateY: '70%' }">
                        <h1 class="mb-4" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">Traveler에
                            방문해주셔서 감사합니다</h1>
                        <p data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">일상을 여행으로라는 슬로건 아래 건전한 여행 문화를
                            공유하자는 목적을 가지고있습니다.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="slider-item js-fullheight" style="background-image:url(images/web_bg_2.jpeg);">
            <div class="overlay"></div>
            <div class="container-fluid">
                <div class="row no-gutters slider-text slider-text-2 js-fullheight align-items-center justify-content-center"
                    data-scrollax-parent="true">
                    <div class="col-md-6 ftco-animate text-center" data-scrollax=" properties: { translateY: '70%' }">
                        <h1 class="mb-4" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">우리들의 여행 이야기를
                            &nbsp;&nbsp;나누어 보아요</h1>
                        <p data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">일상을 여행으로라는 슬로건 아래 건전한 여행 문화를
                            공유하자는 목적을 가지고있습니다.</p>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center mb-5 pb-2">
                <div class="col-md-7 heading-section text-center ftco-animate">
                    <h2 class="mb-4">베스트 여행글</h2>
                </div>
            </div>
            <div class="row">
                <?php
                
                include('dbcon.php');
                $sql = "SELECT trip_no, trip_title, trip_write_member_no, trip_representative_img, count(trip_good.trip_good_trip_no) count_like_member
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
                        $trip_write_member_no = $row['trip_write_member_no'];
                        $trip_representative_img = $row['trip_representative_img'];

                        $find_member_id_sql = "SELECT member_id FROM member WHERE member_no='$trip_write_member_no';";
                        $find_member_id_result = mysqli_query($con,$find_member_id_sql);
                        $find_member_id_row = mysqli_fetch_assoc($find_member_id_result);
                        $trip_write_member_id = $find_member_id_row['member_id'];


                        $trip_count_like_member = $row['count_like_member'];
                        ?>
                        <!-- ########### -->
                        <!-- <div class="col-md-4 ftco-animate" style="background:#ccccccaa;"> -->
                        <div class="col-md-4 ftco-animate">
                            <img src="<?php echo $trip_representative_img; ?>" width="300px" style="margin:10px;"> <br>
                            <div class="text text-center">
                                <a href="trip_detail.php?trip_no=<?php echo $trip_no;?>"> 
                                <?php
                                    if(mb_strlen($trip_title,'utf-8')>15){
                                        ?>
                                        <!-- <li><a href="trip_detail.php?trip_no=<?php echo $trip_no;?>"><?php echo mb_substr($trip_title,0,15).'...'; ?> <span>(<?php echo $trip_count_like_member; ?>)</span></a></li> -->
                                        <h5><?php echo mb_substr($trip_title,0,15).'...'; ?></h5> 
                                        <?php
                                    }else{
                                        ?>
                                        <!-- <li><a href="trip_detail.php?trip_no=<?php echo $trip_no;?>"><?php echo $trip_title; ?> <span>(<?php echo $trip_count_like_member; ?>)</span></a></li> -->
                                        <h5><?php echo $trip_title; ?></h5> 
                                        <?php
                                    }
                                    ?>
                                
                            
                            </a>
                                <span class="icon-person"><span><?php echo $trip_write_member_id; ?></span>
                            </div>                            

                        </div>
                        <!-- ########### -->
                        <?php
                    }
                }else{
                    // 여행 글이 없음
                }
                mysqli_close($con);
                
                
                ?>

            </div>
        </div>
    </section>

<!-- footer 삽입 -->
<?php include_once("footer.php");?>
<!-- loader 삽입 -->
<?php include_once("loader.php");?>

<div style="
      position: absolute;
  bottom: 10px;
  right: 5%;
  width: 500px;
  //border: 3px solid #73AD21;
  //background: #aaaaaabb;
  background: #dddddd;
  margin: 10px;
  padding: 10px;
    ">
    <div class="col text-center" style="margin-bottom:20px;">
        <strong>어떤 여행지가 궁금하세요?</strong>
    </div>
    <div>
        <form method="GET" action="trip_list.php" class="search-form" name="search_form">
            <div class="form-group" >
                <span class="icon icon-search"></span>
                <input type="hidden" name="trip_type" value="0">
                <input id="search_location" name="search_location" type="search" class="form-control" placeholder="장소를 입력해주세요" autocomplete="off" maxlength="10">
            </div>
        </form>
    </div>

    <div id="recommend_div" class="sidebar-box" style="border:1px solid; display:none; background=#ffffff;">
        <p id="recommend_p" style="background=#ffffff; font-weight:bold;">
            [검색 가능 장소]<br>
        </p>
    </div>


</div>

</body>
<script>
    $(function(){
        $('#search_location').keyup(function(){
            var search_spot = $('#search_location').val();
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
                            document.getElementById('recommend_p').innerHTML="[검색 가능장소]<br>";
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
                document.getElementById('recommend_p').innerHTML="[검색 가능장소]<br>";
            }
        });
    });
</script>

</html>