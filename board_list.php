<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header 삽입 -->
    <?php include_once("header.php");?>
</head>

<?php
    if(isset($_GET['board_type'])){ // get 요청에 게시글 타입이 있는 경우
        if(!preg_match("/^[0-1]$/",$_GET['board_type'])){ // board_type이 0 또는 1이 아닌경우 예외처리
            echo "<script>window.location.replace('board_list.php?board_type=0');</script>";
            exit;
        } 

        // get 요청의 board_type이 0 또는 1인 경우
        if($_GET['board_type']==0) $board_type="공지사항";
        elseif($_GET['board_type']==1) $board_type="자유게시판";

    }elseif( !isset($_GET['logout_action']) && !isset($_POST['login_action']) && !isset($_POST['join_action'])){ // 로그아웃, 로그인, 회원가입 요청을 제외한 요청 예외처리
        echo "<script>window.location.replace('board_list.php?board_type=0');</script>";
        exit;
    }
?>

<body>
    <!-- nav 삽입 -->
    <?php include_once("nav.php"); ?>
    <br/><br/><br/><br/><br/>

    <div class="container">
        <div>
            <?php 
            echo "<h1>$board_type</h1>";
            ?>
        </div>
        <div class="col text-right">
            <!-- <div> -->
            <div class="block">
                <ul style="display:inline-block">
                    <li style="display:inline"><a href="board_list.php?board_type=0">공지사항</a></li> &nbsp;&nbsp; / &nbsp;&nbsp;
                    <li style="display:inline"><a href="board_list.php?board_type=1">자유게시판</a></li>
                </ul>
            </div>
        </div>
        <div class="row">
            
                    <?php
                        include('dbcon.php');
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

                        $page_set = 10; // 한페이지 줄수
                        $block_set = 5; // 한페이지 블럭수

                        if($_GET['board_type']==0){ // 공지사항 요청일 경우
                            ?>
                            <table class="table table-striped" style="text-align:center; ">
                            <thead style="border:1px solid #ffffff">
                                <tr>
                                    <th style="background-color:#ffffff; text-align:center; width:5px;">번호</th>
                                    <th style="background-color:#ffffff; text-align:center; width:200px;">제목</th>
                                    <th style="background-color:#ffffff; text-align:center; width:20px;">작성자</th>
                                    <th style="background-color:#ffffff; text-align:center; width:20px;">작성일</th>
                                    <th style="background-color:#ffffff; text-align:center; width:5px;">조회수</th>
                                </tr>
                            </thead>
                            <tbody >
                            <?php
                            
                            $sql = "SELECT count(*) c FROM board WHERE board_type=0 AND board_is_remove=0;";
                            $result = mysqli_query($con,$sql);
                            $row = mysqli_fetch_assoc($result);
                            $total = $row['c'];

                            $total_page=ceil($total/$page_set);
                            $total_block=ceil($total_page/$block_set);

                            $page = $_GET['page'];
                            if(!$page) $page=1;

                            $block=ceil($page/$block_set); 
                            $start_idx=($page-1)*$page_set;

                            $sql = "SELECT * FROM board WHERE board_type=0 AND board_is_remove=0 ORDER BY board_no DESC LIMIT $start_idx,$page_set;";

                            $result = mysqli_query($con,$sql);
                            if(mysqli_num_rows($result)>0){
                                while($board = mysqli_fetch_array($result)){
    
                                    // 디비에서 게시글 정보 꺼내기
                                    $board_no = $board['board_no'];
                                    $board_title = $board['board_title'];
                                    $board_write_member_no = $board['board_write_member_no'];
                                    $board_is_remove = $board['board_is_remove'];
    
                                    // 디비에서 작성자 멤버 번호로 멤버 아이디, 프로필사진 꺼내기
                                    $find_write_member_sql = "select * from member where member_no='$board_write_member_no'";
                                    $find_write_member_result = mysqli_query($con,$find_write_member_sql);
                                    $find_write_member_row = mysqli_fetch_array($find_write_member_result);
                                    $member_id = $find_write_member_row['member_id'];
                                    $member_profile_img = $find_write_member_row['member_profile_img'];
                                    ?>
                                        <tr style='background-color:#ffffff;'>
                                            <td><?php echo $board_no; ?></td>
                                            <td>
                                                <a href="board_detail.php?board_type=<?php echo $_GET['board_type'];?>&board_no=<?php echo $board['board_no']; ?>"><?php echo $board['board_title']; ?></a>
                                            </td>
                                            <td><span><img src="<?php echo $member_profile_img;?>" class="rounded-circle" alt="<?php echo $member_id; ?>님 프로필 사진" width="25" height="25"></span>&nbsp;&nbsp;<?php echo $member_id; ?></td>
                                            <td><?php echo $board['board_write_time']; ?></td>
                                            <td><?php echo $board['board_hit_count']; ?></td>
                                        </tr>
                                    <?php
                                }
                            }else{ // 게시글이 없음
                                if($page==1){
                                    echo "<tr style='background-color:#ffffff;'><td colspan='5'>게시글 없음</td></tr>";
                                }else{
                                    echo "<script>alert('없는 페이지 입니다');</script>";
                                    echo "<script>history.back();</script>";
                                    exit;
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                        </div> <br>
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
                                echo ($prev_block > 0) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?board_type=0&page=1'> << </a></li>" : "";
                                echo ($prev_block > 0) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?board_type=0&page=$prev_block_page'><</a></li>" : "";
                                for($i=$first_page; $i<=$last_page;$i++){
                                    echo ($i!=$page) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?board_type=0&page=$i'>$i</a></li>" 
                                                        : "<li class='page-item active'><a class='page-link' href='#'>$i</a></li>";
                                }
                                echo ($next_block <= $total_block) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?board_type=0&page=$next_block_page'>></a></li>" : "";
                                echo ($next_block <= $total_block) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?board_type=0&page=$total_page'> >> </a></li>" : "";
                                ?>
                            </ul>
                        </div>
                        <?php
                        }elseif($_GET['board_type']==1){ // 자유게시판 요청일 경우
                            ?>
                            <table class="table table-striped" style="text-align:center; ">
                            <thead style="border:1px solid #ffffff">
                                <tr>
                                    <th style="background-color:#ffffff; text-align:center; width:5px;">번호</th>
                                    <th style="background-color:#ffffff; text-align:center; width:200px;">제목</th>
                                    <th style="background-color:#ffffff; text-align:center; width:20px;">작성자</th>
                                    <th style="background-color:#ffffff; text-align:center; width:20px;">작성일</th>
                                    <th style="background-color:#ffffff; text-align:center; width:5px;">조회수</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $sql = "SELECT count(*) c FROM board WHERE board_type=1 AND board_is_remove=0;";
                            $result = mysqli_query($con,$sql);
                            $row = mysqli_fetch_assoc($result);
                            $total = $row['c'];

                            $total_page=ceil($total/$page_set);
                            $total_block=ceil($total_page/$block_set);

                            $page = $_GET['page'];
                            if(!$page) $page=1;

                            $block=ceil($page/$block_set); 
                            $start_idx=($page-1)*$page_set;
                            // $sql = "SELECT * FROM board WHERE board_type=1 AND board_is_remove=0 ORDER BY board_no DESC;";
                            $sql = "SELECT * FROM board WHERE board_type=1 AND board_is_remove=0 ORDER BY board_no DESC LIMIT $start_idx,$page_set;";
                            $result = mysqli_query($con,$sql);
                            if(mysqli_num_rows($result)>0){
                                while($board = mysqli_fetch_array($result)){

                                    // 디비에서 게시글 정보 꺼내기
                                    $board_no = $board['board_no'];
                                    $board_title = $board['board_title'];
                                    $board_write_member_no = $board['board_write_member_no'];
                                    $board_is_remove = $board['board_is_remove'];

                                    // 디비에서 작성자 멤버 번호로 멤버 아이디, 프로필사진 꺼내기
                                    $find_write_member_sql = "select * from member where member_no='$board_write_member_no'";
                                    $find_write_member_result = mysqli_query($con,$find_write_member_sql);
                                    $find_write_member_row = mysqli_fetch_array($find_write_member_result);
                                    $member_id = $find_write_member_row['member_id'];
                                    $member_profile_img = $find_write_member_row['member_profile_img'];
                                    ?>
                                        <tr style='background-color:#ffffff;'>
                                            <td><?php echo $board_no; ?></td>
                                            <td>
                                                <a href="board_detail.php?board_type=<?php echo $_GET['board_type'];?>&board_no=<?php echo $board['board_no']; ?>"><?php echo $board['board_title']; ?></a>
                                            </td>
                                            <td><span><img src="<?php echo $member_profile_img;?>" class="rounded-circle" alt="<?php echo $member_id; ?>님 프로필 사진" width="25" height="25"></span>&nbsp;&nbsp;<?php echo $member_id; ?></td>
                                            <td><?php echo $board['board_write_time']; ?></td>
                                            <td><?php echo $board['board_hit_count']; ?></td>
                                        </tr>
                                    <?php
                                }
                                
                            }else{ // 게시글이 없음
                                if($page==1){
                                    echo "<tr style='background-color:#ffffff;'><td colspan='5'>게시글 없음</td></tr>";
                                }else{
                                    echo "<script>alert('없는 페이지 입니다');</script>";
                                    echo "<script>history.back();</script>";
                                    exit;
                                }
                                

                            }
                            ?>
                            </tbody>
                        </table>
                        </div> <br>
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
                                echo ($prev_block > 0) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?board_type=1&page=1'> << </a></li>" : "";
                                echo ($prev_block > 0) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?board_type=1&page=$prev_block_page'><</a></li>" : "";
                                for($i=$first_page; $i<=$last_page;$i++){
                                    echo ($i!=$page) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?board_type=1&page=$i'>$i</a></li>" 
                                                        : "<li class='page-item active'><a class='page-link' href='#'>$i</a></li>";
                                }
                                echo ($next_block <= $total_block) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?board_type=1&page=$next_block_page'>></a></li>" : "";
                                echo ($next_block <= $total_block) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?board_type=1&page=$total_page'> >> </a></li>" : "";
                                ?>
                            </ul>
                        </div>
                        <?php
                        }
                        mysqli_close($con);
                    ?>
            
        
            
            <?php 
            if($board_type==='공지사항'){ // board_type이 공지사항인 경우
                if($_SESSION['login_id']==='admin'){
                    echo "<div class='col text-right'>
                            <a href='board_write.php?board_type=0' class='btn btn-secondary' >글쓰기</a>
                        </div>";
                }
            }elseif($board_type==='자유게시판'){ // board_type이 자유게시판인 경우
                if(isset($_SESSION['login_id'])){
                    echo "<div class='col text-right'>
                            <a href='board_write.php?board_type=1' class='btn btn-secondary' >글쓰기</a>
                        </div>";
                }
            }
            ?>
        </div>
    </div>
    <br/><br/><br/><br/><br/>
    <!-- footer 삽입 -->
    <?php include_once("footer.php"); ?>
    <!-- loader 삽입 -->
    <?php include_once("loader.php");?>
</body>

</html>