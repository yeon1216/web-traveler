<?php session_start(); 
if(empty($_SESSION['login_id'])){
    // echo "<script>alert('잘못된 접근입니다.');</script>";
    echo "<script>window.location.replace('index.php');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header 삽입 -->
    <?php include_once("header.php");?>

    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script> 
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 
    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">
    <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>
</head>
<script>
    function boardUpdate(){
        var board_title = document.getElementById('board_title').value;
        var board_content = document.getElementById('summernote').value;
        if(board_title.length==0){
            alert('제목을 입력해주세요');
            document.getElementById('board_title').focus();
            return false;
        }
        if(board_content.length==0){
            alert('내용을 입력해주세요');
            document.getElementById('summernote').focus();
            return false;
        }
        document.board_update_form.submit();
    }
$(document).ready(function(){
    $('#summernote').summernote({
        heigh: 500,
        minHeight: null,
        maxHeight: null,
        focus: true
    });
});
$(document).ready(function(){
    $('#summernote').summernote();
});
</script>
<body>
    
<!-- nav 삽입 -->
<?php //include_once("nav.php");?>

<br><br>
<div class="container">
        <?php
        if($_GET['board_type']==0){
            echo "<h2>공지사항 수정</h2>";
        }elseif($_GET['board_type']==1){
            echo "<h2>자유게시글 수정</h2>";
        }elseif(!($_GET['board_type']==1 || $_GET['board_type']==0)){
            echo "<script>window.location.replace('board_list.php?board_type=0');</script>";
            exit;
        }
        ?>
        <?php
        include('dbcon.php');
        $board_no = $_GET['board_no'];
        $sql = "SELECT board_title, board_content FROM board WHERE board_no = '$board_no';";
        $result = mysqli_query($con,$sql);
        $row = mysqli_fetch_assoc($result);
        $board_title = $row['board_title'];
        $board_content = $row['board_content'];
        echo "<script>console.log('board_no: '+$board_no);</script>";
        echo "<script>console.log('board_count: '+$board_title);</script>";
        mysqli_close($con);
        ?>
        <form name="board_update_form" id="board_update_form" role="form" method="post" action="board_detail.php">
        <br><br>
            <div class="mb-3">
                <label for="title">제목</label>
                <input type="text" class="form-control" name="board_title" id="board_title" placeholder="제목을 입력해 주세요" value="<?php echo $board_title;?>">
            </div>
            <br><br>
            <div class="mb-3">
                <label for="content">내용</label>
                <textarea class="summernote" name="board_content" id="summernote"><?php echo $board_content; ?></textarea>
            </div>
            <input type="hidden" id="board_no" name="board_no" value="<?php echo $board_no; ?>"/>
            <input type="hidden" id="board_type" name="board_type" value="<?php echo $_GET['board_type']; ?>"/>
            <input type="hidden" id="board_update_action" name="board_update_action" value="true"/>
        </form>
        <br><br>
        <div class="col text-right">
            <a href="board_detail.php?board_type=<?php echo $_GET['board_type']; ?>&board_no=<?php echo $board_no; ?>" class="btn btn btn-secondary">취소</a>
            <a href="#" onclick="boardUpdate()" class="btn btn btn-secondary">수정</a>
        </div>
    </div>

    <br><br>


<!-- footer 삽입 -->
<?php //include_once("footer.php");?>

<!-- loader 삽입 -->
    <!-- loader -->
    <!-- <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px">
            <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
            <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10"
                stroke="#F96D00" /></svg></div> -->


    <!-- <script src="js/jquery.min.js"></script>  -->
    <script src="js/jquery-migrate-3.0.1.min.js"></script> 
    <script src="js/popper.min.js"></script> 
    <script src="js/bootstrap.min.js"></script> 
    <script src="js/jquery.easing.1.3.js"></script>  
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.stellar.min.js"></script> 
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script> 
    <script src="js/aos.js"></script>
    <script src="js/jquery.animateNumber.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <!-- <script src="js/jquery.timepicker.min.js"></script> -->
    <script src="js/scrollax.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false">
    </script>
    <!-- <script src="js/google-map.js"></script> -->
    <script src="js/main.js"></script>
    
</body>
</html>