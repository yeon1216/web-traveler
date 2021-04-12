<?php
//Snoopy.class.php를 불러옵니다
// require($_SERVER['DOCUMENT_ROOT'].'usr/local/apache24/htdocs/explore/Snoopy-2.0.0.tar.gz/Snoopy.class.php');
// include_once('Snoopy-2.0.0.tar.gz/Snoopy.class.php');
require_once('Snoopy-2.0.0.tar.gz/Snoopy.class.php');
 
// //스누피를 생성해줍시다
// $snoopy = new Snoopy;
 
// //스누피의 fetch함수로 제 웹페이지를 긁어볼까요? :)
// $snoopy->fetch('http://dovetail.tistory.com/38');
 
// //결과는 $snoopy->results에 저장되어 있습니다
// //preg_match 정규식을 사용해서 이제 본문인 article 요소만을 추출해보도록 하죠
// preg_match('/<div class="article">(.*?)<\/div>/is', $snoopy->results, $text);
 
// //이제 결과를 보면...?
// echo 'dddd';
// echo $text[1];


$snoopy = new Snoopy;

// 헤더값에 따라 403 에러가 발생 할 경우 셋팅
$snoopy->agent = $_SERVER['HTTP_USER_AGENT'];
$snoopy->referer = "https://infotake.tistory.com";

$snoopy->fetch('https://infotake.tistory.com');

/* 모두 가져오기
$html = $snoopy->results;
echo $html;
 */

/*
 * 정규식 가져오기 (일부 사이트는 방지가 되어 있을 수 있으니 정규식 지정전에 전체 가져오기를 해보세요)
 */
preg_match('/<!doctype html>(.*?)<\/html>/is', $snoopy->results, $html);
echo $html[0];

?>
