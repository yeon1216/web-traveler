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

<div class="container">
<div class="sidebar-box">
    <form action="trip_list.php" class="search-form" >
        <div class="form-group">
            <span class="icon icon-search"></span>
            <input maxlength="10" type="text" id="search_location" 
                name="search_location" class="form-control" placeholder="장소를 검색해주세요" 
                value="<?php echo $search_word;?>" autocomplete="address-level2">
        </div>
    </form>
</div>
</div>

<!-- footer 삽입 -->
<?php include_once("footer.php");?>

<!-- loader 삽입 -->
<?php include_once("loader.php");?>
    
</body>
</html>