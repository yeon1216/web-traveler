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

<br><br><br><br>

    <div class="container">
        <h2>여행 정보</h2>
        <p>기본적인 여행 상식과 팁, 그리고 여행 정보를 알려 드려요</p>
        <!-- <p>Tip: Use the "media-right" class to right-align the media object.</p><br> -->
        <br><br>
        <?php
        /**
         * << 페이징을 하는 코드 >>
         * 
         * $page (현재 페이지) : get요청으로 알수있음
         * $block (현재 블럭) = ceil($page/$block_set);
         * $page_set (한 페이지 줄 수) = 10;
         * $block_set (한 페이지 블럭 수) = 5;
         * $total (전체 데이터 수) : 디비에 접근하여 알 수 있음
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
        $page_set = 5; // 한페이지 줄수
        $block_set = 5; // 한페이지 블럭수
        include('dbcon.php');
        ///////////////////////////
        $sql = "SELECT count(*) c FROM info WHERE info_is_remove=0;";
        $result = mysqli_query($con,$sql);
        $row = mysqli_fetch_assoc($result);
        $total = $row['c'];

        $total_page=ceil($total/$page_set);
        $total_block=ceil($total_page/$block_set);

        $page = $_GET['page'];
        if(!$page) $page=1;

        $block=ceil($page/$block_set); 
        $start_idx=($page-1)*$page_set;
        //////////////////////////
        $sql = "SELECT * FROM info WHERE info_is_remove=0 ORDER BY info_no DESC LIMIT $start_idx,$page_set; ";
        $result = mysqli_query($con,$sql);
        if(mysqli_num_rows($result)>0){
            while($info = mysqli_fetch_array($result)){
                $info_no = $info['info_no'];
                $info_title = $info['info_title'];
                $info_content = $info['info_content'];
                $info_write_member_no = $info['info_write_member_no'];
                $info_write_time = $info['info_write_time'];
                $info_hit_count = $info['info_hit_count'];
                $info_representative_img = $info['info_representative_img'];

                $find_write_member_sql = "SELECT * FROM member WHERE member_no='$info_write_member_no';";
                $find_write_member_result = mysqli_query($con,$find_write_member_sql);
                $find_write_member_row = mysqli_fetch_assoc($find_write_member_result);
                $info_write_member_id = $find_write_member_row['member_id'];
                $info_write_member_profile_img = $find_write_member_row['member_profile_img'];
                ?>
                <!-- ###### -->
                <div class="media border">
                    <!-- <img src="images/person_1.jpg" alt="John Doe" class="mr-3 mt-3 rounded-circle" style="width:60px; margin:10px"> -->
                    <img src="<?php echo $info_representative_img;?>" style="width:120px; height:120px; margin:10px">
                    <div class="media-body" style="margin-top:10px; margin-left:20px;vertical-align:middle;">
                            <span>
                            <img src="<?php echo $info_write_member_profile_img;?>" class="rounded-circle"style="width:20px; height:20px;">
                               <strong> <?php echo $info_write_member_id;?></strong> <small><i>Posted on <?php echo $info_write_time;?></i></small></span><br>
                                <br><h4><a href="info_detail.php?info_no=<?php echo $info_no;?>"><?php echo $info_title;?></a></h4>      
                    </div>
                </div><br>
                <!-- ###### -->
                <?php
            }
        }else{
            // 여행 글이 없음
            if($page==1){
                echo "없 음";
            }else{
                echo "<script>alert('없는 페이지 입니다');</script>";
                echo "<script>history.back();</script>";
                exit;
            }
        }
        if($_SESSION['login_id']==='admin'){
            echo "<div class='col text-right'>
            <a href='info_write.php' class='btn btn-secondary' >글쓰기</a>
        </div>";
        }
        
        ?>
        <br>
        <div >    
            <ul class="pagination justify-content-center">
                <?php
                // 블럭 설정
                $first_page = (($block - 1) * $block_set) + 1; // 첫번째 페이지번호
                $last_page = min ($total_page, $block * $block_set); // 마지막 페이지번호
                $prev_block = $block -1; // 이전 블럭
                $prev_block_page = ($prev_block-1)*$block_set+1; // 이전 블럭 페이지
                $next_block = $block +1; // 다음 블럭
                $next_block_page = ($next_block-1)*$block_set+1; // 다음 블럭 페이지
                echo ($prev_block > 0) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?&page=1'> << </a></li>" : "";
                echo ($prev_block > 0) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?&page=$prev_block_page'><</a></li>" : "";
                for($i=$first_page; $i<=$last_page;$i++){
                    echo ($i!=$page) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?page=$i'>$i</a></li>" 
                                        : "<li class='page-item active'><a class='page-link' href='#'>$i</a></li>";
                }
                echo ($next_block <= $total_block) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?page=$next_block_page'>></a></li>" : "";
                echo ($next_block <= $total_block) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?page=$total_page'> >> </a></li>" : "";
                ?>
            </ul>
        </div>
        
    </div>
  
    
<br><br><br>

<!-- footer 삽입 -->
<?php include_once("footer.php");?>

<!-- loader 삽입 -->
<?php include_once("loader.php");?>
    
</body>
</html>