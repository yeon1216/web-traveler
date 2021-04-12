<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header 삽입 -->
    <?php include_once("header.php");?>
</head>

<body>
    
<!-- nav 삽입 -->
<?php include_once("nav.php");?>
<br><br><br><br>
<div class="container">
<h2>페이징 테스트</h2><br><br><br>
<table class="table table-striped" style="text-align:center; ">
    <thead>
    <tr>
        <th style="width:20%;">번호</th>
        <th>내용</th>
    </tr>
    </thead>
    <tbody>


<?php
include_once('dbcon.php');
/**
 * << 페이징을 하는 코드 >>
 * 
 * $current_page(현재 페이지)
 * $display_page_num(게시글 리스트 밑의 페이징 갯수) = 5
 * $per_page_num(한페이지에 들어갈 게시글 수) = 10
 * $total_page(전체 페이지 수) = N/
 * $total_board(전체 게시글 수) = N
 */
// $sql = "SELECT * FROM paging;";
// $result = mysqli_query($con,$sql);
// $row = mysqli_fetch_assoc($result);

$page_set = 10; // 한페이지 줄수
$block_set = 5; // 한페이지 블럭수

$sql = "SELECT count(*) c FROM paging;";
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_assoc($result);
$total = $row['c'];

$total_page=ceil($total/$page_set);
$total_block=ceil($total_page/$block_set);

$page = $_GET['page'];
if(!$page) $page=1;

$block=ceil($page/$block_set); 
$limit_idx=($page-1)*$page_set;

$sql = "SELECT * FROM paging ORDER BY paging_no DESC LIMIT $limit_idx, $page_set;";
$result = mysqli_query($con,$sql);
// $sql = "SELECT * FROM paging;";
// $result = mysqli_query($con,$sql);
if(mysqli_num_rows($result)){
    while($paging = mysqli_fetch_array($result)){
        // 디비에서 페이징 정보 꺼내기
        $paging_no = $paging['paging_no'];
        $paging_content = $paging['paging_content'];
        ?>
            <tr>
                <td><?php echo $paging_no ?></td>
                <td><?php echo $paging_content ?></td>
            </tr>
        <?php
    }
}
?>
    </tbody>
</table>
<?php
// 블럭 설정
$first_page = (($block - 1) * $block_set) + 1; // 첫번째 페이지번호
$last_page = min ($total_page, $block * $block_set); // 마지막 페이지번호
$prev_block = $block -1; // 이전 블럭
$prev_block_page = ($prev_block-1)*$block_set+1; // 이전 블럭 페이지
$next_block = $block +1; // 다음 블럭
$next_block_page = ($next_block-1)*$block_set+1; // 다음 블럭 페이지
?>
<br>
            <div >    
                <ul class="pagination justify-content-center">
                    <?php
                    echo ($prev_block > 0) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?page=1'> << </a></li>" : "";
                    echo ($prev_block > 0) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?page=$prev_block_page'><</a></li>" : "";
                    for($i=$first_page; $i<=$last_page;$i++){
                        echo ($i!=$page) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?page=$i'>$i</a></li>" 
                                            : "<li class='page-item active'><a class='page-link' href='#'>$i</a></li>";
                    }
                    echo ($next_block <= $total_block) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?page=$next_block_page'>></a></li>" : "";
                    echo ($next_block <= $total_block) ? "<li class='page-item'><a class='page-link' href='$PHP_SELF?page=$total_page'> >> </a></li>" : "";
                    ?>
                </ul>
            </div>
</div>
<br><br><br><br>
<!-- footer 삽입 -->
<?php include_once("footer.php");?>

<!-- loader 삽입 -->
<?php include_once("loader.php");?>
    
</body>
</html>