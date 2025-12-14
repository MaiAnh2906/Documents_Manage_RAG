<?php
include("../config/config.php");
include("func/function.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

$id = $_GET['id'];
// folders.path AS folder_path
$sql = "SELECT recycle_bin.*, 
        files.path AS file_path, files.name AS file_name, folders.name AS folder_name
        from recycle_bin
        LEFT JOIN files ON recycle_bin.file_id = files.file_id
        LEFT JOIN folders ON recycle_bin.folder_id = folders.folder_id
        WHERE id = $id";
$kq = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($kq);
$isFile = !empty($row['file_id']);
$isFolder = !empty($row['folder_id']);

if($isFile){
    $file_id = $row['file_id'];
    $file_path = $row['file_path']; 

    if (!empty($file_path) && file_exists($file_path)) {
        unlink($file_path);
    }

// lưu activity (PHẢI ĐỂ TRƯỚC DELETE)
logActivity(
    $conn,
    $row['user_id'],
    "delete_file_permanent",
    null,
    $file_id,
    null,
    null,
    "Xóa vĩnh viễn tệp " . $row['file_name']
);

// xóa theo đúng thứ tự FK (KHÔNG JOIN)
mysqli_query($conn, "DELETE FROM shares WHERE file_id = $file_id");
mysqli_query($conn, "DELETE FROM files WHERE file_id = $file_id");

}
if($isFolder){
    $folder_id = $row['folder_id'];

    $sql_file = "SELECT path FROM files   
            JOIN contents ON files.content_id = contents.content_id
            WHERE files.folder_id = $folder_id";
    $result_file = mysqli_query($conn, $sql_file);

    while ($row_file = mysqli_fetch_assoc($result_file)) {
        $filePath = $row_file['path'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

// lưu activity (PHẢI ĐỂ TRƯỚC DELETE)
logActivity(
    $conn,
    $row['user_id'],
    "delete_folder_permanent",
    $folder_id,
    null,
    null,
    null,
    "Xóa vĩnh viễn dự án " . $row['folder_name']
);

// xóa đúng thứ tự FK (KHÔNG JOIN)
mysqli_query($conn, "DELETE FROM shares WHERE folder_id = $folder_id");
mysqli_query($conn, "DELETE FROM files WHERE folder_id = $folder_id");
mysqli_query($conn, "DELETE FROM contents WHERE folder_id = $folder_id");
mysqli_query($conn, "DELETE FROM folders WHERE folder_id = $folder_id");

    
}

$sql = "DELETE FROM recycle_bin WHERE id = $id";
mysqli_query($conn, $sql);

header("Location: trash.php");

?>