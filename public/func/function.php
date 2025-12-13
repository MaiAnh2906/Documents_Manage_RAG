<?php
function logActivity($conn, $user_id, $action, $folder_id = null, $file_id = null, $content_id = null, $comment_id = null, $description = null) {
    $user_id = intval($user_id);
    $folder_id = $folder_id ? intval($folder_id) : "NULL";
    $file_id = $file_id ? intval($file_id) : "NULL";
    $content_id = $content_id ? intval($content_id) : "NULL";
    $comment_id = $comment_id ? intval($comment_id) : "NULL";
    $action = mysqli_real_escape_string($conn, $action);
    $description = $description ? "'" . mysqli_real_escape_string($conn, $description) . "'" : "NULL";

    $sql = "INSERT INTO activity_logs (user_id, action, folder_id, file_id, content_id, comment_id, description) 
            VALUES ($user_id, '$action', $folder_id, $file_id, $content_id, $comment_id, $description)";
    
    mysqli_query($conn, $sql);
}
// Hàm để render label màu theo hành động
function actionLabel($action) {
    $action = strtolower($action);
    switch (true) {
        case strpos($action, 'đăng nhập') !== false:
            return "<span class='badge bg-success text-white px-3 py-2 rounded-pill'>Đăng nhập</span>";
        case strpos($action, 'tạo dự án') !== false:
        case strpos($action, 'tạo folder') !== false:
            return "<span class='badge bg-primary text-white px-3 py-2 rounded-pill'>Tạo dự án</span>";
        case strpos($action, 'mời thành viên') !== false:
            return "<span class='badge bg-info text-white px-3 py-2 rounded-pill'>Mời thành viên</span>";
        case strpos($action, 'đăng xuất') !== false:
            return "<span class='badge bg-secondary text-white px-3 py-2 rounded-pill'>Đăng xuất</span>";
        default:
            return "<span class='badge bg-light text-dark px-3 py-2 rounded-pill'>" . htmlspecialchars($action) . "</span>";
    }
}
?>