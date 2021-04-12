<?php
    include('dbcon.php');
    $sql = "SELECT * FROM chat ORDER BY chat_no DESC; ";
    $result = mysqli_query($con,$sql);
    if(mysqli_num_rows($result)>0){
        while($chat = mysqli_fetch_array($result)){
            $chat_no = $chat['chat_no'];
            $chat_write_member_no = $chat['chat_write_member_no'];
            $chat_write_time = $chat['chat_write_time'];
            $chat_content = nl2br($chat['chat_content']);
            
            $find_write_member_sql = "SELECT * FROM member WHERE member_no='$chat_write_member_no';";
            $find_write_member_result = mysqli_query($con,$find_write_member_sql);
            $find_write_member_row = mysqli_fetch_assoc($find_write_member_result);
            $chat_write_member_id = $find_write_member_row['member_id'];
            $chat_write_member_profile_img = $find_write_member_row['member_profile_img'];
            ?>

            <!-- ###### -->
            <div class="media border" style="padding:10px">
                <div class="media-body">
                        <span>
                        <img src="<?php echo $chat_write_member_profile_img;?>" class="rounded-circle"style="width:20px; height:20px;">
                            <strong> <?php echo $chat_write_member_id;?></strong> <small><i>Posted on <?php echo $chat_write_time;?></i></small></span><br>
                            <br><h6><?php echo $chat_content;?></h6>      
                </div>
            </div><br>
            <!-- ###### -->
            <?php

        }
    }else{
        // chat 없음
    }
    mysqli_close($con);
?>
