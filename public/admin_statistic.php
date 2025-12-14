<?php
include("../config/config.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['login']['user_id'];
$username = $_SESSION['login']['username'];
$role = $_SESSION['login']['role'];

if ($role != 1) {
    header("Location: assets/images/403.php");
    exit();
}
$sqlStats = "SELECT 
    u.user_id,
    u.username,
    COUNT(DISTINCT fo.folder_id) AS total_projects,
    GROUP_CONCAT(DISTINCT fo.name SEPARATOR ', ') AS project_names,
    COALESCE(SUM(f.size), 0) AS total_used
    FROM users u
    LEFT JOIN folders fo 
        ON fo.user_id = u.user_id AND fo.is_deleted = 0
    LEFT JOIN files f 
        ON f.user_id = u.user_id
    GROUP BY u.user_id, u.username
    ORDER BY u.username
";
$resultStats = mysqli_query($conn, $sqlStats);
// // tổng dung lượng đã dùng theo user
// $sql = "select u.username, u.user_id, sum(f.size) as total_used
// from users u join files f on u.user_id = f.user_id
// group by u.user_id, u.username";
// $result = mysqli_query($conn, $sql);
// $row = mysqli_fetch_assoc($result);
// $totalS = $row['total_used'];

define('ALLOW_ACCESS', true);
$pageTitle = "Thống kê người dùng";
include "navbar.php";
?>

<div class="main-container p-6">

    <h2 class="text-2xl font-bold mb-6">📊 Thống kê dự án</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php 
        if ($resultStats) {
            while ($row = mysqli_fetch_assoc($resultStats)) {
                $projects = $row['project_names'] ? explode(',', $row['project_names']) : []; 
                $user_name = $row['username'];
                $totalPro = $row['total_projects'];
                $totalUsed = $row['total_used'] / 1024 / 1024;
                ?>

                <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition">
                    <h3 class="text-lg font-semibold mb-2">
                        👤 <?php echo $user_name ?>
                    </h3>
                    <p class="text-sm text-gray-500 mb-3">
                        Tổng dự án: <span class="font-bold"><?php echo $totalPro ?></span>
                    </p>
                    <p class="text-sm text-gray-500 mb-3">
                        Dung lượng đã dùng: <span class="font-bold"><?php echo number_format($totalUsed, 2)?> MB</span>
                    </p>
                    <?php if(!empty($projects)) { ?>
                        <ul class="list-disc list-inside text-gray-700 text-sm">
                            <?php foreach($projects as $p) { ?>
                                <li><?php echo $p ?></li>
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <p class="text-gray-400 text-sm italic">Chưa có dự án</p>
                    <?php } ?>
                </div>
            <?php
                }
            } else {
                echo "<p class='text-red-500'>Lỗi truy vấn: " . mysqli_error($conn) . "</p>";
            } ?>
    </div>
</div>
