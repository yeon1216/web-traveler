<script>

// 아이디 중복 체크 함수
function idConditionCheck() {
    console.log('idConditionCheck() 실행');
    // 회원가입 아이디 입력창에 입력한 아이디
    var join_id = document.getElementById('join_id').value;

    // 1. 아이디가 입력되어있는지 검사
    if (join_id.length == 0) {
        document.getElementById('is_possible_join_id').value = 1;
        alert('아이디를 입력해주세요');
        document.getElementById('join_id').focus();
        return false;
    }

    // 2. 아이디 조건에 충족하는지 검사
    if (join_id.length < 3) {
        document.getElementById('is_possible_join_id').value = 2;
        alert('아이디는 3 ~ 8자로 입력해주세요');
        document.getElementById('join_id').focus();
        return false;
    }
    if(join_id.length>9){
        document.getElementById('is_possible_join_id').value = 2;
        alert('아이디는 3 ~ 8자로 입력해주세요');
        document.getElementById('join_id').focus();
        return false;
    }

    // 3. 아이디 중복 검사
    $.ajax({
        type: 'post',
        dataType: 'text',
        url: 'ajax.php',
        data: {mode:'join_id_check', join_id:join_id},
        success: function(data){
            if(data==0){
                // 아이디 사용 가능
                // 4. 모든 조건 충족시
                document.getElementById('is_possible_join_id').value = 4;
                document.getElementById('join_id').readOnly=true;
                document.getElementById('join_pw').focus();
                alert('사용가능한 아이디입니다');
            }else{
                // 아이디 중복
                document.getElementById('is_possible_join_id').value = 3;
                alert('이미 사용중인 아이디입니다');
                document.getElementById('join_id').focus();
                return false;
            }
        },
        error: function(request,status,error){
            console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
        }
    });    
}

// 회원가입 post 요청 전 검사 함수
function joinSubmit() {
    var is_possible_join_id = document.getElementById('is_possible_join_id').value;
    var join_pw = document.getElementById('join_pw').value;
    var join_pw_check = document.getElementById('join_pw_check').value;
    var join_email = document.getElementById('join_email').value;
    if(is_possible_join_id != 4){
        alert('아이디 중복검사를 해주세요');
        document.getElementById('is_possible_join_id').focus();
        return false;
    }
    // 비밀번호 조건검사 추가하기
    if(join_pw.length==0){
        alert('비밀번호를 입력해주세요');
        document.getElementById('join_pw').focus();
        return false;
    }

    if (join_pw != join_pw_check) {
        alert('비밀번호와 비밀번호 확인이 일치하지 않습니다');
        document.getElementById('join_pw_check').focus();
        return false;
    }

    // 이메일 조건검사
    if(join_email.length==0){
        alert('이메일을 입력해주세요');
        document.getElementById('join_email').focus();
        return false;
    }
    if(join_email.length>0){
        var regExp = /^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/i;
        if(join_email.match(regExp)==null){
            alert('이메일 주소가 형식에 맞지 않습니다.');
            document.getElementById('join_email').focus();
            return false;
        }
    }

    // 이메일 중복검사
    $.ajax({
        type: 'post',
        dataType: 'text',
        url: 'ajax.php',
        data: {mode:'join_email_check', join_email:join_email},
        success: function(data){
            console.log("data: "+data);
            if(data==0){
                // 이메일 사용 가능
                // 모든 조건 충족시 submit 호출
                document.join_form.submit();
            }else{
                // 이메일 중복
                alert('이미 사용중인 이메일입니다');
                document.getElementById('join_email').focus();
                return false;
            }
        },
        error: function(request,status,error){
            console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
        }
    });

}
</script>

<?php


$current_page=$_SERVER['PHP_SELF'];
// post 요청 시작
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 로그인 요청 시작
    if($_POST['login_action']){
        // $web_data = json_decode($_SESSION["web_data"],true);
        $login_id = $_POST['login_id'];
        $login_pw = $_POST['login_pw'];
        include("dbcon.php");
        $sql = "SELECT * FROM member WHERE member_id='$login_id'";
        $result = mysqli_query($con,$sql);
        $login_member = mysqli_fetch_assoc($result);
        if(!$login_member['member_id'] || !($login_pw === $login_member['member_pw'])){
            // 로그인 실패
            echo "<script>alert('로그인 정보가 일치하지 않습니다');</script>";
            echo "<script>history.back()</script>";
            // echo "<script>window.location.replace('$current_page');</script>";
            exit;
        }
        $_SESSION["login_id"]=$login_id;
        echo "<script>alert('로그인 되었습니다');</script>";
        echo "<script>history.back()</script>";
        // echo "<script>window.location.replace('$current_page');</script>";
        exit;

        // 데이터베이스 접속 종료
        mysqli_close($con);

    } // 로그인 요청시 끝

    // 회원가입 요청 시작
    if($_POST['join_action']){

        // 인증메일 보내기
        

        // 데이터베이스 열기
        include("dbcon.php");
    
        // post 요청에서 값 받기
        $join_id = $_POST['join_id'];
        $join_pw = $_POST['join_pw'];
        $join_email = $_POST['join_email'];

        $sql = "INSERT INTO member (member_id, member_pw, member_email)
                VALUES('$join_id','$join_pw','$join_email');";
        $result = mysqli_query($con,$sql);
        if($result){
            // 회원가입 성공
            echo "<script>alert($join_id+'님 회원가입에 성공하였습니다');</script>";
            echo "<script>history.back()</script>";
            // echo "<script>window.location.replace('$current_page');</script>";
        }

    } // 회원가입 요청 끝

} // post 요청시 끝

// get 요청 시작
if($_SERVER["REQUEST_METHOD"] == "GET"){
    // 로그아웃 요청 시작
    if(isset($_GET['logout_action'])){
        $_SESSION['login_id']=null;
        echo "<script>alert('로그아웃 되었습니다');</script>";
        echo "<script>history.back()</script>";
        // echo "<script>window.location.replace('$current_page');</script>";
        exit;
    } // 로그아웃 요청시 끝

    
} // get 요청시 끝

?>


<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">Traveler</a>
            <!-- <h6 class='font-italic text-white text-center' vertical-align='middle'> _일상을 여행으로</h6> -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
                aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="oi oi-menu"></span> Menu
            </button>

            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <!-- <li class="nav-item active dropdown"> -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="trip_list.php" id="dropdown04" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">여행 이야기</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown04">
                            <a class="dropdown-item" href="trip_list.php?trip_type=0">전체</a>
                            <a class="dropdown-item" href="trip_list.php?trip_type=1">여행 후기</a>
                            <a class="dropdown-item" href="trip_list.php?trip_type=2">여행 계획</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="board_list.php" id="dropdown04" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">게시판</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown04">
                            <a class="dropdown-item" href="board_list.php?board_type=0">공지사항</a>
                            <a class="dropdown-item" href="board_list.php?board_type=1">자유 게시판</a>
                        </div>
                    </li>
                    <!-- <li class="nav-item">
                    
                    <a class="nav-link" href="trip_product.php" id="dropdown04" onclick="mailSend()">여행용품 거래</a>
                    </li> -->

                    <li class="nav-item">
                        <a class="nav-link" href="info_list.php" id="dropdown04">여행 정보</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="chat.php" id="dropdown04">실시간 여행 정보</a>
                    </li>
                    <?php
                    if(isset($_SESSION['login_id'])){
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="trip_write.php" id="dropdown04">나의 여행 남기기</a>
                        </li>
                        <?php
                    }
                    ?>
                    
                    <li class="nav-item">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </li>
                    <li class="nav-item">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    
                    </li>
                    <?php
						if($_SESSION['login_id']){
                            $login_id = $_SESSION['login_id'];
                            // 작성자 아이디, 프로필사진 찾기
                            include('dbcon.php');
                            $find_write_member_sql = "select * from member where member_id='$login_id'";
                            $find_write_member_result = mysqli_query($con,$find_write_member_sql);
                            $find_write_member_row = mysqli_fetch_array($find_write_member_result);
                            $nav_member_profile_img = $find_write_member_row['member_profile_img'];
                            mysqli_close($con);
                    ?>
                    <li class="nav-item" style="margin-top:5px;">
                        <span><img src="<?php echo $nav_member_profile_img;?>" class="rounded-circle" alt="Cinque Terre" width="40" height="40"></span>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="board_list.php" id="dropdown04" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false"><?php echo $login_id."님 "; ?></a>
                        <div class="dropdown-menu" aria-labelledby="dropdown04">
                            <!-- <a class="dropdown-item" href="trip_write.php">나의 여행 남기기</a> -->
                            <?php
                            if($_SESSION['login_id']=='admin'){
                                echo "<a class='dropdown-item' href='mypage.php?mypage_type=0'>마이/관리페이지</a>";
                            }else{
                                echo "<a class='dropdown-item' href='mypage.php?mypage_type=0'>마이페이지</a>";
                            }
                            ?>
                            <!-- <a class='dropdown-item' href='mypage.php?mypage_type=0'>마이페이지</a> -->
                            <a class="dropdown-item" href="<?php echo $_SERVER['PHP_SELF'];?>?logout_action=true">로그아웃</a>
                        </div>
                    </li>
                    <?php
						}else{
					?>
                    <li class="nav-item"><a href="#" class="nav-link" data-toggle="modal"
                            data-target="#login-modal">Login</a></li>
                    <?php
				  		}
			  		?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- END nav -->

    <!-- The Modal -->
<div class="modal fade" id="login-modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <!-- <div class="modal-dialog modal-lg"> -->
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">로그인</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
                    <label>아이디&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; <input type="text" name="login_id" /></label><br />
                    <label>비밀번호 &nbsp;:&nbsp; <input type="password"
                            name="login_pw" /></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="submit" class="btn btn-secondary">로그인</button>
                    <input type="hidden" name="login_action" value="true" />
                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <p>
                    <!-- 로그인 정보를 잊으셨나요?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-toggle="modal"
                        data-target="#">로그인 정보 찾기</button><br /> -->
                </p>
                <p>
                    계정이 없으신가요?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-toggle="modal"
                        data-target="#join-modal">회원가입</button>
                </p>
                <div>
                    <?php
							// $web_data = $_SESSION['$web_data'];
							// echo $web_data;
							// echo "<script>alert('dd ' . $web_data);</script>";
						?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="join-modal" data-backdrop="static" data-keyboard="false">
        <!-- <div class="modal-dialog"> -->
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">회원가입</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</button>
                </div>

                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>" name="join_form">
                    <!-- Modal body -->
                    <div class="modal-body">
                        <label>아이디&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; <input type="text" id="join_id"
                                name="join_id" /></label>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <button type="button" onclick="idConditionCheck()" class="btn btn-sm btn-secondary">아이디 중복 확인</button>
                        <br />
                        <label>비밀번호 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; <input type="password" id="join_pw"
                                name="join_pw" /></label><br />
                        <label>비밀번호 확인&nbsp; :&nbsp; <input type="password" id="join_pw_check"
                                name="join_pw_check" /></label><br />
                        <label>이메일&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; <input type="text" id="join_email"
                                name="join_email" /></label>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <!-- <button type="button" onclick="phoneConditionCheck()" class="btn  btn-sm btn-secondary">전화번호 인증</button> -->
                        <input type="hidden" name="join_action" value="true" />
                        <input type="hidden" id="is_possible_join_id" name="is_possible_join_id" value="0" />
                        <!-- 0: 인증 안함, 1: 아이디 미입력, 2: 아이디 조건 미충족, 3: 아이디 중복, 4: 회원가입 가능 -->
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <p>
                        </p>
                        <p>
                            <button type="button" onclick="joinSubmit()" class="btn btn-secondary">회원가입</button>
                        </p>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        // 모달 창 닫힐 때 입력값 초기화
        $(document).ready(function() {
            $(".modal").on("hidden.bs.modal", function() {
                $("input[name=login_id]").val("");
                $("input[name=login_pw]").val("");

            
                $("#join_id").val("");
                $("#join_id").removeAttr('readonly');
                $("#join_pw").val("");
                $("#join_pw_check").val("");
                $("#join_email").val("");
                $("#is_possible_join_id").val("0");
            });
        });
    </script>