<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header 삽입 -->
    <?php include_once("header.php");?>
</head>
<?php
include_once('function.php');
mailer('관리자','qwse8770@naver.com','sykim8770@gmail.com','이메일 제목','이메일 내용');
?>
<body>
    
<!-- nav 삽입 -->
<?php include_once("nav.php");?>

<!-- footer 삽입 -->
<?php include_once("footer.php");?>

<!-- loader 삽입 -->
<?php include_once("loader.php");?>
    
</body>
</html>