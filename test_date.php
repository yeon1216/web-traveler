<?php



// 현재 시간 받기
// echo "현재 시간   ".date("Y-m-d H:i:s")."<br>";
echo "현재시간<br>";
date_default_timezone_set("Asia/Seoul");
$dateStr = date("Y-m-d",time());
$current_time = strtotime($dateStr." +0 day");
echo $current_time.'<br>';
echo date("Y-m-d",$current_time), "<br>";

// 시간 정하기
echo "<br>";echo "내가 정한 시간<br>";
$trip_start_day = strtotime("2019-08-17");
echo $trip_start_day.'<br>';
echo date("Y-m-d",$trip_start_day), "<br>";

// 여행 계획일 때
echo "<br>";echo "여행 계획일 경우<br>";
if($current_time<=$trip_start_day){
    echo "통과<br>";
}else{
    echo "안됨<br>";
}

// 여행 후기일때
echo "<br>";echo "여행 후기일 경우<br>";
if($current_time>=$trip_start_day){
    echo "통과<br>";
}else{
    echo "안됨<br>";
}

// echo "<br>";echo "<br>";
// echo '2019-08-15 : ' . strtotime('2019-08-15'), "<br>";
// echo '2019-08-16 : ' . strtotime('2019-08-16'), "<br>";
// echo '2019-08-17 : ' . strtotime('2019-08-17'), "<br>";
?>
