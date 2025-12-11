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
    $sql = "UPDATE files 
    LEFT JOIN shares ON files.file_id = shares.file_id
    SET files.is_deleted = 1, files.deleted_at = NOW(), shares.is_deteled = 1
    WHERE files.file_id = $id";
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
    $sql = "UPDATE folders 
        LEFT JOIN shares ON folders.folder_id = shares.folder_id
        SET folders.is_deleted = 1, folders.deleted_at = NOW(), shares.is_deleted = 1
        WHERE folders.folder_id = $id";
    mysqli_query($conn, $sql);

    $sql = "SELECT * FROM folders WHERE folder_id = $id";
    $kq = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($kq);
    $user_id = $row['user_id'];
    $expireDays = 7;

    $sql = "INSERT INTO recycle_bin (folder_id, user_id, deleted_at, expiry_date) VALUES ($id, $user_id, NOW(), DATE_ADD(NOW(), INTERVAL $expireDays DAY))";
    mysqli_query($conn, $sql);
}

header("Location: index.php");

?>
