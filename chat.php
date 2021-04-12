<?php session_start();
if(isset($_COOKIE['aa'])){ // aa 쿠키가 있는 경우 (aa 쿠키 삭제)
    echo "<script>
    // aa 쿠키 삭제
    console.log('aa 쿠키가 있는 경우 (aa 쿠키 삭제)');
    var name = 'aa';
    var date = new Date();
    date.setDate(date.getDate()-1);

    var willCookie ='';
    willCookie += name + '=remove;';
    willCookie +='expires='+date.toUTCString();

    document.cookie = willCookie;
    </script>";
}else{ // aa 쿠키가 없는 경우 (chat_count 쿠키 값을 10로 변경)
    echo "<script>
    // chat_count 쿠키 값 변경
    console.log('aa 쿠키가 없는 경우 (chat_count 쿠키 값을 2로 변경)');
    var name = 'chat_count';
    var value = 10;
    var date = new Date();
    date.setDate(date.getDate()+1);

    var willCookie ='';
    willCookie += name + '=' + encodeURIComponent(value) +';';
    willCookie +='expires='+date.toUTCString()+'';

    document.cookie = willCookie;
    </script>";
}

echo "<script>

        // 페이지 새로고침
        setTimeout(function(){
            console.log('하핳하하하하ㅏ하하핳');
            $('#chat_div').load(document.URL +  ' #chat_div');
            $('#more_chat_btn_div').load(document.URL +  ' #more_chat_btn_div');
        },100);        
</script>";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header 삽입 -->
    <?php include_once("header.php");?>
</head>

<?php
/**
 * 채팅 작성 post 요청
 */
if(isset($_POST['chat_write_action'])){
    include('dbcon.php');
    $chat_write_member_no = $_POST['chat_write_member_no'];
    $chat_content = $_POST['chat_content'];
    
    $sql = "INSERT INTO chat (chat_write_member_no, chat_content)
            VALUES ($chat_write_member_no,'$chat_content');";
    $result = mysqli_query($con,$sql);
    mysqli_close($con);
    // echo "<script>window.location.replace('chat.php');</script>";
    echo "<script>$('#chat_div').load(document.URL +  ' #chat_div');</script>";
    // exit;
}
?>

<body>
    
<!-- nav 삽입 -->
<?php include_once("nav.php");?>
<br><br><br><br><br>
<div class="container">
<h2>실시간 여행 정보 공유</h2>
<p>현재 여행정보를 공유해주세요</p><br><br>
<script>
    $(document).ready(function() {
        /**
            실시간 정보 글자수를 카운트하는 코드
         */
        $('#chat_content').on('keyup', function() { 
            $('#counter').html("("+$(this).val().length+" / 최대 500자)");
            if($(this).val().length > 500) {
                $(this).val($(this).val().substring(0, 500));
                alert("최대 500자까지 입력 가능합니다.");
                $(this).val(content.substring(0, 200));
                $('#counter').html("(500 / 최대 500자)");
            }
        });

        /**
            3초에 한번씩 새로운 채팅 있는 지 확인
         */
        check_chat = setInterval(function() {
            var count_chat = <?php 
                include('dbcon.php');
                $sql = "SELECT * FROM chat ORDER BY chat_no DESC; ";
                $result = mysqli_query($con,$sql);
                echo mysqli_num_rows($result);
                ?>;
            $.ajax({ // ajax를 이용하여 화면에 있는 채팅 갯수와 디비에 있는 채팅 갯수 비교
                type: 'post',
                dataType: 'text',
                url: 'ajax.php',
                data: {mode:'check_chat',count_chat:count_chat},
                success: function(data){
                    console.log("data: "+data);
                    if(data==1){ // 화면의 채팅 갯수와 디비의 채팅 갯수 다름 --> 화면 다시 로드
                        // location.reload();
                        $('#chat_div').load(document.URL +  ' #chat_div');
					    return false;
                    }
                },
                error: function(request,status,error){
                    console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
                }
            });
        }, 3000);
    });

/**
    채팅 작성
 */
function chatWrite(){
    // console.log('chatWrite start');
    var login_id = <?php
                        if(empty($_SESSION['login_id'])){
                            echo 0;
                        }else{
                            echo 1;
                        }
                    ?>;
    if(login_id==0){ // 로그인을 안했으면 댓글 안써지도록하는 조건
        alert('로그인을 해주세요');
        return false;
    }
    if(document.getElementById('chat_content').value.length==0){ // 글을 작성하지 않았을 경우 댓글 안써지도록 예외처리
        alert('정보를 입력해주세요');
        document.getElementById('chat_content').focus();
        return false;
    }
    // document.chat_write_form.submit(); // 댓글 작성 요청
    $.ajax({
        type: 'post',
        dataType: 'text',
        url: 'ajax.php',
        data: {mode:'chat_write_action',chat_content:document.getElementById('chat_content').value},
        success: function(data){
            // console.log('data: '+data);
            $('#chat_div').load(document.URL +  ' #chat_div');
            document.getElementById('chat_content').value="";
            document.getElementById('counter').innerHTML="(0 / 최대 500자)";
        },
        error: function(request,status,error){
            console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
        }
    });
    // console.log('chatWrite end');
}
</script>

<?php
    /**
     * 세션에 저장된 로그인 아이디를 통해 로그인한 멤버 번호 찾는 코드
     */
    include('dbcon.php');
    $login_id = $_SESSION['login_id'];
    $sql = "select member_no from member where member_id='$login_id';";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $login_member_no = $row['member_no'];
    mysqli_close($con);
?>

<!-- 채팅 작성 양식 -->
<form name="chat_write_form" id="chat_write_form" role="form" method="post" action="chat.php">
    <div class="mb-3">
        <label for="content">실시간 정보공유 작성</label>&nbsp;&nbsp;<span style="color:#aaa;" id="counter">(0 / 최대 500자)</span>
        <textarea class="form-control" rows="5" name="chat_content" id="chat_content"></textarea>
    </div>
    
    <input type="hidden" id="chat_write_member_no" name="chat_write_member_no" value="<?php echo $login_member_no; ?>"/>
    <input type="hidden" id="chat_write_action" name="chat_write_action" value="true"/>
    <p class="text-right">
        <button type="button" class="btn btn-sm btn-secondary" onclick="chatWrite()">실시간 정보 등록</button>
    </p>
</form> <br><br>

<!-- 채팅 리스트 -->
<div id="chat_div">
<?php
    include('dbcon.php');
    if(isset($_COOKIE['chat_count'])){
        $chat_count = $_COOKIE['chat_count'];
    }else{
        $chat_count = 10;
    }
    $sql = "SELECT * FROM chat ORDER BY chat_no DESC LIMIT 0,$chat_count; ";
    $result = mysqli_query($con,$sql);
    if(mysqli_num_rows($result)>0){
        while($chat = mysqli_fetch_array($result)){
            $chat_no = $chat['chat_no'];
            $chat_write_member_no = $chat['chat_write_member_no'];
            $chat_write_time = $chat['chat_write_time'];
            $chat_content = nl2br($chat['chat_content']);
            
            $find_write_member_sql = "SELECT * FROM member WHERE member_no='$chat_write_member_no';";
            $find_write_member_result = mysqli_query($con,$find_write_member_sql);
            $find_write_member_row = mysqli_fetch_assoc($find_write_member_result);
            $chat_write_member_id = $find_write_member_row['member_id'];
            $chat_write_member_profile_img = $find_write_member_row['member_profile_img'];
            if($_SESSION['login_id']==$chat_write_member_id){
                ?>
                <div class="media border" style="padding:10px; background:#ddffffff; margin:5px">
                    <div class="media-body">
                            <span>
                            <img src="<?php echo $chat_write_member_profile_img;?>" class="rounded-circle"style="width:20px; height:20px;">
                                <strong> <?php echo $chat_write_member_id;?></strong> <small><i>&nbsp;&nbsp;&nbsp;<?php echo $chat_write_time;?></i></small></span><br>
                                <br><h6><?php echo $chat_content;?></h6>      
                    </div>
                </div>
                <?php
            }else{
                ?>
                <div class="media border" style="padding:10px; margin:5px">
                    <div class="media-body">
                            <span>
                            <img src="<?php echo $chat_write_member_profile_img;?>" class="rounded-circle"style="width:20px; height:20px;">
                                <strong> <?php echo $chat_write_member_id;?></strong> <small><i>&nbsp;&nbsp;&nbsp;<?php echo $chat_write_time;?></i></small></span><br>
                                <br><h6><?php echo $chat_content;?></h6>      
                    </div>
                </div>
                <?php
            }
        }
    }else{
        // chat 없음$('#chat_div').load(document.URL +  ' #chat_div');
    }
    mysqli_close($con);
?>

</div>
<!-- 채팅 리스트 끝 -->
<br><br>

<div id="more_chat_btn_div">
    <?php
    // 1. 쿠키 chat_count 가지고오기
    $chat_count_in_cookie = $_COOKIE['chat_count'];
    // 2. 디비 채팅 갯수 가지고오기
    include('dbcon.php');
    $sql = "SELECT count(*) c FROM chat";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $chat_count_in_db = $row['c'];
    mysqli_close($con);
    // 3. 쿠키에 있는 chat_count와 디비의 채팅 갯수 비교
    if($chat_count_in_cookie<$chat_count_in_db){
        ?>
        <p class="text-center">
            <button type="button" class="btn btn-secondary btn-lg" onclick="more_chat();">더보기</button>
        </p>
        <?php
    }
    // 4. 쿠키 chat_count < 디비 채팅 갯수 --> '더보기'버튼 보여주기
    ?>
    <script>
    function more_chat(){
        /**
            쿠키에 저장되어있는 chat_count 읽기
         */
        var chat_count = 0;
        var name = 'chat_count';
        var cookies = document.cookie.split(';');
        for(var i in cookies){
            if(cookies[i].search(name)!=-1){
                chat_count = decodeURIComponent(cookies[i].replace(name+'=',''));
            }
        }
        console.log('before chat_count in more_chat: '+chat_count);

        /**
            chat_count 에서 10 더해주기
         */
        chat_count = parseInt(chat_count) + 10; 
        var value = chat_count;

        /**
            chat_count 쿠키 삭제
         */
        var date = new Date();
        date.setDate(date.getDate()-1);

        var willCookie ='';
        willCookie += name + '=remove;';
        willCookie +='expires='+date.toUTCString();

        document.cookie = willCookie;

        /**
            10 더해준 chat_count 쿠키에 저장
         */
        var date = new Date();
        date.setDate(date.getDate()+1);

        // name="aa";
        var willCookie ='';
        willCookie += name + '=' + encodeURIComponent(value) +';';
        willCookie +='expires='+date.toUTCString()+'';

        document.cookie = willCookie;
        console.log('after chat_count in more_chat: '+chat_count);

        /**
            새로고침
         */

        var date = new Date();
        date.setDate(date.getDate()+1);

        name="aa";
        value="aa";
        var willCookie ='';
        willCookie += name + '=' + encodeURIComponent(value) +';';
        willCookie +='expires='+date.toUTCString()+'';

        document.cookie = willCookie;
        
        // $('#chat_div').load(document.URL +  '? #chat_div');
        // location.href = location.href;
        self.location.reload(true);
    }
    </script>
</div><br><br>

</div>
<!-- footer 삽입 -->
<?php include_once("footer.php");?>

<!-- loader 삽입 -->
<?php include_once("loader.php");?>
    
</body>
</html>