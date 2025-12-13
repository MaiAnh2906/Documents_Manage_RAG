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

define('ALLOW_ACCESS', true);
$pageTitle = "Trang chủ";
include "navbar.php";
include "detail_folder.php";
?>
<!-- MAIN CONTENT -->
<div class="main-container">
    <h2 class="text-xl font-bold text-gray-700 uppercase mt-10 mb-4 tracking-wider border-b pb-2">QUẢN TRỊ HỆ THỐNG</h2>

    <div class="grid grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg p-6 shadow-md">
            <div class="text-sm text-gray-500">TỔNG NGƯỜI DÙNG</div>
            <div class="text-3xl font-bold text-blue-700">
                <?php echo $totalUsers; ?>
            </div>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-md">
            <div class="text-sm text-gray-500">TỔNG DỰ ÁN</div>
            <div class="text-3xl font-bold text-blue-700">
                <?php echo $totalProjects; ?>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-4 gap-6">

        <!-- Quản lý tài khoản -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold mb-2">Quản lý tài khoản</h3>
            <p class="text-sm text-gray-600 mb-4">Xem, thêm, sửa, xóa tài khoản người dùng trong hệ thống</p>
            <a href="user_manage.php" class="btn btn-primary" style="background-color: #2563eb; border: none;">Quản lý người dùng</a>
        </div>

        <!-- Quản lý phân quyền -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold mb-2">Quản lý phân quyền</h3>
            <p class="text-sm text-gray-600 mb-4">Phân quyền hệ thống cho người dùng (Admin/User)</p>
            <a href="role_manage.php" class="btn btn-success" style="background-color: #22c55e; border: none;">Phân quyền</a>
        </div>

        <!-- Lịch sử hoạt động -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold mb-2">Lịch sử hoạt động</h3>
            <p class="text-sm text-gray-600 mb-4">Tra cứu lịch sử đăng nhập và hoạt động của người dùng</p>
            <a href="admin_activity_log.php" class="btn btn-warning" style="background-color: #fbbf24; border: none;">Xem lịch sử</a>
        </div>

        <!-- Bảo trì hệ thống -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold mb-2">Bảo trì hệ thống</h3>
            <p class="text-sm text-gray-600 mb-4">Sao lưu dữ liệu, khôi phục database, bảo trì hệ thống</p>
            <a href="maintenance.php" class="btn btn-danger" style="background-color: #ef4444; border: none;">Bảo trì</a>
        </div>

    </div>

</div>
