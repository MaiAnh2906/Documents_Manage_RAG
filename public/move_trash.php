<?php

use Dom\Mysql;

include("../config/config.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

$username = $_SESSION['login']['username'];
$role = $_SESSION['login']['role'];
$type = "";
$id = 0;

if(isset($_GET['folder_id'])){
    $type = "folder";
    $id = $_GET['folder_id'];
}

if(isset($_GET['file_id'])){
    $type = "file";
    $id = $_GET['file_id'];
}

if($type == "file"){
    $sql = "UPDATE files SET is_deleted = 1, deleted_at = NOW() WHERE file_id = $id";
    mysqli_query($conn, $sql);

    $sql = "SELECT * FROM files WHERE file_id = $id";
    $kq = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($kq);
    $user_id = $row['user_id'];
    $deleted_at = $row['deleted_at'];
    $expireDays = 7;



    $sql = "INSERT INTO recycle_bin (file_id, user_id, deleted_at, expiry_date) VALUES ($id, $user_id, '$deleted_at', DATE_ADD(NOW(), INTERVAL $expireDays DAY))";
    mysqli_query($conn, $sql);
}

if($type == "folder"){
    $sql = "UPDATE folders SET is_deleted = 1, deleted_at = NOW() WHERE folder_id = $id";
    mysqli_query($conn, $sql);

    $sql = "SELECT * FROM folders WHERE folder_id = $id";
    $kq = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($kq);
    $user_id = $row['user_id'];
    $deleted_at = $row['deleted_at'];
    $expireDays = 7;

    $sql = "INSERT INTO recycle_bin (folder_id, user_id, deleted_at, expiry_date) VALUES ($id, $user_id, '$deleted_at', DATE_ADD(NOW(), INTERVAL $expireDays DAY))";
    mysqli_query($conn, $sql);
}

header("Location: index.php");

?>
