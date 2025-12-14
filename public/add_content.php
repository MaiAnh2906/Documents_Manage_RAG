<?php

use Pdo\Mysql;

include("../config/config.php");
include("auto_clean.php");
include("func/function.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

if (!isset($_GET['folder_id'])) {
    die("Không có thư mục được chọn.");
}

$folder_id = intval($_GET['folder_id']);
$user_id = $_SESSION['login']['user_id'];

// Lấy thông tin folder
$sql = "SELECT folders.*, users.username AS owner_name, users.user_id AS owner_id
        FROM folders
        JOIN users ON folders.user_id = users.user_id
        WHERE folder_id = $folder_id";

$res = mysqli_query($conn, $sql);
$folder = mysqli_fetch_assoc($res);

if (!$folder) {
    die("Thư mục không tồn tại.");
}

$isOwner = ($user_id == $folder['owner_id']); 

$participants = [];
$sql = "SELECT users.username, shares.permission FROM shares 
        JOIN users ON shares.target_user_id = users.user_id
        WHERE shares.folder_id = $folder_id";
$kq = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($kq)){
    $participants[] = $row;
}

$sql = "SELECT permission FROM shares 
        WHERE folder_id = $folder_id
        AND target_user_id = $user_id";
$kq = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($kq);

$permission = $row['permission'] ?? null;

$canAdd = $isOwner || ($permission == 'contributor') || ($permission == 'operator');


$status = "";
if($isOwner){
    $status = "approved";
}else{
    $status = "pending";
}

$error = "";
if(isset($_POST['btn_luu'])){
    $title = $_POST['tieu_de'];
    $content_text = $_POST['noi_dung'];

    if($title == "" || $content_text == ""){
        $error = "Vui lòng nhập đầy đủ thông tin!";
    }
    elseif($_FILES['file_upload']['error'] == 4){
        $sql = "INSERT INTO contents (folder_id, user_id, title, content_text, status, created_at) VALUES 
        ($folder_id, $user_id, '$title', '$content_text', '$status', NOW())";
        mysqli_query($conn, $sql);
        header("Location: folder.php?folder_id=$folder_id");
    }elseif(isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0){
        $target = "uploads/$user_id/";
        $filename = $_FILES['file_upload']['name'];
        
        if(!file_exists($target)){
            mkdir($target, 0777, true);
        }
        
        $type = pathinfo($filename, PATHINFO_EXTENSION) ;
        $newname = uniqid("file_", true) . "." . $type;
        $path = $target . $newname;
        $size = $_FILES['file_upload']['size'];

        $allowed_extensions = [
            'pdf','doc','docx',
            'xls','xlsx',
            'ppt','pptx',
            'txt',
            'jpg','jpeg','png','gif','webp',
            'zip','rar'
        ];

        if (!in_array($type, $allowed_extensions)) {
            $error = "Định dạng file không được phép!";
        }elseif($size >= 10 * 1024 * 1024){
            $error = "File quá lớn, dung lượng tối đa là 10MB!";
        }

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

        if($error == ""){
            move_uploaded_file($_FILES['file_upload']['tmp_name'], $path);
            $sql = "INSERT INTO contents (folder_id, user_id, title, content_text, status, created_at) VALUES 
            ($folder_id, $user_id, '$title', '$content_text', '$status', NOW())";
            mysqli_query($conn, $sql);

            $content_id = mysqli_insert_id($conn);
            $sql =  "INSERT INTO files (`user_id`, `folder_id`, `content_id`, `name`, `path`, `type`, `size`) VALUES ($user_id, $folder_id, $content_id, '$filename', '$path', '$type', $size)";
            mysqli_query($conn, $sql);
            // lưu activity
            logActivity($conn, $user_id, "add_content", $folder_id, null, $content_id, null, "Thêm nội dung mới: $title");


            header("Location: folder.php?folder_id=$folder_id");
        }
        
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Bảng làm việc dự án</title>

    <style>
        body {
            background: #f4f7f9;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #387af5;
            color: #fff;
            border-radius: 0 60px 40px 0;
            padding: 30px 20px;
            position: fixed;
            left: 0;
            top: 0;
        }

        .sidebar-btn {
            background: #fff;
            color: #1e73e8;
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: 500;
            width: 100%;
            border: none;
        }

        .member-item:hover {
            background: rgba(255,255,255,0.2);
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<aside class="sidebar">

    <h2 class="text-xl font-bold mb-3"><?php echo htmlspecialchars($folder['name']); ?></h2>
    <hr>

    <p class="text-sm tracking-wide mt-3 mb-2 opacity-90 font-semibold">
        Thao tác
    </p>

    <?php if ($isOwner) { ?>
    <button class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg mb-3">
        Ngừng dự án
    </button>
    <?php } ?>

    <a 
    href="index.php"
    class="inline-flex items-center gap-2 px-4 py-2 mb-5
           bg-white border border-gray-300 text-gray-700 
           rounded-lg shadow-sm hover:bg-gray-100 
           hover:border-gray-400 transition-all duration-200">
    ← Quay lại trang chủ
    </a>



    <!-- Thành viên -->
    <h3 class="text-lg font-semibold mb-2">Danh sách thành viên</h3>

    <ul class="space-y-2 mb-4">
        <li class="member-item p-2 rounded">👑 Owner — <?php echo $folder['owner_name']; ?></li>
        <?php
            foreach ($participants as $participant) {
                echo '<li class="member-item p-2 rounded">👤 ' . $participant['permission']. ' — ' . $participant['username'] . ' </li>';
            }
        ?>
        
    </ul>

    <?php if ($isOwner) { ?>
    <a href="share/share.php" class="underline text-sm">Quản lý thành viên</a>
<?php } ?>

</aside>

<!-- MAIN CONTENT -->
<main class="ml-[260px] p-8">

<section class="bg-white p-6 rounded-xl shadow-lg">
    <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-3">
         Thêm nội dung mới
    </h2>
    <?php if (!empty($error)) { ?>
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded-lg mb-3">
            <?php echo $error; ?>
        </div>
    <?php } ?>

    <form action="" method="POST" enctype="multipart/form-data" class="space-y-5">

        <!-- Tiêu đề -->
        <div>
            <label class="block text-gray-700 font-medium mb-1">Tiêu đề</label>
            <input 
                type="text" 
                name="tieu_de"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg 
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                       outline-none transition"
                placeholder="Nhập tiêu đề bài viết..."
            >
        </div>

        <!-- Nội dung -->
        <div>
            <label class="block text-gray-700 font-medium mb-1">Nội dung</label>
            <textarea 
                name="noi_dung"
                rows="6"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg 
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                       outline-none transition resize-none"
                placeholder="Nhập nội dung bài viết..."
            ></textarea>
        </div>

        <!-- Upload file -->
        <div>
            <label class="block text-gray-700 font-medium mb-2">Tệp đính kèm</label>

            <label 
                for="fileUpload"
                class="flex items-center gap-3 cursor-pointer bg-gray-50 border border-gray-300 
                       rounded-lg px-4 py-3 hover:bg-gray-100 transition">
                <i class="bi bi-upload text-blue-600 text-lg"></i>
                <span id="fileLabel" class="text-gray-600">Chọn tệp để tải lên...</span>
            </label>

            <input type="file" name="file_upload" id="fileUpload" class="hidden"
                onchange="document.getElementById('fileLabel').innerText = this.files[0]?.name ?? 'Chọn tệp để tải lên...'"
            >
        </div>

        <!-- Button lưu -->
        <div class="flex justify-end">
            <button 
                class="bg-blue-600 hover:bg-blue-700 text-white 
                       px-6 py-2 rounded-lg shadow-md transition" name="btn_luu">
                Lưu nội dung
            </button>
        </div>

    </form>
</section>

</main>

</body>
</html>
