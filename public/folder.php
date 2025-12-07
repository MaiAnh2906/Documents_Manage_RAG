<?php 
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

    <!-- Tên dự án -->
    <h2 class="text-xl font-bold mb-3"><?php echo htmlspecialchars($folder['name']); ?></h2>
    <hr>

    <p class="text-sm tracking-wide mt-3 mb-2 opacity-90 font-semibold">
        Thao tác
    </p>
    <!-- Ngừng dự án -->
    <?php if ($isOwner) { ?>
    <button class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg mb-3">
        Ngừng dự án
    </button>
    <?php } ?>

    <!-- Quay lại -->
    <button class="sidebar-btn mb-5">
        ← Quay lại danh sách dự án
    </button>

    <!-- Thành viên -->
    <h3 class="text-lg font-semibold mb-2">Danh sách thành viên</h3>

    <ul class="space-y-2 mb-4">
        <li class="member-item p-2 rounded">👑 Owner — Nguyễn Văn A</li>
        <li class="member-item p-2 rounded">👤 viewer — user01</li>
        <li class="member-item p-2 rounded">👤 editor — user02</li>
    </ul>

    <?php if ($isOwner) { ?>
    <a href="share/share.php" class="underline text-sm">Quản lý thành viên</a>
<?php } ?>

</aside>

<!-- MAIN CONTENT -->
<main class="ml-[260px] p-8">

    <!-- TOP BAR -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-700">Bảng làm việc dự án</h1>

        <button class="bg-[#387af5] hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow">
            + Thêm nội dung
        </button>
    </div>

    <!-- CONTENT SECTION -->
    <section class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-lg font-bold text-gray-700 mb-4">Nội dung dự án</h2>

        <!-- Bài 1 -->
        <div class="border p-4 rounded-lg mb-4 hover:bg-gray-50 transition">
            <div class="flex justify-between">
                <div>
                    <h3 class="font-semibold text-lg">Tiêu đề bài viết 1</h3>
                    <p class="text-sm text-gray-500">
                        user02 · 2025-01-05
                    </p>
                </div>

                <div class="space-x-3">
                    <button class="text-blue-600">Sửa</button>
                    <button class="text-red-600">Xóa</button>
                </div>
            </div>

            <p class="mt-3 text-gray-700">
                Nội dung bài viết hiển thị ở đây...
            </p>
        </div>

        <!-- Bài 2 -->
        <div class="border p-4 rounded-lg hover:bg-gray-50 transition">
            <div class="flex justify-between">
                <div>
                    <h3 class="font-semibold text-lg">Tiêu đề bài viết 2</h3>
                    <p class="text-sm text-gray-500">
                        user01 · 2025-01-04
                    </p>
                </div>

                <div class="space-x-3">
                    <button class="text-blue-600">Sửa</button>
                    <button class="text-red-600">Xóa</button>
                </div>
            </div>

            <p class="mt-3 text-gray-700">
                Nội dung bài viết hiển thị ở đây...
            </p>
        </div>

    </section>
</main>

</body>
</html>
