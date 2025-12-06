<?php
include("../config/config.php");

$sql = "SELECT recycle_bin.*, 
        files.path AS file_path
        from recycle_bin
        LEFT JOIN files ON recycle_bin.file_id = files.file_id
        LEFT JOIN folders ON recycle_bin.folder_id = folders.folder_id
        WHERE expiry_date <= NOW()";
$kq = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($kq)){
    $id = $row['id'];
    $isFile = !empty($row['file_id']);
    $isFolder = !empty($row['folder_id']);

    if($isFile){
        $file_id = $row['file_id'];
        $file_path = $row['file_path'];

        if (!empty($file_path) && file_exists($file_path)) {
            unlink($file_path);
        }
        
        $sql = "DELETE FROM files WHERE file_id = $file_id";
        mysqli_query($conn, $sql);
    }
    if($isFolder){
        $folder_id = $row['folder_id'];
        $sql = "DELETE FROM folders WHERE folder_id = $folder_id";
        mysqli_query($conn, $sql);
    }

    
    $sql = "DELETE FROM recycle_bin WHERE id = $id";
    mysqli_query($conn, $sql);  

}


?>