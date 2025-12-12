<?php
include("../config/config.php");
include("auto_clean.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

$user_id = $_SESSION['login']['user_id'];
$username = $_SESSION['login']['username'];
$role = $_SESSION['login']['role'];

// xác định loại chia sẻ file/folder
$type = "folder";
$target_id = ""; 

if (isset($_GET['folder_id'])) {
    $type = "folder";
    $target_id = $_GET['folder_id'];
}

if ($target_id == "") {
    die("Chưa chọn dự án để chia sẻ");
}

if (isset($_POST['btnShare'])) {
    $email = $_POST['email'];
    $permission = $_POST['permission'];
    $type = $_POST['type'];
    $target_id = $_POST['target_id'];

    // tìm userid nhận
    $sql = "SELECT user_id FROM users WHERE email = '$email'";
    $u = $conn->query($sql);

    if ($u->num_rows == 0) {
        $error = "Email chưa được đăng ký tài khoản.";
    } else {
        $share_to = $u->fetch_assoc()['user_id'];

        // kiểm tra quyền sở hữu
        if ($type == "file") {
            $sql = "SELECT user_id FROM files WHERE file_id = $target_id";
        } else {
            $sql = "SELECT user_id FROM folders WHERE folder_id = $target_id";
        }
        $kq = $conn->query($sql);

        if ($kq->num_rows == 0) {
            $error = "Tệp/Thư mục không tồn tại.";
        } else {
            $owner = $kq->fetch_assoc()['user_id'];
            
            if ($owner != $user_id) {
                $error = "Bạn không phải chủ sở hữu!";
            } else {
                // Lưu vào bảng se
                if ($type == "file") {
                    $sql = "INSERT INTO shares (file_id, owner_id, target_user_id, permission)
                            VALUES ($target_id, $user_id, $share_to, '$permission')
                            ON DUPLICATE KEY UPDATE permission='$permission'";
                    $conn->query($sql);
                } else {
                    $sql = "INSERT INTO shares (folder_id, owner_id, target_user_id, permission)
                            VALUES ($target_id, $user_id, $share_to, '$permission')
                            ON DUPLICATE KEY UPDATE permission='$permission'";
                    $conn->query($sql);
                }
                $success = "Chia sẻ thành công!";
            }
        }     
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="main-container">
        <h2 class="text-xl font-bold mb-4">
            Chia sẻ <?php echo ($type == "file") ? "File" : "Folder"; ?>
        </h2>

        <?php 
            if (!empty($error)) { ?>
                <div class="bg-red-200 text-red-700 p-3 rounded mb-3"><?php echo $error ?></div>
        <?php } 
            if (!empty($success)) { ?>
                <div class="bg-green-200 text-green-700 p-3 rounded mb-3"><?php echo $success; ?></div>
        <?php } ?>

        <form method="POST">
            <input type="hidden" name="type" value="<?php echo $type; ?>">
            <input type="hidden" name="target_id" value="<?php echo $target_id; ?>">

            <label>Email người nhận</label>
            <input type="email" name="email" required class="w-full border p-2 rounded mb-4">

            <label>Quyền truy cập</label>
            <select name="permission" class="w-full border p-2 rounded mb-4">
                <option value="viewer">Người quan sát</option>
                <option value="contributor">Người đóng góp</option>
                <option value="operator">Người điều hành</option>
            </select>

            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" name="btnShare">
                Chia sẻ
            </button>
        </form>


        <hr class="my-5">

        <h3 class="font-semibold mb-2">Người đã được chia sẻ:</h3>

        <?php
        if ($type == "file") {
            $sql = "SELECT users.email, shares.permission, shares.share_id
                    FROM shares
                    JOIN users ON shares.target_user_id  = users.user_id
                    WHERE file_id = $target_id";
        } else {
            $sql = "SELECT users.email, shares.permission, shares.share_id
                    FROM shares
                    JOIN users ON shares.target_user_id  = users.user_id
                    WHERE folder_id = $target_id";
        }

        $shared = $conn->query($sql);

        if ($shared->num_rows == 0) {
            echo "<p class='text-gray-500'>Chưa chia sẻ cho ai.</p>";
        }

        while ($s = $shared->fetch_assoc()) {
            $share_id = $s['share_id'];
        ?>

            <div class="border p-2 rounded mb-2 flex justify-between">
                <div>
                    <b><?php echo $s['email']; ?></b>
                    — quyền: <b><?php echo $s['permission']; ?></b>
                </div>

                <!-- unshare -->
                <a href="share/unshare.php?share_id=<?php echo $share_id; ?>&type=<?php echo $type; ?>&target_id=<?php echo $target_id; ?>"
                    class="text-red-600 hover:text-red-800">Hủy</a>
            </div>

        <?php } ?>

    </div>

</body>
</html>