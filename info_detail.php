<?php session_start(); ?>
<?php 
if(isset($_GET['info_no'])){
    $info_no = $_GET['info_no'];
    if(isset($_SESSION['login_id'])){
        // 로그인 한 경우
        $login_id = $_SESSION['login_id'];
        if(isset($_COOKIE[$login_id.'_hit_trip'])){
            $value = $info_no.'|'.$_COOKIE[$login_id.'_hit_trip'];
        }else{
            $value = $info_no;
        }
        $hit_info_arr = explode("|",$value);
        $hit_info_arr=array_unique($hit_info_arr);
        $value='';
        foreach($hit_info_arr as $hit_info){
            if($value==''){
                $value = $hit_info;
            }else{
                $value = $value.'|'.$hit_info;
            }
        }
        setcookie($login_id.'_hit_info',$value,time()+86400);
    }else{
        // 로그인 안한 경우
        if(isset($_COOKIE['no_login_hit_info'])){
            $value = $info_no.'|'.$_COOKIE['no_login_hit_info'];
        }else{
            $value = $info_no;
        }
        $hit_info_arr = explode("|",$value);
        $hit_info_arr=array_unique($hit_info_arr);
        $value='';
        foreach($hit_info_arr as $hit_info){
            if($value==''){
                $value = $hit_info;
            }else{
                $value = $value.'|'.$hit_info;
            }
        }
        setcookie('no_login_hit_info',$value,time()+86400);
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
function infoDelete() {
    document.info_delete_form.submit();
}

function infoReplyWrite(){
    
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
    if(document.getElementById('info_reply_content').value.length==0){
        alert('댓글을 입력해주세요');
        document.getElementById('info_reply_content').focus();
        return false;
    }
    document.info_reply_write_form.submit();
}
</script>

<?php
// 예외처리
if(isset($_GET['info_no'])){
    $info_no = $_GET['info_no'];
    include('dbcon.php');
    $sql = "SELECT count(*) AS c FROM info WHERE info_no='$info_no' AND info_is_remove=0;";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    mysqli_close($con);
    if($row['c']==0){
        echo "<script>alert('잘못된 접근입니다');</script>";
        echo "<script>window.location.replace('info_list.php');</script>";
        exit;
    }
}elseif( !isset($_GET['logout_action']) && !isset($_POST['login_action']) && !isset($_POST['join_action']) && !isset($_POST['info_reply_write_action'])
            && !isset($_POST['info_write_action'])&& !isset($_POST['info_update_action'])&& !isset($_POST['info_delete_action'])&& !isset($_POST['info_reply_delete_action'])){
    echo "<script>window.location.replace('info_list.php');</script>";
    exit;
}

////////////////////////
/**
 * 이 게시글이 어떤 페이지에 있는 것인지 알아보자
 */
include('dbcon.php');
$info_no = $_GET['info_no'];
$seq = 0; // 몇번째 글인지
$page_set = 5; // 한페이지 줄수

$sql = "SELECT count(*) c FROM info WHERE info_is_remove=0;";
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_assoc($result);
$total = $row['c'];

$total_page=ceil($total/$page_set);

$sql = "SELECT ROW_NUMBER() OVER(ORDER BY A.info_no DESC)AS seq, A.info_no AS info_no FROM info A WHERE info_is_remove=0;";

$result = mysqli_query($con,$sql);
while($row = mysqli_fetch_array($result)){
    if($info_no==$row['info_no']){
        $seq = $row['seq'];   
    }
}
$page = ceil($seq/$page_set);
echo "<script>console.log('info_no: '+$info_no);</script>";
echo "<script>console.log('seq: '+$seq);</script>";
echo "<script>console.log('total_page: '+$total_page);</script>";
echo "<script>console.log('page: '+$page);</script>";
mysqli_close($con);
////////////////////////////////////

/**
 * 여행 정보 조회 get 요청
 */
if($_GET['info_no']){
    $info_no = $_GET['info_no'];
    include('dbcon.php');

    /**
     * 조회수 늘리기
     */
    if(isset($_SESSION['login_id'])){
        $login_id = $_SESSION['login_id'];
        if(empty($_COOKIE[$login_id.'_hit_info'])){
            $sql = "UPDATE info SET info_hit_count=info_hit_count+1 WHERE info_no='$info_no';";
            $result = mysqli_query($con,$sql);
        }else{
            $value=$_COOKIE[$login_id.'_hit_info'];
            $hit_info_arr = explode("|",$value);
            $i=0;
            foreach($hit_info_arr as $hit_info){
                if($hit_info==$info_no){
                    $i=1;
                }
            }
            if($i==0){
                $sql = "UPDATE info SET info_hit_count=info_hit_count+1 WHERE info_no='$info_no';";
                $result = mysqli_query($con,$sql);
            }
        }
    }else{
        if(empty($_COOKIE['no_login_hit_info'])){
            $sql = "UPDATE info SET info_hit_count=info_hit_count+1 WHERE info_no='$info_no';";
            $result = mysqli_query($con,$sql);
        }else{
            $value=$_COOKIE['no_login_hit_info'];
            $hit_info_arr = explode("|",$value);
            $i=0;
            foreach($hit_info_arr as $hit_info){
                if($hit_info==$info_no){
                    $i=1;
                }
            }
            if($i==0){
                $sql = "UPDATE info SET info_hit_count=info_hit_count+1 WHERE info_no='$info_no';";
                $result = mysqli_query($con,$sql);
            }
        }
    }
    

    $sql = "SELECT * FROM info WHERE info_no='$info_no';";
    $result = mysqli_query($con,$sql);
    $info = mysqli_fetch_assoc($result);
    $info_title = $info['info_title'];
    $info_content = $info['info_content'];
    $info_write_member_no = $info['info_write_member_no'];
    $info_write_time = $info['info_write_time'];
    $info_hit_count = $info['info_hit_count'];
    $info_representative_img = $info['info_representative_img'];

    $sql = "SELECT * FROM member WHERE member_no='$info_write_member_no';";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $info_write_member_id = $row['member_id'];
    $info_write_member_profile_img = $row['member_profile_img'];
    
    mysqli_close($con);
}

/**
 * 글 작성 post 요청
 */
if($_POST['info_write_action']){
    $info_write_member_no = $_POST['info_write_member_no'];
    $info_title = $_POST['info_title'];
    $info_content = $_POST['info_content'];

    $file = $_FILES['fileToUpload'];
    if(empty($file['tmp_name'])){
        $info_representative_img = 'upload/2019-08-20 08:38:55.png';
    }else{
        $target_dir = "upload/";
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $check = getimagesize($file["tmp_name"]);
        $upload_img_dir =  $target_dir . date('Y-m-d H:i:s').'.'.$imageFileType;
        move_uploaded_file($file["tmp_name"], $upload_img_dir);
        $info_representative_img = $upload_img_dir;
    }
    
    include('dbcon.php');
    $sql = "INSERT INTO info (info_write_member_no, info_title, info_content,info_representative_img)
            VALUES ('$info_write_member_no','$info_title','$info_content','$info_representative_img');";
    $result = mysqli_query($con,$sql);

    $sql = "SELECT MAX(info_no) FROM info;";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $info_no = $row['MAX(info_no)'];

    mysqli_close($con);
    echo "<script>window.location.replace('info_detail.php?info_no=$info_no');</script>";
    exit;
}

/**
 * 여행글 수정 post 요청
 */
if($_POST['info_update_action']){
    $info_no = $_POST['info_no'];
    $info_title = $_POST['info_title'];
    $info_content = $_POST['info_content'];

    $file = $_FILES['fileToUpload'];
    if(empty($file['tmp_name'])){
        $info_representative_img = $_POST['info_representative_img'];
    }else{
        $target_dir = "upload/";
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $check = getimagesize($file["tmp_name"]);
        $upload_img_dir =  $target_dir . date('Y-m-d H:i:s').'.'.$imageFileType;
        move_uploaded_file($file["tmp_name"], $upload_img_dir);
        $info_representative_img = $upload_img_dir;
    }

    include('dbcon.php');
    $sql = "UPDATE info SET info_title='$info_title', info_content='$info_content', info_representative_img='$info_representative_img'
            WHERE info_no='$info_no';";
    $result = mysqli_query($con,$sql);
    mysqli_close($con);
    echo "<script>window.location.replace('info_detail.php?info_no=$info_no');</script>";
    exit;
}

/**
 * 여행 정보글 삭제 post 요청
 */
if($_POST['info_delete_action']){
    $info_no = $_POST['info_no'];
    include('dbcon.php');

    ////////////////////////
    /**
     * 이 게시글이 어떤 페이지에 있는 것인지 알아보자
     */
    // $info_no = $_GET['info_no'];
    $seq = 0; // 몇번째 글인지
    $page_set = 5; // 한페이지 줄수

    $sql = "SELECT count(*) c FROM info WHERE info_is_remove=0;";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $total = $row['c'];

    $total_page=ceil($total/$page_set);

    $sql = "SELECT ROW_NUMBER() OVER(ORDER BY A.info_no DESC)AS seq, A.info_no AS info_no FROM info A WHERE info_is_remove=0;";

    $result = mysqli_query($con,$sql);
    while($row = mysqli_fetch_array($result)){
        if($info_no==$row['info_no']){
            $seq = $row['seq'];   
        }
    }
    $page = ceil($seq/$page_set);
    echo "<script>console.log('info_no: '+$info_no);</script>";
    echo "<script>console.log('seq: '+$seq);</script>";
    echo "<script>console.log('total_page: '+$total_page);</script>";
    echo "<script>console.log('page: '+$page);</script>";
    ////////////////////////////////////

    $sql = "UPDATE info SET info_is_remove=1 WHERE info_no='$info_no';";
    $result = mysqli_query($con,$sql);
    

    /**
     * 글을 삭제하는 페이지에 글의 갯수를 확인하고 해당 페이지에 글이 없으면 '$page-1' 페이지로 이동하기
     */
    if($total_page==$page){ // 마지막 페이지의 게시글인 경우
        $sql = "SELECT count(*) c FROM info WHERE info_is_remove=0;";
        $result = mysqli_query($con,$sql);
        $row = mysqli_fetch_assoc($result);
        $total_after_remove = $row['c'];
    
        $total_page_after_remove = ceil($total_after_remove/$page_set);

        if($total_page==$total_page_after_remove){ // 원래 페이지에 게시글이 있는 경우
            echo "<script>alert('게시글이 삭제되었습니다');</script>";
            echo "<script>window.location.replace('info_list.php?page=$page');</script>";
            exit;
        }else{ // 원래 페이지에 게시글이 없는 경우
            echo "<script>alert('게시글이 삭제되었습니다');</script>";
            if($page!=1) $page--;
            echo "<script>window.location.replace('info_list.php?page=$page');</script>";
            exit;
        }
    }else{ // 마지막 페이지가 아닌 페이지의 게시글인 경우
        echo "<script>alert('게시글이 삭제되었습니다');</script>";
        echo "<script>window.location.replace('info_list.php?page=$page');</script>";
        exit;
    }
    mysqli_close($con);
}

if($_POST['info_reply_write_action']){ // 댓글 작성시
    include('dbcon.php');
    $info_no = $_POST['info_no'];
    $info_reply_write_member_no = $_POST['info_reply_write_member_no'];
    $info_reply_content = $_POST['info_reply_content'];
    
    $sql = "INSERT INTO info_reply (info_no, info_reply_write_member_no, info_reply_content)
            VALUES ($info_no,$info_reply_write_member_no,'$info_reply_content');";
    $result = mysqli_query($con,$sql);
    
    echo "<script>window.location.replace('info_detail.php?info_no=$info_no');</script>";
    exit;
}

if($_POST['info_reply_delete_action']){ // 댓글 삭제시
    include('dbcon.php');
    $info_no = $_POST['info_no'];
    $info_reply_no = $_POST['info_reply_no'];    
    $sql = "UPDATE info_reply SET info_reply_is_remove=1 WHERE info_reply_no='$info_reply_no';";
    $result = mysqli_query($con,$sql);
    echo "<script>alert('댓글이 삭제되었습니다')</script>";
    echo "<script>window.location.replace('info_detail.php?info_no=$info_no');</script>";
    exit;
}
?>

<body>

    <?php include_once("nav.php");?>

    <br><br><br><br>

    <div class="container">
        <h2>여행정보</h2>
        <br><br>
        
            <div class="mb-3">
                <p>
                    <label for="title"><strong>작성자</strong></label>&nbsp;&nbsp;:&nbsp;&nbsp;
                <span><img src="<?php echo $info_write_member_profile_img;?>" class="rounded-circle" alt="Cinque Terre" width="30" height="30"></span>
                    <label for="title">&nbsp;<?php echo $info_write_member_id; ?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <label for="title"><strong>작성 시간</strong></label>&nbsp;&nbsp;:&nbsp;&nbsp;<label
                        for="title"><?php echo $info_write_time; ?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <label for="title"><strong>조회수</strong></label>&nbsp;&nbsp;:&nbsp;&nbsp;<label
                        for="title"><?php echo $info_hit_count; ?></label>
                </p>
            </div>
            <br>
            <div class="mb-3">
                <img src="<?php echo $info_representative_img;?>" width="400" height="400">
            </div><br>
            
            <div class="mb-3">
                <label for="title"><strong>제 목 </strong><?php //echo $board_title; ?></label>
                <input type="text" class="form-control" name="title" id="title" value="<?php echo $info_title; ?>" readonly>
            </div>
            <br>
            
            <br>
            <div class="mb-3">
                <label for="content"><strong>내 용</strong></label>
                <br>
                <hr>
                <span><?php echo $info_content; ?></span>
                <br>
                <hr><br>

            </div>
        <br>
        <div class="col text-right">
            <?php
                if((isset($_SESSION['login_id']) && $_SESSION['login_id']===$info_write_member_id) || 
                        (isset($_SESSION['login_id']) && $_SESSION['login_id']==='admin')){
                    ?>
            <a href='info_update.php?info_no=<?php echo $_GET['info_no']; ?>' class='btn btn btn-secondary'>수정</a>
            <a href='#' class='btn btn btn-secondary' data-toggle="modal" data-target="#info-delete-check-modal">삭제</a>
            <?php
                }
            ?>
            <a href="info_list.php?page=<?php echo $page; ?>" class="btn btn btn-secondary">목록</a>
            <!-- <button type="button" class="btn btn btn-secondary" href="board_list.php?board_type=1">목록</button> -->
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
                $('#info_reply_content').on('keyup', function() {
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
        <form name="info_reply_write_form" id="info_reply_write_form" role="form" method="post" action="info_detail.php">
            <div class="mb-3">
                <label for="content">댓글 작성</label>&nbsp;&nbsp;<span style="color:#aaa;" id="counter">(0 / 최대 200자)</span>
                <textarea class="form-control" rows="5" name="info_reply_content" id="info_reply_content"></textarea>
            </div>
            <input type="hidden" id="info_no" name="info_no" value="<?php echo $info_no; ?>"/>
            <input type="hidden" id="info_reply_write_member_no" name="info_reply_write_member_no" value="<?php echo $login_member_no; ?>"/>
            <input type="hidden" id="info_reply_write_action" name="info_reply_write_action" value="true"/>
        </form> 
        <p class="text-right">
                <a href="#" class="btn btn-sm btn-secondary" onclick="infoReplyWrite()">댓글 등록</a>
            </p>       
        <hr>
        <?php
         include('dbcon.php');
         $sql = "SELECT * FROM info_reply WHERE info_reply_is_remove=0 AND info_no='$info_no' ORDER BY info_reply_no DESC;";
         $result = mysqli_query($con,$sql);
         if(mysqli_num_rows($result)>0){
             ?>
            <br>
            <h2>댓 글</h2>
            <br><br>
             <?php
             while($info_reply = mysqli_fetch_array($result)){
                 // 댓글 하나씩 가져오기
                 $info_reply_no = $info_reply['info_reply_no'];
                 $info_no = $info_reply['info_no'];
                 $info_reply_write_member_no = $info_reply['info_reply_write_member_no'];
                 $info_reply_content = $info_reply['info_reply_content'];
                 $info_reply_content = nl2br($info_reply_content); // /n 을 <br> 로 치환
                 $info_reply_write_time = $info_reply['info_reply_write_time'];

                 $find_write_member_sql = "select * from member where member_no='$info_reply_write_member_no'";
                 $find_write_member_result = mysqli_query($con,$find_write_member_sql);
                 $find_write_member_row = mysqli_fetch_array($find_write_member_result);
                 $member_id = $find_write_member_row['member_id'];
                 $member_profile_img = $find_write_member_row['member_profile_img'];
                 ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th><span><img src="<?php echo $member_profile_img;?>" class="rounded-circle" alt="Cinque Terre" width="30" height="30"></span><span class="text">&nbsp;&nbsp;<?php echo $member_id; ?></span></th>
                            <th class="text-right"><?php echo $info_reply_write_time; ?></th>
                            
                        </tr>
                        
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2"><?php echo $info_reply_content; ?></td>
                        </tr>
                        <tr>
                        <?php
                        if($member_id==$_SESSION['login_id'] ||'admin'==$_SESSION['login_id']){
                            ?>
                                <td class="text-right" colspan="2">
                                    <form name="info_reply_delete_form_<?php echo $info_reply_no;?>" id="info_reply_delete_form_<?php echo $info_reply_no;?>" role="form" method="post"
                                        action="<?php echo $_SERVER['PHP_SELF'];?>">
                                        <input type="hidden" name="info_no" value="<?php echo $info_no; ?>" />
                                        <input type="hidden" name="info_reply_no" id="info_reply_no" value="<?php echo $info_reply_no; ?>" />
                                        <input type="hidden" name="info_reply_delete_action" value="true" />
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

    </div>

    <br><br>

    <!-- footer 삽입 -->
    <?php include_once("footer.php"); ?>
    <!-- loader 삽입 -->
    <?php include_once("loader.php");?>
</body>

<!-- The Modal -->
<!-- <div class="modal fade" id="board-delete-check-modal" data-backdrop="static" data-keyboard="false"> -->
<div class="modal fade" id="info-delete-check-modal">
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
                <form name="info_delete_form" id="info_delete_form" role="form" method="post"
                    action="<?php echo $_SERVER['PHP_SELF'];?>">
                    <h4>정말로 삭제하시겠어요?</h4>
                    <input type="hidden" name="info_no" value="<?php echo $info_no; ?>" />
                    <input type="hidden" name="info_delete_action" value="true" />
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
                        onclick="infoDelete()">삭제</button>
                </p>
                
            </div>

        </div>
    </div>
</div>








</html>