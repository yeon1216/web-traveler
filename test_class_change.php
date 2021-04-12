<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header 삽입 -->
    <?php include_once("header.php");?>
</head>

<body>

<div id="TestID">아이디</div>



<div class="TestClass">클래스</div>



<div onclick="Click()">클릭</div>







<script>

function Click(){
    alert('aa');

$(".TestClass").attr('class','NoClass');          //TestClass를 NoClass로 변경한다.

$("#TestID").attr('id','NoID');                    //TestID를 NoID로 변경한다.

$(".TestClass").attr('id','NoID');                  //TestClass의 id를 NoID로 만든다.

$("#TestID").attr('class','NoClass');             //TestID의 class를 NoClass로 만든다
window.location.replace('test_class_change.php');

}

</script>
    
<!-- nav 삽입 -->
<?php include_once("nav.php");?>

<!-- footer 삽입 -->
<?php include_once("footer.php");?>

<!-- loader 삽입 -->
<?php include_once("loader.php");?>
    
</body>
</html>

