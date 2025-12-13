<?php
session_start();
include("../config/config.php");

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['login']['user_id'];
$role = $_SESSION['login']['role'];

if (!isset($_GET['folder_id'])) {
    header("Location: index.php");
    exit();
}

$folder_id = intval($_GET['folder_id']);

// Lấy dữ liệu folder hiện tại
$sql = "SELECT * FROM folders WHERE folder_id = $folder_id AND user_id = '$user_id' AND is_deleted = 0";
$res = mysqli_query($conn, $sql);
$folder = mysqli_fetch_assoc($res);

if (!$folder) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['cancel'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['update'])) {
    $folder_name = trim($_POST['folder_name'] ?? "");
    $description = trim($_POST['description'] ?? "");

    if ($folder_name == "") {
        $error = "Vui lòng nhập tên thư mục!";
    } else {
        $original_name = $folder_name;
        $i = 1;

        $sql = "SELECT folder_id FROM folders 
                WHERE user_id='$user_id' 
                AND name='$folder_name' 
                AND folder_id != $folder_id
                AND is_deleted = 0";
        $check = mysqli_query($conn, $sql);

        while (mysqli_num_rows($check) > 0) {
            $folder_name = $original_name . " ($i)";
            $sql = "SELECT folder_id FROM folders 
                    WHERE user_id='$user_id' 
                    AND name='$folder_name'
                    AND folder_id != $folder_id
                    AND is_deleted = 0";
            $check = mysqli_query($conn, $sql);
            $i++;
        }

        // Cập nhật
        $sql = "UPDATE folders SET name='$folder_name', description='$description' 
                WHERE folder_id = $folder_id AND user_id = '$user_id'";
        mysqli_query($conn, $sql);

        header("Location: index.php?success=2"); 
        exit();
    }
}

define('ALLOW_ACCESS', true);
$pageTitle = "Chỉnh sửa dự án";
include "navbar.php";
?>

<!-- MAIN CONTENT -->
<div class="main-container">
    <h2>Chỉnh sửa dự án</h2>
    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form action="" method="POST" class="mt-3" style="max-width: 400px;">
        <div class="mb-3">
            <input type="text" name="folder_name" class="form-control" 
                   value="<?php echo htmlspecialchars($folder['name']); ?>" 
                   placeholder="Tên dự án">
        </div>
        <div class="mb-3">
            <textarea name="description" class="form-control" 
                      placeholder="Mô tả dự án (không bắt buộc)" rows="3"><?php echo htmlspecialchars($folder['description']); ?></textarea>
        </div>
        <button type="submit" name="update" class="btn btn-primary">Cập nhật</button>
        <button type="submit" name="cancel" class="btn btn-secondary">Hủy</button>
    </form>
</div>
