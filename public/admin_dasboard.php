<?php
include("../config/config.php");
include("auto_clean.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

$user_id = $_SESSION['login']['user_id'];
$username = $_SESSION['login']['username'];
$role = $_SESSION['login']['role'];

if ($role != 1) {
    header("Location: assets/images/403.php");
    exit();
}
// tinh sl user và dự án
$sql = "SELECT
            (SELECT COUNT(*) FROM users) AS total_users,
            (SELECT COUNT(*) FROM folders WHERE is_deleted = 0) AS total_projects";
$kq = mysqli_query($conn, $sql);
if ($kq) {
    $row = mysqli_fetch_assoc($kq);

    $totalUsers = $row['total_users'];
    $totalProjects = $row['total_projects'];

} else {
    echo "Lỗi truy vấn: " . mysqli_error($conn);
}

// sl user hoạt động hôm nay
$sqlActive = "SELECT COUNT(DISTINCT user_id) AS active_users_today
              FROM activity_logs
              WHERE action IN ('login')
              AND DATE(created_at) = CURDATE()";
$resultActive = mysqli_query($conn, $sqlActive);
$activeToday = 0;
if ($resultActive) {
    $rowActive = mysqli_fetch_assoc($resultActive);
    $activeToday = $rowActive['active_users_today'];
}


define('ALLOW_ACCESS', true);
$pageTitle = "Trang chủ";
include "navbar.php";
include "detail_folder.php";
?>
<!-- MAIN CONTENT -->
<div class="main-container">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl p-8 shadow-lg mb-10">
        <h2 class="text-2xl font-bold mb-2">
            👋 Xin chào, <?php echo $username; ?>
        </h2>
        <p class="text-sm opacity-90 mb-6">
            Tổng quan nhanh hệ thống quản lý file hôm nay
        </p>

        <div class="grid grid-cols-3 gap-6">
            <div>
                <div class="text-3xl font-bold"><?php echo $totalUsers ?></div>
                <div class="text-sm opacity-80">Người dùng</div>
            </div>
            <div>
                <div class="text-3xl font-bold"><?php echo $totalProjects ?></div>
                <div class="text-sm opacity-80">Dự án</div>
            </div>
            <div>
                <div class="text-3xl font-bold"><?php echo $activeToday ?></div>
                <div class="text-sm opacity-80">Hoạt động hôm nay</div>
            </div>
        </div>
    </div>

    <h3 class="text-lg font-semibold text-gray-700 mb-4">
        Truy cập nhanh
    </h3>

    <div class="grid grid-cols-3 gap-6 mb-10">

        <a href="user_manage.php" class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition group">
            <div class="text-4xl mb-3 group-hover:scale-110 transition">👤</div>
            <h4 class="font-semibold text-lg">Người dùng</h4>
            <p class="text-sm text-gray-500">Quản lý tài khoản</p>
        </a>

        <a href="admin_statistic.php" class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition group">
            <div class="text-4xl mb-3 group-hover:scale-110 transition">📁</div>
            <h4 class="font-semibold text-lg">Thống kê</h4>
            <p class="text-sm text-gray-500">Thống kê theo người dùng</p>
        </a>

        <a href="admin_activity_log.php" class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition group">
            <div class="text-4xl mb-3 group-hover:scale-110 transition">🕒</div>
            <h4 class="font-semibold text-lg">Hoạt động</h4>
            <p class="text-sm text-gray-500">Lịch sử hệ thống</p>
        </a>

    </div>

</div>
