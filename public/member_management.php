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

$edit_share_id = "";
$edit_email = "";
$edit_permission = "";

if (isset($_GET['file_id'])) {
    $type = "file";
    $target_id = $_GET['file_id'];
}
if (isset($_GET['folder_id'])) {
    $type = "folder";
    $target_id = $_GET['folder_id'];
}
if (isset($_GET['type']) && isset($_GET['target_id'])) {
    $type = $_GET['type'];
    $target_id = $_GET['target_id'];
}

if ($type == "") {
    die("Không xác định loại chia sẻ.");
}

if (isset($_GET['edit_share'])) {
    $edit_share_id = $_GET['edit_share'];

    $sql = "SELECT users.email, shares.permission
            FROM shares
            JOIN users ON shares.target_user_id = users.user_id
            WHERE share_id = $edit_share_id";
    $edit = $conn->query($sql)->fetch_assoc();

    $edit_email = $edit['email'];
    $edit_permission = $edit['permission'];
}
// SHARE
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
                // xử lý đã share rồi
                if ($type == "file") {
                    $sql = "SELECT * FROM shares WHERE file_id = $target_id AND target_user_id = $share_to";
                } else {
                    $sql = "SELECT * FROM shares WHERE folder_id = $target_id AND target_user_id = $share_to";
                }
                $existing_share = $conn->query($sql);

                if ($existing_share->num_rows > 0) {
                    $error = "Người dùng này đã được chia sẻ!";
                } else {
                    if ($type == "file") {
                        $sql = "INSERT INTO shares (file_id, owner_id, target_user_id, permission)
                                VALUES ($target_id, $user_id, $share_to, '$permission')";
                    } else {
                        $sql = "INSERT INTO shares (folder_id, owner_id, target_user_id, permission)
                                VALUES ($target_id, $user_id, $share_to, '$permission')";
                    }
                    $conn->query($sql);
                    $success = "Chia sẻ thành công!";
                }
            }
        }
    }
}
// UPDATE
if (isset($_POST['btnUpdate'])) {
    $permission = $_POST['permission'];
    $share_id = $_POST['share_id'];

    $conn->query("UPDATE shares SET permission='$permission' WHERE share_id=$share_id");
    $success = "Cập nhật quyền thành công!";
    $edit_share_id = "";
    $edit_email = "";
    $edit_permission = "";
}
// CANCEL
if (isset($_POST['btnCancel'])) {
    header("Location: folder.php?folder_id=$target_id");
}

// chỗ này copy bên folder.php sang

if (!isset($_GET['folder_id'])) {
    die("Không có thư mục được chọn.");
}

$folder_id = intval($_GET['folder_id']);
$user_id = $_SESSION['login']['user_id'];

// Lấy thông tin folder
$sql = "SELECT folders.*, users.username AS owner_name, users.user_id AS owner_id
        FROM folders
        JOIN users ON folders.user_id = users.user_id
        WHERE folder_id = $folder_id";

$res = mysqli_query($conn, $sql);
$folder = mysqli_fetch_assoc($res);

if (!$folder) {
    die("Thư mục không tồn tại.");
}

$isOwner = ($user_id == $folder['owner_id']); 

$participants = [];
$sql = "SELECT users.username, shares.permission FROM shares 
        JOIN users ON shares.target_user_id = users.user_id
        WHERE shares.folder_id = $folder_id";
$kq = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($kq)){
    $participants[] = $row;
}

$sql = "SELECT permission FROM shares 
        WHERE folder_id = $folder_id
        AND target_user_id = $user_id";
$kq = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($kq);

$permission = $row['permission'] ?? null;

$canAdd = ($isOwner || ($permission == 'contributor') || ($permission == 'operator')) && ($folder['status'] == 1);


if(isset($_GET['delete_content_id'])){
    $delete_ct = $_GET['delete_content_id'];

    $sql = "SELECT * FROM files 
        WHERE content_id = $delete_ct AND folder_id = $folder_id";
    $kq = mysqli_query($conn, $sql);
    $row_file = mysqli_fetch_assoc($kq);

    if(file_exists($row_file['path'])){
        unlink($row_file['path']);
    }

    $sql = "DELETE FROM contents WHERE content_id = $delete_ct";
    mysqli_query($conn, $sql);

    $sql_delete = "DELETE FROM files WHERE content_id = $delete_ct AND folder_id = $folder_id";
    mysqli_query($conn, $sql_delete);

    header("Location: folder.php?folder_id=$folder_id");
    exit();
}

if(isset($_POST['btn_stop'])){

    $sql = "UPDATE folders SET status = 0 WHERE folder_id = $folder_id";
    mysqli_query($conn, $sql);

    $sql = "UPDATE shares SET permission = 'viewer' WHERE folder_id = $folder_id";
    mysqli_query($conn, $sql);
    
    header("Location: folder.php?folder_id=$folder_id");
}

if(isset($_POST['btn_start'])){
    $sql = "UPDATE folders SET status = 1 WHERE folder_id = $folder_id";
    mysqli_query($conn, $sql);

    header("Location: folder.php?folder_id=$folder_id");
}


?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <title>Bảng làm việc dự án</title>

    <style>
        body {
            background: #f4f7f9;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #387af5;
            color: #fff;
            border-radius: 0 60px 40px 0;
            padding: 30px 20px;
            position: fixed;
            left: 0;
            top: 0;
        }

        .sidebar-btn {
            background: #fff;
            color: #1e73e8;
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: 500;
            width: 100%;
            border: none;
        }

        .member-item:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="flex items-center gap-2">
            <h2 class="text-xl font-bold"><?php echo htmlspecialchars($folder['name']); ?></h2>
            <?php if ($folder['status'] == 1) { ?>
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs flex items-center">
                    Hoạt động
                </span>
            <?php } else { ?>
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs flex items-center">
                    Ngừng hoạt động
                </span>
            <?php } ?>
        </div>
        <hr>

        <p class="text-sm tracking-wide mt-3 mb-2 opacity-90 font-semibold">
            Thao tác
        </p>

        <?php if ($isOwner) { ?>
            <form method="post">
                <?php if ($folder['status'] == 1) { ?>
                    <input type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg mb-3" name="btn_stop" onclick="return confirm('Bạn có chắc muốn ngừng dự án không?');" value="Ngừng dự án">

                <?php } else { ?>
                    <input type="submit" name="btn_start" value="Khởi động dự án"
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg mb-3 
                    font-semibold shadow-sm hover:shadow transition" />
                <?php } ?>
            </form>
        <?php } ?>

        <a
            href="index.php"
            class="inline-flex items-center gap-2 px-4 py-2 mb-5
           bg-white border border-gray-300 text-gray-700 
           rounded-lg shadow-sm hover:bg-gray-100 
           hover:border-gray-400 transition-all duration-200">
            ← Quay lại trang chủ
        </a>



        <!-- Thành viên -->
        <h3 class="text-lg font-semibold mb-2">Danh sách thành viên</h3>

        <ul class="space-y-2 mb-4">
            <li class="member-item p-2 rounded">👑 Owner — <?php echo $folder['owner_name']; ?></li>
            <?php
            foreach ($participants as $participant) {
                echo '<li class="member-item p-2 rounded">👤 ' . $participant['permission'] . ' — ' . $participant['username'] . ' </li>';
            }
            ?>
        </ul>

        <?php if ($isOwner) { ?>
            <a href="member_management.php?folder_id=<?= $folder_id ?>" class="underline text-sm">Quản lý thành viên</a>
        <?php } ?>
        <a class="dropdown-item" href="#" onclick="openFolderDetail(<?= $folder_id ?>)">
            <i class="bi bi-eye"></i> Chi tiết dự án
        </a>


    </aside>

    <!-- MAIN CONTENT -->
    <div class="ml-[260px] p-8">
        <h2 class="text-xl font-bold mb-4">
            Quản lý thành viên
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
            <input type="email" name="email"
                value="<?php echo $edit_email; ?>"
                <?php echo ($edit_email != "") ? "readonly" : ""; ?>
                class="w-full border p-2 rounded mb-4">

            <label>Quyền truy cập</label>
            <select name="permission" class="w-full border p-2 rounded mb-4">
                <option value="viewer" <?php if ($edit_permission == "viewer") echo "selected"; ?>>Người quan sát</option>
                <option value="contributor" <?php if ($edit_permission == "contributor") echo "selected"; ?>>Người đóng góp</option>
                <option value="operator" <?php if ($edit_permission == "operator") echo "selected"; ?>>Người điều hành</option>
            </select>
            <!-- lưu share id để sửa -->
            <?php if ($edit_share_id != "") { ?>
                <input type="hidden" name="share_id" value="<?php echo $edit_share_id; ?>">
            <?php } ?>

            <button class="bg-blue-600 text-white px-4 py-2 rounded"
                name="<?php echo ($edit_share_id != "") ? 'btnUpdate' : 'btnShare'; ?>">
                <?php echo ($edit_share_id != "") ? 'Cập nhật' : 'Chia sẻ'; ?>
            </button>
            <button class="bg-red-600 text-white px-4 py-2 rounded" name="btnCancel">
                Hủy
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

                <a href="?edit_share=<?php echo $share_id; ?>&type=<?php echo $type; ?>&target_id=<?php echo $target_id; ?>&folder_id=<?php echo $folder_id; ?>" class="text-blue-600 hover:text-blue-800 mr-3">Sửa</a>
                <a href="share/unshare.php?share_id=<?php echo $share_id; ?>&type=<?php echo $type; ?>&target_id=<?php echo $target_id; ?>&folder_id=<?php echo $folder_id; ?>" class="text-red-600 hover:text-red-800">Xóa</a>
            </div>

        <?php } ?>

    </div>


</body>

</html>