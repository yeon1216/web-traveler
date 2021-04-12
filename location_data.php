
<!DOCTYPE html>
<html lang="en">

<head>

</head>



<body>

<?php
include_once('real_location_data.php');
$db_host="127.0.0.1";
$db_user="root";
$db_password="1216";
$db_name="traveler";

$con=mysqli_connect($db_host,$db_user,$db_password,$db_name); 

if(!$con){ die("연결 실패 : ".mysqli_connect_error()); }

// 테이블 제거
$sql = "DROP TABLE spot;";
$result = mysqli_query($con,$sql);

// 테이블 생성
$sql = "CREATE TABLE spot( spot_no INT PRIMARY KEY AUTO_INCREMENT, spot_city VARCHAR(50), spot_name VARCHAR(50) );";
$result = mysqli_query($con,$sql);

// 장소 데이터 추가
foreach($spot_arr as $spot_city => $spot_name_arr){
    foreach($spot_name_arr as $spot_name){
        $sql = "INSERT INTO spot (spot_city, spot_name)
                VALUES('$spot_city','$spot_name');";
        $result = mysqli_query($con,$sql);
    }
}

// 중복되는 지역이 있는지 체크
foreach($spot_arr as $spot_city => $spot_name_arr){
    foreach($spot_name_arr as $spot_name){
        $sql = "SELECT count(*) as c from spot where spot_name='$spot_name';";
        $result = mysqli_query($con,$sql);
        $row = mysqli_fetch_assoc($result);
        echo $spot_city.' --- '.$spot_name .': '.$row['c'].'<br>';
    }
}
?>

</body>

</html>