<?php
if (isset($_GET['ajax']) && $_GET['ajax'] == "1") {

    include("../config/config.php");
    session_start();

    if (!isset($_SESSION['login'])) {
        echo "Chưa đăng nhập.";
        exit;
    }

    if (!isset($_GET['folder_id'])) {
        echo "Không tìm thấy thư mục.";
        exit;
    }

    $folder_id = intval($_GET['folder_id']);

    $sql = "SELECT * FROM folders WHERE folder_id = $folder_id AND is_deleted = 0";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);

    if (!$row) {
        echo "Không tìm thấy thư mục.";
        exit;
    }

    echo "
        <p><b>Tên thư mục:</b> {$row['name']}</p>
        <p><b>Mô tả:</b> " . (!empty($row['description']) ? htmlspecialchars($row['description']) : "<i>Chưa có mô tả</i>") . "</p>
        <p><b>Ngày tạo:</b> {$row['created_at']}</p>
        <p><b>Trạng thái:</b> " .
            ($row['status'] == 1
                ? "<span class='text-green-600 font-semibold'>Hoạt động</span>"
                : "<span class='text-red-600 font-semibold'>Ngừng hoạt động</span>"
            )
        . "</p>
    ";
    exit; 
}
?>

<!-- popup -->
<style>
.popup-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.popup-box {
    background: white;
    width: 420px;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}
</style>

<div id="folderDetailPopup" class="popup-overlay">
    <div class="popup-box">
        <h2 class="text-lg font-bold mb-3">Chi tiết Folder</h2>

        <div id="folderDetailContent">
            Đang tải...
        </div>

        <button onclick="closeFolderDetail()"
                class="mt-4 px-4 py-2 bg-blue-600 text-white rounded">
            Đóng
        </button>
    </div>
</div>

<script>
function openFolderDetail(folder_id) {
    document.getElementById("folderDetailPopup").style.display = "flex";

    fetch("detail_folder.php?ajax=1&folder_id=" + folder_id)
        .then(res => res.text())
        .then(html => {
            document.getElementById("folderDetailContent").innerHTML = html;
        });
}

function closeFolderDetail() {
    document.getElementById("folderDetailPopup").style.display = "none";
}
</script>
