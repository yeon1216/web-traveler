<?php session_start(); 
if(empty($_SESSION['login_id'])){
    echo "<script>alert('잘못된 접근입니다.');</script>";
    echo "<script>window.location.replace('index.php');</script>";
}else{
    if($_SESSION['login_id']!='admin'){
        echo "<script>alert('잘못된 접근입니다.');</script>";
        echo "<script>window.location.replace('index.php');</script>";
    }
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
    }else{
        echo "<script>alert('잘못된 접근입니다');</script>";
        echo "<script>window.location.replace('info_list.php');</script>";
        exit;
    }
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
function infoUpdate() {
    var info_title = document.getElementById('info_title').value;
    var info_content = document.getElementById('summernote').value;
    if (info_title.length == 0) {
        alert('제목을 입력해주세요');
        document.getElementById('info_title').focus();
        return false;
    }
    if (info_content.length == 0) {
        alert('내용을 입력해주세요');
        document.getElementById('summernote').focus();
        return false;
    }
    document.info_update_form.submit();
}

$(document).ready(function() {
    $('#summernote').summernote({
        heigh: 500,
        minHeight: 300,
        maxHeight: null
    });

    $("#fileToUpload").change(function() {
        readURL(this);
    });
});

function readURL(input) {
    var fileNm = $("#fileToUpload").val();
    if (fileNm != "") { 
        var ext = fileNm.slice(fileNm.lastIndexOf(".") + 1).toLowerCase();
        if (!(ext == "gif" || ext == "jpg" || ext == "png")) {
            alert("이미지파일 (.jpg, .png, .gif ) 만 업로드 가능합니다.");
            $("#fileToUpload").val("");
            return false;
        }
    }
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#image_section_span').css('display','none');
            $('#image_section').css('display','');
            $('#image_section').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<body>

    <!-- nav 삽입 -->
    <?php //include_once("nav.php");?>
    <br><br>
    <!-- <div id="summernote">Hello Summernote</div> -->

    <div class="container">
        <h2>여행정보 수정</h2><br><br>

        <br><br>
        <?php
        include('dbcon.php');
        

        // 수정할 글을 찾기
        $info_no = $_GET['info_no'];
        $sql = "SELECT * FROM info WHERE info_no='$info_no';";
        $result = mysqli_query($con,$sql);
        $info = mysqli_fetch_assoc($result);
        $info_title = $info['info_title'];
        $info_content = $info['info_content'];
        $info_representative_img = $info['info_representative_img'];

        $login_id = $_SESSION['login_id'];
        $sql = "SELECT member_no FROM member WHERE member_id='$login_id';";
        $result = mysqli_query($con,$sql);
        $row = mysqli_fetch_assoc($result);
        $login_member_no = $row['member_no'];
        mysqli_close($con);
        ?>
        <form name="info_update_form" id="info_update_form" role="form" method="post" action="info_detail.php"
            enctype="multipart/form-data">

            <div class="mb-3">
                <label for="title"><strong>대표사진</strong> </label>(선택하지 않으면 기본사진이 올라갑니다)
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td colspan="2"><input type="file" name="fileToUpload" id="fileToUpload"> </td> </tr>
                        <tr>
                            <td  style="width:20%"><label for="title">선택한 대표사진</label><br></td>
                            <td>
                                <span id="image_section_span" style="display:none"> 없 음 </span>
                                <img id="image_section" src="<?php echo $info_representative_img; ?>" width="300px" height="300px" >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br><br>

            <div class="mb-3">
                <label for="title">제목</label>
                <input type="text" class="form-control" name="info_title" id="info_title" placeholder="제목을 입력해 주세요"
                    maxlength="40" value="<?php echo $info_title; ?>">
            </div>

            <br><br>
            <div class="mb-3">
                <label for="content">내용</label>
                <textarea class="summernote" name="info_content" id="summernote"><?php echo $info_content; ?></textarea>
            </div><br><br>
            
            <input type="hidden" id="info_no" name="info_no" value="<?php echo $info_no; ?>" />
            <input type="hidden" id="page" name="page" value="<?php echo $page; ?>" />
            <input type="hidden" id="info_representative_img" name="info_representative_img" value="<?php echo $info_representative_img; ?>" />
            <input type="hidden" id="info_write_member_no" name="info_write_member_no" value="<?php echo $login_member_no; ?>" />
            <input type="hidden" id="info_update_action" name="info_update_action" value="true" />
        </form>
        <br><br>
        <div class="col text-right">
            <a href="#" class="btn btn btn-secondary" onclick="history.back();">취소</a>
            <a href="#" onclick="infoUpdate()" class="btn btn btn-secondary">수정</a>
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
    <script src="js/jquery-migrate-3.0.1.min.js">
    < /scr<?php session_start(); ?>ipt>  <
    script src = "js/popper.min.js" >
    </script>
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