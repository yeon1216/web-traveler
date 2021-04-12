<?php




$file = $_FILES['fileToUpload'];
$target_dir = "upload/";
$target_file = $target_dir . basename($file["name"]);

$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image

// echo count($file).'<br>';
// echo $file['tmp_name'].'<br>';
// echo $file['name'].'<br>';
// echo $file['size'].'<br>';

if(empty($file['tmp_name'])){
    echo "<script>alert('파일이 선택되지 않았습니다');</script>";
    echo "<script>history.back()</script>";
}else{
    // 프로필 사진 등록
    if(isset($_POST["profile_img_submit"])) {
        
        $check = getimagesize($file["tmp_name"]);
        if($check !== false) {
            // echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
            // Check if file already exists
            if (file_exists($target_file)) {
                // echo "<script>alert('Sorry, file already exists.');</script>";
                echo "<script>alert('파일이 이미 존재합니다');</script>";
                echo "<script>history.back()</script>";
                $uploadOk = 0;
            }else{
                // Check file size
                if ($file["size"] > 5000000) {
                    // echo "<script>alert('Sorry, your file is too large.');</script>";
                    echo "<script>alert('파일크기가 너무 큽니다');</script>";
                    echo "<script>history.back()</script>";
                    $uploadOk = 0;
                }else{
                    // Allow certain file formats
                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                    && $imageFileType != "gif" ) {
                        // echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
                        echo "<script>alert('jpg, jpeg, png, gif 이미지 파일만 업로드 가능합니다');</script>";
                        echo "<script>history.back()</script>";
                        $uploadOk = 0;
                    }else{
                        $upload_img_dir =  $target_dir . date('Y-m-d H:i:s').'.'.$imageFileType;
                        if (move_uploaded_file($file["tmp_name"], $upload_img_dir)) {
                            include_once('dbcon.php');
                            $login_id = $_SESSION['login_id'];
                            $sql="UPDATE member SET member_profile_img='$upload_img_dir' WHERE member_id='$login_id';";
                            $result = mysqli_query($con,$sql);
                            echo "<script>window.location.replace('mypage.php?mypage_type=0');</script>";
                            // echo "<script>
                            //     var name = 'temp_img_upload';
                            //     var value = '$upload_img_dir';
                            //     var day = 1;
                            //     var date = new Date();
                            //     date.setDate(date.getDate()+day);
                        
                            //     var willCookie ='';
                            //     willCookie += name + '=' + encodeURIComponent(value) +';';
                            //     willCookie +='expires='+date.toUTCString()+'';
                        
                            //     document.cookie = willCookie;
                            //     history.back();
                            // </script>";

                        } else {
                            echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
                            echo "<script>history.back()</script>";
                            // echo "<br><button type='button' onclick='history.back()'>돌아가기</button>";
                        }
                        // // Check if $uploadOk is set to 0 by an error
                        // if ($uploadOk == 0) {
                        //     // echo "<script>alert('Sorry, your file was not uploaded.');</script>";
                        //     echo "<script>alert('파일이 없로드 되지 않았습니다.');</script>";
                        //     echo "<script>history.back()</script>";
                        // } else {
                        // }
                    }
                }
            }
        } else {
            // echo "<script>alert('File is not an image.');</script>";
            echo "<script>alert('파일이 선택되지 않았거나 이미지 파일이 아닙니다');</script>";
            // echo "<script>history.back()</script>";
            // $uploadOk = 0;
        }
    }



    // 여행 글 작성할 때
    if(isset($_POST["submit"])) {
        
        $check = getimagesize($file["tmp_name"]);
        if($check !== false) {
            // echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
            // Check if file already exists
            if (file_exists($target_file)) {
                // echo "<script>alert('Sorry, file already exists.');</script>";
                echo "<script>alert('파일이 이미 존재합니다');</script>";
                echo "<script>history.back()</script>";
                $uploadOk = 0;
            }else{
                // Check file size
                if ($file["size"] > 5000000) {
                    // echo "<script>alert('Sorry, your file is too large.');</script>";
                    echo "<script>alert('파일크기가 너무 큽니다');</script>";
                    echo "<script>history.back()</script>";
                    $uploadOk = 0;
                }else{
                    // Allow certain file formats
                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                    && $imageFileType != "gif" ) {
                        // echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
                        echo "<script>alert('jpg, jpeg, png, gif 이미지 파일만 업로드 가능합니다');</script>";
                        echo "<script>history.back()</script>";
                        $uploadOk = 0;
                    }else{
                        $upload_img_dir =  $target_dir . date('Y-m-d H:i:s').'.'.$imageFileType;
                        if (move_uploaded_file($file["tmp_name"], $upload_img_dir)) {
                            // echo "<script>window.location.replace('trip_write.php?upload_img_dir=$upload_img_dir');</script>";
                            echo "<script>
                                var name = 'temp_img_upload';
                                var value = '$upload_img_dir';
                                var day = 1;
                                var date = new Date();
                                date.setDate(date.getDate()+day);
                        
                                var willCookie ='';
                                willCookie += name + '=' + encodeURIComponent(value) +';';
                                willCookie +='expires='+date.toUTCString()+'';
                        
                                document.cookie = willCookie;
                                
                                history.back();
                            </script>";

                        } else {
                            echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
                            echo "<script>history.back()</script>";
                            // echo "<br><button type='button' onclick='history.back()'>돌아가기</button>";
                        }
                        // // Check if $uploadOk is set to 0 by an error
                        // if ($uploadOk == 0) {
                        //     // echo "<script>alert('Sorry, your file was not uploaded.');</script>";
                        //     echo "<script>alert('파일이 없로드 되지 않았습니다.');</script>";
                        //     echo "<script>history.back()</script>";
                        // } else {
                        // }
                    }
                }
            }
        } else {
            // echo "<script>alert('File is not an image.');</script>";
            echo "<script>alert('파일이 선택되지 않았거나 이미지 파일이 아닙니다');</script>";
            // echo "<script>history.back()</script>";
            // $uploadOk = 0;
        }
    }

    // 여행 글 작성할 때
    if(isset($_POST["submit_test"])) {
        echo 123;
        $check = getimagesize($file["tmp_name"]);
        if($check !== false) {
            // echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
            // Check if file already exists
            if (file_exists($target_file)) {
                // echo "<script>alert('Sorry, file already exists.');</script>";
                echo "<script>alert('파일이 이미 존재합니다');</script>";
                echo "<script>history.back()</script>";
                $uploadOk = 0;
            }else{
                // Check file size
                if ($file["size"] > 5000000) {
                    // echo "<script>alert('Sorry, your file is too large.');</script>";
                    echo "<script>alert('파일크기가 너무 큽니다');</script>";
                    echo "<script>history.back()</script>";
                    $uploadOk = 0;
                }else{
                    // Allow certain file formats
                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                    && $imageFileType != "gif" ) {
                        // echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
                        echo "<script>alert('jpg, jpeg, png, gif 이미지 파일만 업로드 가능합니다');</script>";
                        echo "<script>history.back()</script>";
                        $uploadOk = 0;
                    }else{
                        $upload_img_dir =  $target_dir . date('Y-m-d H:i:s').'.'.$imageFileType;
                        if (move_uploaded_file($file["tmp_name"], $upload_img_dir)) {
                            // echo "<script>window.location.replace('trip_write.php?upload_img_dir=$upload_img_dir');</script>";
                            echo "<script>
                                var name = 'temp_img_upload';
                                var value = '$upload_img_dir';
                                var day = 1;
                                var date = new Date();
                                date.setDate(date.getDate()+day);
                        
                                var willCookie ='';
                                willCookie += name + '=' + encodeURIComponent(value) +';';
                                willCookie +='expires='+date.toUTCString()+'';
                        
                                document.cookie = willCookie;
                                
                                history.back();
                            </script>";

                        } else {
                            echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
                            echo "<script>history.back()</script>";
                            // echo "<br><button type='button' onclick='history.back()'>돌아가기</button>";
                        }
                        // // Check if $uploadOk is set to 0 by an error
                        // if ($uploadOk == 0) {
                        //     // echo "<script>alert('Sorry, your file was not uploaded.');</script>";
                        //     echo "<script>alert('파일이 없로드 되지 않았습니다.');</script>";
                        //     echo "<script>history.back()</script>";
                        // } else {
                        // }
                    }
                }
            }
        } else {
            // echo "<script>alert('File is not an image.');</script>";
            echo "<script>alert('파일이 선택되지 않았거나 이미지 파일이 아닙니다');</script>";
            // echo "<script>history.back()</script>";
            // $uploadOk = 0;
        }
    }

    
}



?>

<?php
// $file = $_FILES['fileToUpload'];
// $target_dir = "upload/";
// $target_file = $target_dir . basename($file["name"]);

// $uploadOk = 1;
// $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// // Check if image file is a actual image or fake image
// if(isset($_POST["submit"])) {
//     $check = getimagesize($file["tmp_name"]);
//     if($check !== false) {
//         // echo "File is an image - " . $check["mime"] . ".";
//         $uploadOk = 1;
//     } else {
//         // echo "<script>alert('File is not an image.');</script>";
//         echo "<script>alert('파일이 선택되지 않았거나 이미지 파일이 아닙니다');</script>";
//         echo "<script>history.back()</script>";
//         $uploadOk = 0;
//     }
// }
// // Check if file already exists
// if (file_exists($target_file)) {
//     // echo "<script>alert('Sorry, file already exists.');</script>";
//     echo "<script>alert('파일이 이미 존재합니다');</script>";
//         echo "<script>history.back()</script>";
//     $uploadOk = 0;
// }
// // Check file size
// if ($_FILES["fileToUpload"]["size"] > 5000000) {
//     // echo "<script>alert('Sorry, your file is too large.');</script>";
//     echo "<script>alert('파일크기가 너무 큽니다');</script>";
//     echo "<script>history.back()</script>";
//     $uploadOk = 0;
// }
// // Allow certain file formats
// if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
// && $imageFileType != "gif" ) {
//     // echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
//     echo "<script>alert('jpg, jpeg, png, gif 이미지 파일만 업로드 가능합니다');</script>";
//     echo "<script>history.back()</script>";
//     $uploadOk = 0;
// }
// // Check if $uploadOk is set to 0 by an error
// if ($uploadOk == 0) {
//     // echo "<script>alert('Sorry, your file was not uploaded.');</script>";
//     echo "<script>alert('파일이 없로드 되지 않았습니다.');</script>";
//     echo "<script>history.back()</script>";
// } else {
//     $upload_img_dir =  $target_dir . date('Y-m-d H:i:s').'.'.$imageFileType;
//     if (move_uploaded_file($file["tmp_name"], $upload_img_dir)) {
//         echo "<script>window.location.replace('trip_write.php?upload_img_dir=$upload_img_dir');</script>";
//     } else {
//         echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
//         echo "<script>history.back()</script>";
//         // echo "<br><button type='button' onclick='history.back()'>돌아가기</button>";
//     }
// }
?>