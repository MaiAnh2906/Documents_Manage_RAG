<?php
session_start();
include("../config/config.php");

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['login']['user_id'];
$role = $_SESSION['login']['role'];

if (isset($_POST['cancel'])) {
    header("Location: index.php");
    exit();
}
if (isset($_POST['create'])) {
    $folder_name = trim($_POST['folder_name'] ?? "");
    $description = trim($_POST['description'] ?? "");

    if ($folder_name == "") {
        $error = "Vui lòng nhập tên thư mục!";
    } else {
        $original_name = $folder_name;
        $i = 1;

        $sql = "SELECT folder_id  FROM folders 
                WHERE user_id='$user_id' 
                AND name='$folder_name'
                AND is_deleted = 0";
        $check = mysqli_query($conn, $sql);

        // Them so khi trung 
        while (mysqli_num_rows($check) > 0) {
            $folder_name = $original_name . " ($i)";
            $sql = "SELECT folder_id  FROM folders 
                    WHERE user_id='$user_id' 
                    AND name='$folder_name'";
            $check = mysqli_query($conn, $sql);
            $i++;
        }

        $sql = "INSERT INTO folders (user_id, name, description, created_at, is_deleted, status)
        VALUES ('$user_id', '$folder_name', '$description', NOW(), 0, 1)";
        mysqli_query($conn, $sql);

        header("Location: index.php?success=1");
        exit();
    }
}

define('ALLOW_ACCESS', true);
$pageTitle = "Tạo dự án mới";
include "navbar.php";
?>
<!-- MAIN CONTENT -->
    <div class="main-container">
        <h2>Tạo thư mục mới</h3>
        <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form action="" method="POST" class="mt-3" style="max-width: 400px;">
            <div class="mb-3">
                <input type="text" name="folder_name" class="form-control" placeholder="Tên dự án">
            </div>
            <div class="mb-3">
                <textarea name="description" class="form-control" placeholder="Mô tả dự án (không bắt buộc)" rows="3"></textarea>
            </div>
            <button type="submit" name="create" class="btn btn-primary">Tạo</button>
            <button type="submit" name="cancel" class="btn btn-secondary">Hủy</button>
        </form>
    </div>