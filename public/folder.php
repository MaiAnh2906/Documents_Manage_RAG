<?php

use Pdo\Mysql;

include("../config/config.php");
include("auto_clean.php");
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

$canAdd = ($isOwner || ($permission == 'contributor') || ($permission == 'operator')) && ($folder['status'] == 1);


if(isset($_GET['delete_content_id'])){
    $delete_ct = $_GET['delete_content_id'];

    $sql = "SELECT * FROM contents WHERE content_id = $delete_ct";
    $kq = mysqli_query($conn, $sql);
    $r = mysqli_fetch_assoc($kq);
    $path = $r['file_path'];
    if(file_exists($path)){
        unlink($path);
    }

    $sql = "DELETE FROM contents WHERE content_id = $delete_ct";
    mysqli_query($conn, $sql);
    header("Location: folder.php?folder_id=$folder_id");
    exit();
}

if(isset($_POST['btn_stop'])){

    $sql = "UPDATE folders SET status = 0 WHERE folder_id = $folder_id";
    mysqli_query($conn, $sql);

    $sql = "UPDATE shares SET permission = 'viewer' WHERE folder_id = $folder_id";
    mysqli_query($conn, $sql);
    
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

    <div class="flex items-center gap-2">
        <h2 class="text-xl font-bold"><?php echo htmlspecialchars($folder['name']); ?></h2>
        <?php if ($folder['status'] == 1) { ?>
            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs flex items-center">
                Hoạt động
            </span>
        <?php } else { ?>
            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs flex items-center">
                Ngừng hoạt động
            </span>
        <?php } ?>
    </div>
    <hr>

    <p class="text-sm tracking-wide mt-3 mb-2 opacity-90 font-semibold">
        Thao tác
    </p>

    <?php if ($isOwner) { ?>
    <form method="post">
    <input type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg mb-3" name="btn_stop" onclick="return confirm('Bạn có chắc muốn ngừng dự án không?');" value="Ngừng dự án">
    </form>
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
    <a href="member_management.php?folder_id=<?= $folder_id ?>" class="underline text-sm">Quản lý thành viên</a>
    <?php } ?>
    <a class="dropdown-item" href="#" onclick="openFolderDetail(<?= $folder_id ?>)">
    <i class="bi bi-eye"></i> Chi tiết dự án
    </a>


</aside>

<!-- MAIN CONTENT -->
<div class="ml-[260px] p-8">

    <!-- TOP BAR -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-700">Bảng làm việc dự án</h1>

        <?php if($canAdd){ ?> 
        <a href="add_content.php?folder_id=<?php echo $folder_id; ?>&user_id=<?php echo $user_id; ?>"
        class="bg-[#387af5] hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow inline-block">
            + Thêm nội dung
        </a>
        <?php } ?>
    </div>

    <section class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-lg font-bold text-gray-700 mb-4">Nội dung dự án</h2>

        <?php
            $sql = "SELECT contents.*, contents.status AS content_status, users.username  FROM contents 
                    JOIN users ON contents.user_id = users.user_id
                    WHERE contents.folder_id = $folder_id
                    ORDER BY contents.content_id DESC";
            $kq = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_assoc($kq)){
        ?>
            <div class="border p-4 rounded-lg mb-4 hover:bg-gray-50 transition">
                <div class="flex justify-between">
                    <div>
                        <h3 class="font-semibold text-lg"><?php echo $row['title']; ?></h3>
                        <p class="text-sm text-gray-500">
                            <?php echo $row['username'] . " . " . $row['created_at'] ; ?>
                        </p>
                        <p class="text-sm mt-1">
                            <?php if ($row['status'] == "approved"){ ?>
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">
                                    Đã duyệt
                                </span>
                            <?php } elseif ($row['status'] == "pending"){ ?>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Chưa duyệt</span>

                            <?php } elseif ($row['status'] == "rejected"){ ?>
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Từ chối</span>
                            <?php } ?>
                        </p>
                        
                    </div>

                    <div class="space-x-3">
                        <?php if($isOwner && (!$row['status'] == "approved")){ ?> 
                        <a href="?content_id=<?php echo $row['content_id']; ?>&status=approved&folder_id=<?php echo $folder_id; ?>" 
                        class="text-green-600 font-semibold">
                            Duyệt
                        </a>

                        <a href="?content_id=<?php echo $row['content_id']; ?>&status=rejected&folder_id=<?php echo $folder_id; ?>" 
                        class="text-yellow-600 font-semibold">
                            Từ chối
                        </a>
                        <?php } ?>

                        <a href="edit_content.php?folder_id=<?php echo $folder_id; ?>&content_id=<?php echo $row['content_id']; ?>" class="text-blue-600">Sửa</a>

                        <a href="?folder_id=<?php echo $folder_id; ?>&delete_content_id=<?php echo $row['content_id']; ?>" class="text-red-600" onclick="return confirm('Bạn có chắc muốn xóa không?');">Xóa</a>
                        
                    </div>
                </div>

                <p class="mt-3 text-gray-700">
                    <?php echo $row['content_text']; ?>
                </p>

                <?php if (!empty($row['file_path'])){ ?>
                    <div class="mt-4 p-3 border border-gray-300 rounded-lg bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-file-earmark-text text-blue-600 text-xl"></i>

                            <div>
                                <p class="font-medium text-gray-700"><?php echo $row['file_name']; ?></p>
                                <p class="text-xs text-gray-500">
                                    <?php echo strtoupper($row['file_type']); ?> —
                                    <?php echo round($row['file_size'] / 1024, 1); ?> KB
                                </p>
                            </div>
                        </div>

                        <a href="<?php echo $row['file_path']; ?>" 
                        download
                        class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">
                            Tải xuống
                        </a>
                    </div>
                <?php } ?>
            </div>
        <?php
            }
        ?>

    </section>
</div>
<?php include "detail_folder.php"; ?>

</body>
</html>
