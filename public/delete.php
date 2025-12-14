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

    // lưu activity
    logActivity($conn, $row['user_id'], "delete", null, $file_id, null, null, "Xóa vĩnh viễn tệp " . $row['file_name']);

    $sql = "DELETE files, shares FROM files 
    LEFT JOIN shares ON files.file_id = shares.file_id
    WHERE files.file_id = $file_id";
    mysqli_query($conn, $sql);
    
}
if($isFolder){
    $folder_id = $row['folder_id'];

    $sql = "SELECT file_path FROM contents WHERE folder_id = $folder_id";
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $filePath = $row['file_path'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // lưu activity
    logActivity($conn, $row['user_id'], "delete", $folder_id, null, null, null, "Xóa vĩnh viễn dự án " . $row['folder_name']);

    $sql = "DELETE folders, contents, shares FROM folders 
    LEFT JOIN contents ON folders.folder_id = contents.folder_id
    LEFT JOIN shares ON folders.folder_id = shares.folder_id
    WHERE folders.folder_id = $folder_id";
    mysqli_query($conn, $sql);
    
}

$sql = "DELETE FROM recycle_bin WHERE id = $id";
mysqli_query($conn, $sql);

header("Location: trash.php");

?>