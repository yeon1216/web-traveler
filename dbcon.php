<?php
// 데이터베이스 정보입력
$db_host="127.0.0.1";
$db_user="root";
$db_password="1216";
$db_name="traveler";
// 데이터베이스 연결
$con=mysqli_connect($db_host,$db_user,$db_password,$db_name); 
// 연결에 실패할 경우 예외처리
if(!$con){ die("연결 실패 : ".mysqli_connect_error()); }
session_start();
?>