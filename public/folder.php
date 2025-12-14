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
        WHERE folders.folder_id = $folder_id";

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

$canEdit = ($isOwner || ($permission == 'operator')) && ($folder['status'] == 1);

$canCmt = ($isOwner || ($permission == 'contributor') || ($permission == 'operator')) && ($folder['status'] == 1);

$qlytv = ($isOwner || ($permission == 'operator'));


if(isset($_GET['delete_content_id'])){
    $delete_ct = $_GET['delete_content_id'];

    $sql = "SELECT * FROM files 
        WHERE content_id = $delete_ct AND folder_id = $folder_id";
    $kq = mysqli_query($conn, $sql);
    $row_file = mysqli_fetch_assoc($kq);

    if(file_exists($row_file['path'])){
        unlink($row_file['path']);
    }

    $sql = "DELETE FROM contents WHERE content_id = $delete_ct";
    mysqli_query($conn, $sql);

    $sql_delete = "DELETE FROM files WHERE content_id = $delete_ct AND folder_id = $folder_id";
    mysqli_query($conn, $sql_delete);

    header("Location: folder.php?folder_id=$folder_id");
    exit();
}

if(isset($_POST['btn_stop'])){

    $sql = "UPDATE folders SET status = 0 WHERE folder_id = $folder_id";
    mysqli_query($conn, $sql);
    
    header("Location: folder.php?folder_id=$folder_id");
}

if(isset($_POST['btn_start'])){
    $sql = "UPDATE folders SET status = 1 WHERE folder_id = $folder_id";
    mysqli_query($conn, $sql);

    header("Location: folder.php?folder_id=$folder_id");
}

if(isset($_GET['approved_id'])){
    $content_id = $_GET['approved_id'];
    $sql = "UPDATE contents SET status = 'approved' WHERE  content_id = $content_id";
    mysqli_query($conn, $sql);
}

if(isset($_GET['rejected_id'])){
    $content_id = $_GET['rejected_id'];
    $sql = "UPDATE contents SET status = 'rejected' WHERE  content_id = $content_id";
    mysqli_query($conn, $sql);
}

if(isset($_POST['btn_comment'])){
    $content_id = $_POST['content_id'];
    $comment_text = $_POST['comment_text'];

    $sql = "INSERT INTO comments (content_id, folder_id, user_id, comment_text) VALUES ($content_id, $folder_id, $user_id, '$comment_text')";
    mysqli_query($conn, $sql);
}

if(isset($_GET['comment_id'])){
    $comment_id = $_GET['comment_id'];
    $sql = "DELETE FROM comments WHERE comment_id = $comment_id";
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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
        <?php if ($folder['status'] == 1) { ?>
                <input type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg mb-3" name="btn_stop" onclick="return confirm('Bạn có chắc muốn ngừng dự án không?');" value="Ngừng dự án">
                
        <?php }else{ ?>
                <input type="submit" name="btn_start" value="Khởi động dự án"
                    class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg mb-3 
                    font-semibold shadow-sm hover:shadow transition" />
        <?php } ?>
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

    <?php if ($isOwner && $folder['status'] == 1) { ?>
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
        if($isOwner){
            $sql = "SELECT contents.*, contents.status AS content_status, users.username  FROM contents 
                JOIN users ON contents.user_id = users.user_id
                WHERE contents.folder_id = $folder_id
                ORDER BY contents.content_id DESC";
        }else{
            $sql = "SELECT contents.*, contents.status AS content_status, users.username  FROM contents 
                JOIN users ON contents.user_id = users.user_id
                WHERE contents.folder_id = $folder_id
                AND (
                    contents.user_id = $user_id
                    OR contents.status = 'approved'
                )
                ORDER BY contents.content_id DESC";
        }
            
            $kq = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_assoc($kq)){
                $content_id = $row['content_id'];
                $sql_file = "SELECT * FROM files 
                        WHERE content_id = $content_id AND folder_id = $folder_id";
                $kq_file = mysqli_query($conn, $sql_file);
                $row_file = mysqli_fetch_assoc($kq_file);
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
                        <?php if($folder['status'] == 1){ ?> 
                            <?php if($isOwner && $row['status'] == "pending"){ ?> 
                                <a href="?approved_id=<?php echo $row['content_id']; ?>&status=approved&folder_id=<?php echo $folder_id; ?>" 
                                class="text-green-600 font-semibold">
                                    Duyệt
                                </a>

                                <a href="?rejected_id=<?php echo $row['content_id']; ?>&status=rejected&folder_id=<?php echo $folder_id; ?>" 
                                class="text-yellow-600 font-semibold">
                                    Từ chối
                                </a>
                            <?php } ?>

                                <?php if($canEdit) { ?>
                                <a href="edit_content.php?folder_id=<?php echo $folder_id; ?>&content_id=<?php echo $row['content_id']; ?>" class="text-blue-600">Sửa</a>
                                <a href="?folder_id=<?php echo $folder_id; ?>&delete_content_id=<?php echo $row['content_id']; ?>" class="text-red-600" onclick="return confirm('Bạn có chắc muốn xóa không?');">Xóa</a>
                                <?php } ?>


                        <?php } ?>
                        
                    </div>
                </div>

                <p class="mt-3 text-gray-700">
                    <?php echo $row['content_text']; ?>
                </p>

                <?php if (!empty($row_file['path'])){ ?>
                    <div class="mt-4 p-3 border border-gray-300 rounded-lg bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div>
                                <p class="font-medium text-gray-700"><?php echo $row_file['name']; ?></p>
                                <p class="text-xs text-gray-500">
                                    <?php echo strtoupper($row_file['type']); ?> —
                                    <?php echo round($row_file['size'] / 1024, 1); ?> KB
                                </p>
                            </div>
                        </div>


                        <div class="col-span-1 flex space-x-2 justify-end text-gray-400 px-3">
                            <div class="dropdown">
                                <i class="bi bi-three-dots-vertical cursor-pointer hover:text-blue-500 text-lg"
                                    data-bs-toggle="dropdown" aria-expanded="false"></i>
                                <ul class="dropdown-menu shadow-lg rounded-xl">
                                    <li>
                                        <a class="dropdown-item flex items-center gap-2" href="detail_file.php?folder_id=<?php echo $folder_id; ?>&id=<?php echo $row_file['file_id']; ?>">
                                            <i class="bi bi-eye"></i> Chi tiết
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item flex items-center gap-2" href="download_file.php?id=<?php echo $row_file['file_id']; ?>">
                                            <i class="bi bi-download"></i> Tải xuống
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php } ?>


                <div class="mt-6 border-t pt-4">
                    <h4 class="btnToggleComment font-semibold text-gray-700 mb-3 text-lg" style="cursor: pointer;">Bình luận</h4>

                    <div id="commentSection" class="comment-section" style="display: none;">
                    <?php if($folder['status'] == 1){ ?> 
                    <form method="POST" class="mb-4">
                        <input type="hidden" name="content_id" value="<?php echo $row['content_id']; ?>">
                        
                        <textarea 
                            name="comment_text" rows="2" 
                            class="w-full border rounded-lg p-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none"
                            placeholder="Viết bình luận của bạn..."
                            required
                        ></textarea>

                        <br>
                        <input type="submit" name="btn_comment"
                            class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm" value="Gửi bình luận"
                        > 
                    </form>
                    <?php } ?>

                    <div class="space-y-4">
                    <?php 
                        $content_id = $row['content_id'];
                        $sql_comment = "SELECT comments.*, users.username
                                FROM comments
                                JOIN users ON comments.user_id = users.user_id
                                WHERE comments.content_id = $content_id AND comments.folder_id = $folder_id";
                        $kq_comment = mysqli_query($conn, $sql_comment);
                        while ($row_comment = mysqli_fetch_assoc($kq_comment)){
                            $username = $row_comment['username'];    
                        ?>
                        <div class="flex gap-3 items-start p-3 rounded-lg bg-gray-50 border border-gray-200 hover:bg-gray-100 transition">
                            <img class="rounded-circle border me-2" src="https://placehold.co/24x24/dc2626/ffffff?text=<?php echo mb_substr($username, 0, 1, "UTF-8"); ?>" class="w-10 h-10 rounded-full shadow">

                            <div class="flex-1">
                                    <p class="font-semibold text-sm text-gray-800">
                                        <?php echo $row_comment['username']; ?>
                                    </p>
                                    <p class="text-gray-700 text-sm mt-1">
                                        <?php echo $row_comment['comment_text']; ?>
                                    </p>
                            </div>
                            <div class="flex items-center gap-3 mt-1 ml-1">
                                <span class="text-xs text-gray-400">
                                    <?php echo $row_comment['created_at']; ?>
                                </span>

                                <?php if ($row_comment['user_id'] == $user_id || $isOwner) { ?>
                                <a href="?comment_id=<?php echo $row_comment['comment_id']; ?>&folder_id=<?php echo $folder_id; ?>"
                                    class="text-xs text-red-500 hover:underline"
                                    onclick="return confirm('Xóa bình luận này?')">
                                    Xóa
                                </a>
                                <?php } ?>
                            </div>

                        </div>
                        <?php
                        }
                    ?>
                    </div>
                    </div>
                </div>

            </div>
        <?php
            }
        ?>

    </section>
</div>
<?php include "detail_folder.php"; ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".btnToggleComment").forEach(function (btn) {
        btn.addEventListener("click", function () {
            const commentSection = this.nextElementSibling;

            if (commentSection.style.display === "none") {
                commentSection.style.display = "block";
                this.textContent = "Ẩn bình luận";
            } else {
                commentSection.style.display = "none";
                this.textContent = "Bình luận";
            }
        });
    });
});
</script>


</body>
</html>
