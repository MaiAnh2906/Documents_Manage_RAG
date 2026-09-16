<?php
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
            'pdf','docx', 'txt'
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
                $document_id = mysqli_insert_id($conn);
                // Gọi REST API
                $data = [
                    "document_id" => $document_id,
                    "path" => realpath($path),
                    "user_id" => $_SESSION['login']['user_id']
                ];
                $ch = curl_init("http://127.0.0.1:8000/ingest");

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Content-Type: application/json"
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    die("cURL Error: " . curl_error($ch));
                }

                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                curl_close($ch);

                header('Location: index.php');
                exit;
                
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
