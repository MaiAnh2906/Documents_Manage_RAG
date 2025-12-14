<?php

use function PHPSTORM_META\type;

include("../config/config.php");
include("func/function.php");

session_start();
if(!isset($_SESSION['login'])){
    header("Location: login.php");
}
$user_id = $_SESSION['login']['user_id'];

$errorFile = "";
if(isset($_POST['btn_upload'])){
    if(isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] == 0){
        $target = "uploads/". $user_id."/";
        $filename = $_FILES['fileInput']['name'];
        
        if(!file_exists($target)){
            mkdir($target, 0777, true);
        }
        
        $type = pathinfo($filename, PATHINFO_EXTENSION) ;
        $newname = uniqid("file_", true) . "." . $type;
        $path = $target . $newname;
        $size = $_FILES['fileInput']['size'];
        $user_id = $_SESSION['login']['user_id'];
        // limit 
        $limit = 2 * 1024 * 1024 * 1024; 
        $sql = "SELECT SUM(size) AS total_size FROM files WHERE user_id = $user_id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $totalSize = $row['total_size'] ?? 0;
        if ($totalSize >= $limit) {
            echo "<script>alert('Bạn đã vượt quá giới hạn dung lượng lưu trữ! Vui lòng xóa bớt tệp tin hoặc nâng cấp tài khoản.'); window.location.href = 'storage.php';</script>";
            exit();
        }

        $allowed_extensions = [
            'pdf','doc','docx',
            'xls','xlsx',
            'ppt','pptx',
            'txt', 'mp4',
            'jpg','jpeg','png','gif','webp',
            'zip','rar'
        ];

        if (!in_array($type, $allowed_extensions)) {
            $errorFile = "Định dạng file không được phép!";
        }elseif($size >= 10 * 1024 * 1024){
            $errorFile = "File quá lớn, dung lượng tối đa là 10MB!";
        }

        if($errorFile == ""){
            move_uploaded_file($_FILES['fileInput']['tmp_name'], $path);
            $sql =  "INSERT INTO files (`user_id`, `name`, `path`, `type`, `size`) VALUES ($user_id, '$filename', '$path', '$type', $size)";
            if(mysqli_query($conn, $sql)){
                // lưu activity
                logActivity($conn, $user_id, "upload", null, mysqli_insert_id($conn), null, null, "Tải lên tệp " . $filename);
                header('Location: index.php');
            }
        }
                
    }
}

define('ALLOW_ACCESS', true);
$pageTitle = "Upload file";
include "navbar.php";
?>

<div class="main-container">
    <h2 class="text-lg font-bold text-gray-700 uppercase mb-4 tracking-wider">UPLOAD FILES</h2>
    <?php if (!empty($errorFile)) { ?>
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded-lg mb-3">
            <?php echo $errorFile; ?>
        </div>
    <?php } ?>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="fileInput" id="fileInput">
        
        <br><br>
        <input class="btn btn-primary" type="submit" name="btn_upload" value="Upload">
    </form>
</div>
