<?php
session_start();
include("../config/config.php");
include("func/function.php");

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['login']['user_id'];
$role = $_SESSION['login']['role'];
if ($role != 1) {
    header("Location: assets/images/403.php");
    exit();
}

$sql = "
    SELECT al.*, u.username, u.email
    FROM activity_logs al
    LEFT JOIN users u ON al.user_id = u.user_id
    ORDER BY al.created_at DESC
    LIMIT 50
";

$res = mysqli_query($conn, $sql);

if (!$res) {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}

define('ALLOW_ACCESS', true);
$pageTitle = "Chỉnh sửa dự án";
include "navbar.php";
?>
<!-- MAIN CONTENT -->
<div class="main-container">
    <h3 class="mb-4">Lịch sử hoạt động (50 bản ghi gần nhất)</h3>

    <div class="table-responsive shadow rounded">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Người dùng</th>
                    <th>Hành động</th>
                    <th>Chi tiết</th>
                    <th>Thời gian</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($res)) : ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($row['username'] ?? '') ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($row['email'] ?? '') ?></small>
                        </td>
                        <td><?= actionLabel($row['action']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['description'] ?? '')) ?></td>
                        <td class="text-nowrap"><?= date('d/m/Y H:i:s', strtotime($row['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($res) === 0): ?>
                    <tr><td colspan="4" class="text-center">Không có bản ghi hoạt động nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
