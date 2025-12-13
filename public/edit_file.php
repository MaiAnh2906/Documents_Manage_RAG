<?php

use function PHPSTORM_META\type;

include("../config/config.php");
include("func/function.php");

session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

$id = $_GET['id'];
if (isset($_POST['btn_cancel'])) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT * FROM files WHERE file_id = $id";
$kq = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($kq);
$file_name = $row['name'];

if (isset($_POST['btn_update'])) {
    $user_id = $_SESSION['login']['user_id'];
    $name = $_POST['filename'];

    if ($_FILES['fileInput']['error'] == 4) {
        if ($name == "") {
            $error = "Vui lòng nhập tên bạn muốn đổi";
        } else {
            $sql = "SELECT * FROM files WHERE file_id = $id";
            $kq = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($kq);
            $type = $row['type'];
            $filename = $name . "." . $type;
            $sql = "UPDATE `files` SET `name`='$filename', `user_id`=$user_id, `upload_date`=NOW() WHERE file_id = $id";
            $kq = mysqli_query($conn, $sql);
            // lưu activity
            logActivity($conn, $user_id, "edit", null, $id, null, null, "Đổi tên file $file_name thành $filename");
            header("Location: index.php");
        }
    } elseif (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] == 0) {
        $target = "uploads/";
        $filename = $_FILES['fileInput']['name'];
        $type = pathinfo($filename, PATHINFO_EXTENSION);
        $newname = uniqid("file_", true) . "." . $type;
        $path = $target . $newname;
        $size = $_FILES['fileInput']['size'];
        move_uploaded_file($_FILES['fileInput']['tmp_name'], $path);

        $sql = "SELECT * FROM files WHERE file_id = $id";
        $kq = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($kq);
        $old_path = $row['path'];
        unlink($old_path);

        $sql = "UPDATE files SET `user_id`=$user_id, `name`='$filename', `path`='$path', `type`='$type', `size`=$size, `upload_date`=NOW() WHERE file_id = $id";
        $kq = mysqli_query($conn, $sql);
    }
}

define('ALLOW_ACCESS', true);
$pageTitle = "Sửa file";
include "navbar.php";
?>

<div class="main-container">
    <h2 class="text-xl font-bold text-gray-800 uppercase mb-5 tracking-wide">
        Edit Files
    </h2>

    <form method="post" enctype="multipart/form-data" class="space-y-5">

        <div>
            <label class="block font-medium text-gray-600 mb-1">Đổi tên file:</label>
            <input type="text" name="filename" placeholder="<?php echo $file_name; ?>"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-500 outline-none">
        </div>

        <div>
            <label class="block font-medium text-gray-600 mb-1">Chọn file mới (nếu muốn thay):</label>
            <input type="file" name="fileInput" id="fileInput"
                class="w-full text-sm text-gray-700 file:px-4 file:py-2 file:rounded-lg file:border file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
        </div>

        <div class="flex justify-end gap-3 pt-3">
            <button type="submit" name="btn_cancel"
                class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-gray-800 transition">
                Hủy
            </button>

            <button type="submit" name="btn_update"
                class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow transition">
                OK
            </button>
        </div>
    </form>
</div>