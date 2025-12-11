<?php
include "../config/config.php";
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

define('ALLOW_ACCESS', true);
$pageTitle = "Trang chủ";
include "navbar.php";

$user_id = $_SESSION['login']['user_id'];
$username = $_SESSION['login']['username'];
$role = $_SESSION['login']['role']; 

$maxStorage = ($role === 1)
    ? 200 * 1024 * 1024 * 1024
    : 2 * 1024 * 1024 * 1024;


$files = [];
$q1 = mysqli_query($conn, "SELECT * FROM files WHERE user_id = $user_id");
while ($row = mysqli_fetch_assoc($q1)) {
    $files[] = $row;
}
$contents = [];
$q2 = mysqli_query($conn, "SELECT * FROM contents WHERE user_id = $user_id");
while ($row = mysqli_fetch_assoc($q2)) {
    if (!empty($row['file_name']) && !empty($row['file_size'])) {
        $row['size'] = (int)$row['file_size'];
        $contents[] = $row;
    }
}

$totalFilesSize = 0;
foreach ($files as $f) $totalFilesSize += $f['size'];

$totalContentSize = 0;
foreach ($contents as $c) $totalContentSize += $c['size'];

$totalUsed = $totalFilesSize + $totalContentSize;
$percent = ($totalUsed / $maxStorage) * 100;


$categories = [
    "document" => 0,
    "image" => 0,
    "video" => 0,
    "other" => 0
];

$extDocument = ['pdf', 'doc', 'docx', 'txt'];
$extImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$extVideo = ['mp4', 'avi', 'mov', 'mkv'];

foreach ($files as $f) {
    $filename = $f['name'] ?? '';
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, $extDocument)) {
        $categories['document'] += $f['size'];
    } 
    else if (in_array($ext, $extImage)) {
        $categories['image'] += $f['size'];
    } 
    else if (in_array($ext, $extVideo)) {  
    $categories['video'] += $f['size'];
    }
    else {
        $categories['other'] += $f['size'];
    }
}

$trashSize = 0;
foreach ($files as $f) {
    if ($f['is_deleted'] == 1) $trashSize += $f['size'];
}

// Top 5 file lon nhat
// usort($files, function ($a, $b) {
//     return $b['size'] - $a['size'];
// });
// $largestFiles = array_slice($files, 0, 5);

$thresholdMB = 10; // ngưỡng 10 MB
$thresholdBytes = $thresholdMB * 1024 * 1024; // chuyển sang bytes

$largestFiles = array_filter($files, function($file) use ($thresholdBytes) {
    return $file['size'] > $thresholdBytes; 
});


function toMB($bytes) {
    return round($bytes / (1024 * 1024), 2);
}
function toGB($bytes) {
    return round($bytes / (1024 * 1024 * 1024), 2);
}
?>


<!-- MAIN CONTENT -->
<div class="main-container">
<section class="bg-white p-6 rounded-xl shadow-md mt-8">
    <h2 class="text-xl font-bold text-gray-700 uppercase mb-4 tracking-wider border-b pb-2">
        QUẢN LÝ BỘ NHỚ
    </h2>

    <!-- Tổng dluong -->
    <div class="flex flex-col md:flex-row items-center md:justify-between gap-6 my-6">
        <!-- Dung lương, thanh tiến trình -->
        <div>
            <p class="text-gray-600 text-sm">Tổng dung lượng đã sử dụng</p>
            <h3 class="text-3xl font-bold text-gray-900">
                <?= toMB($totalUsed) ?> MB / <?= toMB($maxStorage) ?> MB
            </h3>

            <div class="w-64 h-3 bg-gray-200 rounded-full mt-3 overflow-hidden">
                <div class="h-full bg-blue-500" style="width: <?= round($percent, 2) ?>%;"></div>
            </div>
        </div>

        <!-- Biểu đồ tròn -->
        <div class="flex flex-col items-center">
            <canvas id="fileChart" width="160" height="160"></canvas>
            <p class="text-xs text-gray-500 mt-2">Phân loại theo định dạng file</p>
        </div>
    </div>

    <!-- Thống kê theo category -->
    <div class="flex gap-4 overflow-x-auto mt-6 p-2">
        <div class="min-w-[180px] p-4 border rounded-lg bg-gray-50">
            <p class="font-semibold text-gray-700">Tài liệu</p>
            <p class="text-gray-500 text-sm mt-1"><?= toMB($categories['document']) ?> MB</p>
        </div>

        <div class="min-w-[180px] p-4 border rounded-lg bg-gray-50">
            <p class="font-semibold text-gray-700">Hình ảnh</p>
            <p class="text-gray-500 text-sm mt-1"><?= toMB($categories['image']) ?> MB</p>
        </div>

        <div class="min-w-[180px] p-4 border rounded-lg bg-gray-50">
            <p class="font-semibold text-gray-700">Video</p>
            <p class="text-gray-500 text-sm mt-1"><?= toMB($categories['video']) ?> MB</p>
        </div>

        <div class="min-w-[180px] p-4 border rounded-lg bg-gray-50">
            <p class="font-semibold text-gray-700">Thùng rác</p>
            <p class="text-gray-500 text-sm mt-1"><?= toMB($trashSize) ?> MB</p>
        </div>

        <div class="min-w-[180px] p-4 border rounded-lg bg-gray-50">
            <p class="font-semibold text-gray-700">Khác</p>
            <p class="text-gray-500 text-sm mt-1"><?= toMB($categories['other']) ?> MB</p>
        </div>
    </div>

    <!-- Tệp dung lượng lớn -->
    <div class="mt-10">
        <h3 class="text-lg font-bold text-gray-700 mb-3">Tệp dung lượng lớn</h3>

        <div class="space-y-3">
            <?php if (count($largestFiles) > 0): ?>
                <?php foreach ($largestFiles as $f): ?>
                    <div class="flex justify-between p-3 border rounded-lg bg-gray-50">
                        <span class="text-gray-700"><?= htmlspecialchars($f['name']) ?></span>
                        <span class="text-gray-500"><?= toMB($f['size']) ?> MB</span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500">Không có tệp nào.</p>
            <?php endif; ?>
        </div>
    </div>

</section>

</div>


<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const dataDocument = <?= $categories['document'] ?>;
const dataImage = <?= $categories['image'] ?>;
const dataVideo = <?= $categories['video'] ?>;
const dataOther = <?= $categories['other'] ?>;

const ctx = document.getElementById('fileChart').getContext('2d');

new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Tài liệu', 'Hình ảnh', 'Video', 'Khác'],
        datasets: [{
            data: [dataDocument, dataImage, dataVideo, dataOther],
            backgroundColor: [
                '#3b82f6', 
                '#10b981', 
                '#ef4444', 
                '#f59e0b' 
            ]
        }]
    },
    options: {
        plugins: { legend: { display: false } }
    }
});
</script>