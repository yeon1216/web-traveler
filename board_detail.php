<?php session_start(); ?>
<?php 
if(isset($_GET['board_no'])){
    $board_no = $_GET['board_no'];
    /**
     * 현재 게시글 번호를 쿠키에 저장하는 코드 --> 하루에 한번만 조회수가 증가하도록 하기 위해서
     */
    if(isset($_SESSION['login_id'])){ // 로그인 한 경우 로그인 아이디를 키값으로 하는 쿠키에 게시글 번호를 저장한다
        $login_id = $_SESSION['login_id'];
        if(isset($_COOKIE[$login_id.'_hit_trip'])){
            $value = $board_no.'|'.$_COOKIE[$login_id.'_hit_trip'];
        }else{
            $value = $board_no;
        }
        $hit_board_arr = explode("|",$value);
        $hit_board_arr=array_unique($hit_board_arr); // 중복되는 게시글 번호를 제거
        $value='';
        foreach($hit_board_arr as $hit_board){
            if($value==''){
                $value = $hit_board;
            }else{
                $value = $value.'|'.$hit_board;
            }
        }
        setcookie($login_id.'_hit_board',$value,time()+86400);

    }else{ // 로그인 안한 경우 'no_login_hit_board'를 키값으로 하는 쿠키에 게시글 번호를 저장한다
        if(isset($_COOKIE['no_login_hit_board'])){
            $value = $board_no.'|'.$_COOKIE['no_login_hit_board'];
        }else{
            $value = $board_no;
        }
        $hit_board_arr = explode("|",$value);
        $hit_board_arr=array_unique($hit_board_arr); // 중복되는 게시글 번호를 제거
        $value='';
        foreach($hit_board_arr as $hit_board){
            if($value==''){
                $value = $hit_board;
            }else{
                $value = $value.'|'.$hit_board;
            }
        }
        setcookie('no_login_hit_board',$value,time()+86400);
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
function boardDelete() { // 게시글 삭제 함수
    document.board_delete_form.submit(); // 게시글 삭제 요청
}

function boardReplyDelete(){ // 게시글 댓글 삭제 함수
    var board_reply_no = document.getElementById('board_reply_no').value;
    alert(board_reply_no);
    document.board_reply_delete_form.submit();
}

function boardReplyWrite(){ // 댓글 작성 함수    
    var login_id = <?php // 로그인 한 경우에는 1, 로그인 안한 경우에는 0을 login_id 저장
                        if(empty($_SESSION['login_id'])){
                            echo 0;
                        }else{
                            echo 1;
                        }
                    ?>;
    if(login_id==0){ // 로그인을 안했으면 댓글 안써지도록함
        alert('로그인을 해주세요');
        return false;
    }
    if(document.getElementById('board_reply_content').value.length==0){ // 댓글을 입력 안한경우 댓글 안써짐
        alert('댓글을 입력해주세요');
        document.getElementById('board_reply_content').focus();
        return false;
    }
    document.board_reply_write_form.submit(); // 모든 조건 만족시 서버에 댓글 작성 요청
}
</script>

<?php

/*
    잘못된 url로 board_detail.php 접근시 예외처리
*/
if(isset($_GET['board_no'])){ 
    $board_no = $_GET['board_no'];
    include('dbcon.php');
    $sql = "SELECT count(*) AS c FROM board WHERE board_no='$board_no' AND board_is_remove=0;";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    mysqli_close($con);
    if($row['c']==0){ // 없는 게시글인 경우
        echo "<script>alert('없는 게시글입니다');</script>";
        echo "<script>history.back();</script>";
        exit;
    }
}elseif( !isset($_GET['logout_action']) && !isset($_POST['login_action']) && !isset($_POST['join_action']) && !isset($_POST['board_reply_write_action'])
            && !isset($_POST['board_write_action'])&& !isset($_POST['board_update_action'])&& !isset($_POST['board_delete_action'])
            && !isset($_POST['board_reply_delete_action'])){ // 정상적인 요청이 아닌경우
    echo "<script>alert('잘못된 접근입니다');</script>";
    echo "<script>history.back();</script>";
    exit;
}

if($_GET['board_no']){ // 게시글 상세보기 get 요청

    $board_no = $_GET['board_no'];
    include('dbcon.php');

    ////////////////////////
    /**
     * 이 게시글이 어떤 페이지에 있는 것인지 알아보자
     */
    $board_type = $_GET['board_type']; // 공지사항 글인지, 자유게시 글인지
    $board_no = $_GET['board_no'];
    $seq = 0; // 몇번째 글인지
    $page_set = 10; // 한페이지 줄수

    $sql = "SELECT count(*) c FROM board WHERE board_type=$board_type AND board_is_remove=0;";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $total = $row['c'];

    $total_page=ceil($total/$page_set);

    $sql = "SELECT ROW_NUMBER() OVER(ORDER BY A.board_no DESC)AS seq, A.board_no AS board_no, A.board_type FROM board A WHERE board_is_remove=0 AND board_type=$board_type;";
    
    $result = mysqli_query($con,$sql);
    while($row = mysqli_fetch_array($result)){
        if($board_no==$row['board_no']){
            $seq = $row['seq'];   
        }
    }
    $page = ceil($seq/$page_set);
    echo "<script>console.log('board_type: '+$board_type);</script>";
    echo "<script>console.log('board_no: '+$board_no);</script>";
    echo "<script>console.log('seq: '+$seq);</script>";
    echo "<script>console.log('total_page: '+$total_page);</script>";
    echo "<script>console.log('page: '+$page);</script>";
    ////////////////////////////////////

    if(isset($_SESSION['login_id'])){ // 로그인 한 경우
        $login_id = $_SESSION['login_id'];
        $value=$_COOKIE[$login_id.'_hit_board'];
        $hit_board_arr = explode("|",$value);
        $i=0; // 하루동안 읽었던 게시글인지 아닌지 체크하기 위한 변수
        foreach($hit_board_arr as $hit_board){
            if($hit_board==$board_no){ // 이미 읽었던 게시글이면 $i를 1로 설정
                $i=1;
            }
        }
        if($i==0){ // $i가 0이면 조회수 늘리기
            $sql = "UPDATE board SET board_hit_count=board_hit_count+1 WHERE board_no='$board_no';";
            $result = mysqli_query($con,$sql);
        }
    }else{ // 로그인 안한 경우
        $value=$_COOKIE['no_login_hit_board'];
        $hit_board_arr = explode("|",$value);
        $i=0; // 하루동안 읽었던 게시글인지 아닌지 체크하기 위한 변수
        foreach($hit_board_arr as $hit_board){
            if($hit_board==$board_no){ // 이미 읽었던 게시글이면 $i를 1로 설정
                $i=1;
            }
        }
        if($i==0){ // $i가 0이면 조회수 늘리기
            $sql = "UPDATE board SET board_hit_count=board_hit_count+1 WHERE board_no='$board_no';";
            $result = mysqli_query($con,$sql);
        }
    }

    // 디비에 접근하여 게시글 정보를 가지고옴
    $sql = "SELECT * FROM board WHERE board_no='$board_no';";
    $result = mysqli_query($con,$sql);
    $board = mysqli_fetch_assoc($result);
    $board_title = $board['board_title'];
    $board_content = $board['board_content'];
    $board_write_member_no = $board['board_write_member_no'];
    $board_write_time = $board['board_write_time'];
    $board_hit_count = $board['board_hit_count'];

    // 디비에 접근하여 게시글 작성자를 이용해 작성자 정보를 가지고옴
    $sql = "SELECT * FROM member WHERE member_no='$board_write_member_no';";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $board_write_member_id = $row['member_id'];
    $board_write_member_profile_img = $row['member_profile_img'];
    
    mysqli_close($con); // db 종료
}

if($_POST['board_write_action']){ // 글 작성 post 요청
    $board_type = $_POST['board_type'];
    $board_write_member_no = $_POST['board_write_member_no'];
    $board_title = $_POST['board_title'];
    $board_content = $_POST['board_content'];
    
    include('dbcon.php');
    $sql = "INSERT INTO board (board_type, board_write_member_no, board_title, board_content)
            VALUES ('$board_type','$board_write_member_no','$board_title','$board_content');";
    $result = mysqli_query($con,$sql);

    $sql = "SELECT MAX(board_no) FROM board;";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $board_no = $row['MAX(board_no)'];

    mysqli_close($con);
    echo "<script>window.location.replace('board_detail.php?board_no=$board_no&board_type=$board_type');</script>";
    exit;
}

if($_POST['board_update_action']){ // 글 수정 post 요청
    $board_no = $_POST['board_no'];
    $board_type = $_POST['board_type'];
    $board_title = $_POST['board_title'];
    $board_content = $_POST['board_content'];
    include('dbcon.php');
    $sql = "UPDATE board SET board_title='$board_title', board_content='$board_content' WHERE board_no='$board_no';";
    $result = mysqli_query($con,$sql);
    mysqli_close($con);
    echo "<script>window.location.replace('board_detail.php?board_no=$board_no&board_type=$board_type');</script>";
    exit;
}

if($_POST['board_delete_action']){ // 글 삭제 post 요청
    $board_no = $_POST['board_no'];
    $board_type = $_POST['board_type'];
    $page = $_POST['page'];
    $page_set = 10;
    include('dbcon.php');

    $sql = "SELECT count(*) c FROM board WHERE board_type=$board_type AND board_is_remove=0;";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $total = $row['c'];

    $total_page = ceil($total/$page_set);
    
    // 디비에 접근하여 글 삭제상태로 수정
    $sql = "UPDATE board SET board_is_remove=1 WHERE board_no='$board_no';";
    $result = mysqli_query($con,$sql);

    /**
     * 글을 삭제하는 페이지에 글의 갯수를 확인하고 해당 페이지에 글이 없으면 '$page-1' 페이지로 이동하기
     */
    if($total_page==$page){ // 마지막 페이지의 게시글인 경우
        $sql = "SELECT count(*) c FROM board WHERE board_type=$board_type AND board_is_remove=0;";
        $result = mysqli_query($con,$sql);
        $row = mysqli_fetch_assoc($result);
        $total_after_remove = $row['c'];
    
        $total_page_after_remove = ceil($total_after_remove/$page_set);

        if($total_page==$total_page_after_remove){ // 원래 페이지에 게시글이 있는 경우
            echo "<script>alert('게시글이 삭제되었습니다');</script>";
            echo "<script>window.location.replace('board_list.php?board_type=$board_type&page=$page');</script>";
            exit;
        }else{ // 원래 페이지에 게시글이 없는 경우
            echo "<script>alert('게시글이 삭제되었습니다');</script>";
            if($page!=1) $page--;
            echo "<script>window.location.replace('board_list.php?board_type=$board_type&page=$page');</script>";
            exit;
        }
    }else{ // 마지막 페이지가 아닌 페이지의 게시글인 경우
        echo "<script>alert('게시글이 삭제되었습니다');</script>";
        echo "<script>window.location.replace('board_list.php?board_type=$board_type&page=$page');</script>";
        exit;
    }
    mysqli_close($con); // 디비 종료
    
}

if($_POST['board_reply_write_action']){ // 댓글 작성 post 요청
    include('dbcon.php');
    $board_no = $_POST['board_no'];
    $board_reply_write_member_no = $_POST['board_reply_write_member_no'];
    $board_reply_content = $_POST['board_reply_content'];
    
    $sql = "INSERT INTO board_reply (board_no, board_reply_write_member_no, board_reply_content)
            VALUES ($board_no,$board_reply_write_member_no,'$board_reply_content');";
    $result = mysqli_query($con,$sql);
    
    echo "<script>window.location.replace('board_detail.php?board_no=$board_no');</script>";
    exit;
}

if($_POST['board_reply_delete_action']){ // 댓글 삭제 post 요청
    include('dbcon.php');
    $board_no = $_POST['board_no'];
    $board_reply_no = $_POST['board_reply_no'];    
    $sql = "UPDATE board_reply SET board_reply_is_remove=1 WHERE board_reply_no='$board_reply_no';";
    $result = mysqli_query($con,$sql);
    echo "<script>alert('댓글이 삭제되었습니다')</script>";
    echo "<script>window.location.replace('board_detail.php?board_no=$board_no');</script>";
    exit;
}
?>

<body>

    <?php include_once("nav.php");?>

    <br><br><br><br>

    <div class="container">
        <h2>게시글</h2>
        <br><br>
            <div class="mb-3">
                <p>
                    <label for="title"><strong>작성자</strong></label>&nbsp;&nbsp;:&nbsp;&nbsp;
                <span><img src="<?php echo $board_write_member_profile_img;?>" class="rounded-circle" alt="Cinque Terre" width="30" height="30"></span>
                    <label for="title">&nbsp;<?php echo $board_write_member_id; ?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <label for="title"><strong>작성 시간</strong></label>&nbsp;&nbsp;:&nbsp;&nbsp;<label
                        for="title"><?php echo $board_write_time; ?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <label for="title"><strong>조회수</strong></label>&nbsp;&nbsp;:&nbsp;&nbsp;<label
                        for="title"><?php echo $board_hit_count; ?></label>
                </p>
            </div>
            <br>
            <div class="mb-3">
                <label for="title"><strong>제 목 </strong></label>
                <input type="text" class="form-control" name="title" id="title" value="<?php echo $board_title; ?>" readonly>
            </div>


            <br>
            <div class="mb-3">
                <label for="content"><strong>내 용</strong></label>
                <br>
                <hr>
                <span><?php echo $board_content; ?></span>
                <br>
                <hr><br>

            </div>
        <br>
        <div class="col text-right">
            <?php
                if((isset($_SESSION['login_id']) && $_SESSION['login_id']===$board_write_member_id) || 
                        (isset($_SESSION['login_id']) && $_SESSION['login_id']==='admin')){ // 게시글 작성자와 로그인한 멤버가 같은경우 or 관리자인 경우
                    ?>
            <a href='board_update.php?board_no=<?php echo $_GET['board_no']; ?>&board_type=<?php echo $_GET['board_type']; ?>'
                class='btn btn btn-secondary'>수정</a>

            <a href='#' class='btn btn btn-secondary' data-toggle="modal" data-target="#board-delete-check-modal">삭제</a>
            <?php
                }
            ?>
            <a href="board_list.php?board_type=<?php echo $_GET['board_type']?>&page=<?php echo $page?>" class="btn btn btn-secondary">목록</a>
        </div>
        <br><hr>
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
                $('#board_reply_content').on('keyup', function() { // 실시간 글자수 가운딩
                    $('#counter').html("("+$(this).val().length+" / 최대 200자)");
                    if($(this).val().length > 200) {
                        $(this).val($(this).val().substring(0, 200));
                        alert("최대 200자까지 입력 가능합니다.");
                        $(this).val(content.substring(0, 200));
                        $('#counter').html("(200 / 최대 200자)");
                    }
                });
            });
        </script>
        <form name="board_reply_write_form" id="board_reply_write_form" role="form" method="post" action="board_detail.php">
            <div class="mb-3">
                <label for="content">댓글 작성</label>&nbsp;&nbsp;<span style="color:#aaa;" id="counter">(0 / 최대 200자)</span>
                <textarea class="form-control" rows="5" name="board_reply_content" id="board_reply_content"></textarea>
            </div>
            <input type="hidden" id="board_no" name="board_no" value="<?php echo $board_no; ?>"/>
            <input type="hidden" id="board_reply_write_member_no" name="board_reply_write_member_no" value="<?php echo $login_member_no; ?>"/>
            <input type="hidden" id="board_reply_write_action" name="board_reply_write_action" value="true"/>
        </form> 
        <p class="text-right">
                <a href="#" class="btn btn-sm btn-secondary" onclick="boardReplyWrite()">댓글 등록</a>
            </p>       
        <hr>
        <?php
         include('dbcon.php');
         $sql = "SELECT * FROM board_reply WHERE board_reply_is_remove=0 AND board_no='$board_no' ORDER BY board_reply_no DESC;";
         $result = mysqli_query($con,$sql);
         if(mysqli_num_rows($result)>0){
             ?>
            <br>
            <h2>댓 글</h2>
            <br><br>

             <?php
             while($board_reply = mysqli_fetch_array($result)){
                 // 댓글 하나씩 가져오기
                 $board_reply_no = $board_reply['board_reply_no'];
                 $board_no = $board_reply['board_no'];
                 $board_reply_write_member_no = $board_reply['board_reply_write_member_no'];
                 $board_reply_content = $board_reply['board_reply_content'];
                 $board_reply_content = nl2br($board_reply_content); // /n 을 <br> 로 치환
                 $board_reply_write_time = $board_reply['board_reply_write_time'];

                 // 댓글 작성자 번호로 댓글 작성자 아이디, 프로필 사진 찾기
                 $find_write_member_sql = "select * from member where member_no='$board_reply_write_member_no'";
                 $find_write_member_result = mysqli_query($con,$find_write_member_sql);
                 $find_write_member_row = mysqli_fetch_array($find_write_member_result);
                 $member_id = $find_write_member_row['member_id'];
                 $member_profile_img = $find_write_member_row['member_profile_img'];
                 ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th><span><img src="<?php echo $member_profile_img;?>" class="rounded-circle" alt="Cinque Terre" width="30" height="30"></span><span class="text">&nbsp;&nbsp;<?php echo $member_id; ?></span></th>
                            <th class="text-right"><?php echo $board_reply_write_time; ?></th>
                            
                        </tr>
                        
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2"><?php echo $board_reply_content; ?></td>
                        </tr>
                        <tr>
                        <?php
                        if($member_id==$_SESSION['login_id'] ||'admin'==$_SESSION['login_id']){ // 댓글 작성자와 로그인 멤버가 같은 경우 or 관리자가 로그인 한 경우
                            ?>
                                <td class="text-right" colspan="2">
                                    <form name="board_reply_delete_form_<?php echo $board_reply_no;?>" id="board_reply_delete_form_<?php echo $board_reply_no;?>" role="form" method="post"
                                        action="<?php echo $_SERVER['PHP_SELF'];?>">
                                        <input type="hidden" name="board_no" value="<?php echo $board_no; ?>" />
                                        <input type="hidden" name="board_reply_no" id="board_reply_no" value="<?php echo $board_reply_no; ?>" />
                                        <input type="hidden" name="board_reply_delete_action" value="true" />
                                        <button type="submit" class="btn btn-sm btn-danger">삭제</button>
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
         }
         mysqli_close($con);
        ?>
    </div>
    <br><br><br>

    <!-- footer 삽입 -->
    <?php include_once("footer.php"); ?>
    <!-- loader 삽입 -->
    <?php include_once("loader.php");?>
</body>

<!-- The Modal (게시글 삭제전에 물어보는 모달) 시작 -->
<!-- <div class="modal fade" id="board-delete-check-modal" data-backdrop="static" data-keyboard="false"> -->
<div class="modal fade" id="board-delete-check-modal">
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
                <form name="board_delete_form" id="board_delete_form" role="form" method="post"
                    action="<?php echo $_SERVER['PHP_SELF'];?>">
                    <h4>정말로 삭제하시겠어요?</h4>
                    <input type="hidden" name="board_no" value="<?php echo $board_no; ?>" />
                    <input type="hidden" name="board_type" value="<?php echo $board_type; ?>" />
                    <input type="hidden" name="page" value="<?php echo $page; ?>" />
                    <input type="hidden" name="board_delete_action" value="true" />
                    
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
                        onclick="boardDelete()">삭제</button>
                </p>
                
            </div>

        </div>
    </div>
</div>
<!-- The Modal (게시글 삭제전에 물어보는 모달) 끝 -->

</html>