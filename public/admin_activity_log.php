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
<div class="main-container container-fluid px-4 py-4 bg-slate-100 min-vh-100">

    <!-- HEADER -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="fw-semibold text-slate-800 mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-primary fs-4"></i>
            Lịch sử hoạt động
        </h3>
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
            50 bản ghi gần nhất
        </span>
    </div>

    <!-- TABLE CARD -->
    <div class="table-responsive bg-white rounded-4 shadow-sm p-3">

        <table class="table table-hover align-middle mb-0">

            <!-- TABLE HEADER -->
            <thead class="table-light">
                <tr class="text-uppercase small text-slate-600">
                    <th class="py-3">Người dùng</th>
                    <th class="py-3">Hành động</th>
                    <th class="py-3">Chi tiết</th>
                    <th class="py-3 text-nowrap">Thời gian</th>
                </tr>
            </thead>

            <!-- TABLE BODY -->
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($res)) : ?>
                    <tr>

                        <!-- USER -->
                        <td>
                            <div class="fw-semibold text-slate-800">
                                <?= htmlspecialchars($row['username'] ?? '') ?>
                            </div>
                            <div class="text-slate-500 text-sm">
                                <?= htmlspecialchars($row['email'] ?? '') ?>
                            </div>
                        </td>

                        <!-- ACTION -->
                        <td>
                            <?= actionLabel($row['action']) ?>
                        </td>

                        <!-- DESCRIPTION -->
                        <td class="text-slate-700 small">
                            <?= nl2br(htmlspecialchars($row['description'] ?? '')) ?>
                        </td>

                        <!-- TIME -->
                        <td class="text-nowrap text-slate-500 small">
                            <?= date('d/m/Y H:i:s', strtotime($row['created_at'])) ?>
                        </td>

                    </tr>
                <?php endwhile; ?>

                <?php if (mysqli_num_rows($res) === 0): ?>
                    <tr>
                        <td colspan="4" class="text-center text-slate-400 py-5">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Không có bản ghi hoạt động nào
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>
    </div>
</div>
