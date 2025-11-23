<?php
include("../config/config.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

$user_id = $_SESSION['login']['user_id'];
$username = $_SESSION['login']['username'];
$role = $_SESSION['login']['role'];

$id = $_GET['id'];
echo $id;

$sql = "SELECT * FROM files WHERE file_id = $id";
$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);
$path = $row['path'];
unlink($path);

$sql = "DELETE FROM `files` WHERE `file_id` = '$id'";
mysqli_query($conn, $sql);
header("Location: index.php");

?>
