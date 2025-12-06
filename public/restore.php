<?php
include("../config/config.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

$id = $_GET['id'];

$sql = "SELECT * from recycle_bin WHERE id = $id";
$kq = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($kq);
$isFile = !empty($row['file_id']);
$isFolder = !empty($row['folder_id']);

if($isFile){
    $file_id = $row['file_id'];
    $sql = "UPDATE files SET is_deleted = 0, deleted_at = NULL WHERE file_id = $file_id";
    mysqli_query($conn, $sql);
}
if($isFolder){
    $folder_id = $row['folder_id'];
    $sql = "UPDATE folders SET is_deleted = 0, deleted_at = NULL WHERE folder_id = $folder_id";
    mysqli_query($conn, $sql);
}

$sql = "DELETE from recycle_bin WHERE id = $id";
mysqli_query($conn, $sql);

header("Location: trash.php");

?>