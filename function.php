<?php
include_once('./PHPMailer/PHPMailerAutoload.php');

function mailer($fname, $fmail, $to, $subject, $content)
{
    
    echo $fname.'<br>';
    echo $fmail.'<br>';
    echo $to.'<br>';
    echo $subject.'<br>';
    echo $content.'<br>';

    echo "<script>console.log('mailer() 함수 실행')</script>";
    try{
        echo "<script>console.log('try문 실행')</script>";

        if(empty($mail)){
            echo 'mail empty true<br>';
        }else{
            echo 'mail empty false<br>';
        }

        $mail = new PHPMailer();

        if(empty($mail)){
            echo 'mail empty true<br>';
        }else{
            echo 'mail empty false<br>';
        }

        $mail->IsSMTP();

        $mail->SMTPSecure="ssl";
        $mail->SMTPAuth=true;

        $mail->Host = "smtp.naver.com";
        $mail->Port=465;
        $mail->Username="qwse8770@naver.com";
        $mail->Password="abcd1234";

        $mail->CharSet='UTF-8';
        $mail->From=$fmail;
        $mail->FromName=$fname;
        $mail->Subject=$subject;
        $mail->msgHTML($content);
        $mail->addAddress($to);
        
        if($mail->send()){
            echo "<script>console.log('메일전송 성공')</script>";
        }else{
            echo "<script>console.log('메일전송 실패')</script>";
            echo "메일 전송 실패 error : ".$mail->ErrorInfo;
        }
    }catch(Exception $e){
        echo "<script>console.log('catch문 실행')</script>";
        $e = $e->getMessage() . '(오류코드: '.$e->getCode().')';
        echo "<script>console.log('error : '+$e)</script>";    
    }
}

function uploadImg($file){
    $target_dir = "upload/";
    // $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($file["tmp_name"]);
        if($check !== false) {
            // echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "<script>alert('File is not an image.');</script>";
            // echo "File is not an image.";
            $uploadOk = 0;
        }
    }
    // Check if file already exists
    if (file_exists($target_file)) {
        echo "<script>alert('Sorry, file already exists.');</script>";
        // echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 5000000) {
        echo "<script>alert('Sorry, your file is too large.');</script>";
        // echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
        // echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "<script>alert('Sorry, your file was not uploaded.');</script>";
        // echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            // echo "<p>The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.</p>";
            // echo "<br><img src=upload/". basename( $_FILES["fileToUpload"]["name"]). ">";
            // echo "<br><button type='button' onclick='history.back()'>돌아가기</button>";
            $img_dir = "upload/".basename($file['name']);
            return $img_dir;
        } else {
            echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
            // echo "<p>Sorry, there was an error uploading your file.</p>";
            // echo "<br><button type='button' onclick='history.back()'>돌아가기</button>";
        }
    }
}

?>