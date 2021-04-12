<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header 삽입 -->
    <?php include_once("header.php");?>
</head>
<script>
function get_ip(){
 
}
</script>
<body>
    
<!-- nav 삽입 -->
<?php //include_once("nav.php");?>
<?php 
        $ip = (string)$_SERVER['REMOTE_ADDR'];
        $login_id = $_SESSION['login_id'];
        $a = $_COOKIE[$login_id];
        // $a = $_COOKIE['192.168.184.1'];
        echo 'ip 확인: '.$_SERVER['REMOTE_ADDR'].'<br>';
        echo 'ip 확인: '.$ip.'<br>';
        echo 'login_id 확인: '.$login_id.'<br>';
        echo 'aaaa: '.$a.'<br>';
        $read_trip_arr = explode("|",$a);
        foreach($read_trip_arr as $read_trip){
            echo $read_trip;
        }

?>
<div class="container">
<!-- <button type="button" onclick="get_ip()">ip얻기</button> -->
<?php
// $ip = $_SERVER['REMOTE_ADDR'];
// echo '사용자 ip : '.$_SERVER['REMOTE_ADDR'].'<br>';
// echo "<script>alert('$ip');</script>"

?>
</div>

<!-- footer 삽입 -->
<?php //include_once("footer.php");?>

<!-- loader 삽입 -->
<?php include_once("loader.php");?>
    
</body>
</html>