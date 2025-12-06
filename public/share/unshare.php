<?php
include("../../config/config.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}
if(isset($_GET['share_id']) && isset($_GET['type']) && isset($_GET['target_id'])){
    $id = $_GET['share_id'];
    $type = $_GET['type'];
    $target_id = $_GET['target_id'];

    $sql = "DELETE FROM shares WHERE share_id = $id";
    mysqli_query($conn, $sql);
    if($type == "file"){
        header("Location: share.php?file_id=$target_id");
    }
    if($type == "folder"){
        header("Location: share.php?folder_id=$target_id");
    }
    
}


// header("Location: share.php");

?>