<?php
include("../../config/config.php");
include("../func/function.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

$user_id = $_SESSION['login']['user_id'];
$username = $_SESSION['login']['username'];
$role = $_SESSION['login']['role'];

// xác định loại chia sẻ file/folder
$type = "";
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
                    // Nếu chưa được chia sẻ, thực hiện chia sẻ mới
                    if ($type == "file") {
                        $sql = "INSERT INTO shares (file_id, owner_id, target_user_id, permission)
                                VALUES ($target_id, $user_id, $share_to, '$permission')";
                    } else {
                        $sql = "INSERT INTO shares (folder_id, owner_id, target_user_id, permission)
                                VALUES ($target_id, $user_id, $share_to, '$permission')";
                    }
                    $conn->query($sql);
                    // lưu activity
                    if ($type == "file") {
                        logActivity(
                            $conn,
                            $user_id,
                            "share_file",
                            null,
                            $target_id,
                            null,
                            null,
                            "Chia sẻ file cho $email với quyền $permission"
                        );
                    } else {
                        logActivity(
                            $conn,
                            $user_id,
                            "share_folder",
                            $target_id,
                            null,
                            null,
                            null,
                            "Chia sẻ folder cho $email với quyền $permission"
                        );
                    }
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
    // lưu activity
    // lấy thông tin share để log cho rõ
    $info = $conn->query("
        SELECT file_id, folder_id 
        FROM shares 
        WHERE share_id = $share_id
    ")->fetch_assoc();

    if ($info['file_id']) {
        logActivity(
            $conn,
            $user_id,
            "update_share_permission",
            null,
            $info['file_id'],
            null,
            null,
            "Cập nhật quyền chia sẻ file thành $permission"
        );
    } else {
        logActivity(
            $conn,
            $user_id,
            "update_share_permission",
            $info['folder_id'],
            null,
            null,
            null,
            "Cập nhật quyền chia sẻ folder thành $permission"
        );
    }
    $success = "Cập nhật quyền thành công!";
    $edit_share_id = "";
    $edit_email = "";
    $edit_permission = "";
}
// CANCEL
if (isset($_POST['btnCancel'])) {
    header("Location: ../index.php");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Trang chủ</title>
    <style>
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
        }

        body {
            padding-top: 65px;
        }

        .search {
            border: none;
            margin-left: 10px;
        }

        .search_form {
            border: solid 1px gray;
            border-radius: 10px;
            margin: 5px;
            padding: 3px;
            width: 30em;
        }

        .bi {
            margin-left: 10px;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #387af5;
            color: #fff;
            border-radius: 0 60px 40px 0;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            align-items: center;
            z-index: 1000;
        }

        .sidebar .btn-upload {
            background: #fff;
            color: #1e73e8;
            font-weight: 500;
            border-radius: 30px;
            padding: 10px 20px;
            width: 100%;
            border: none;
        }

        .sidebar .nav-link {
            color: #fff;
            align-items: center;
        }


        .dropdown-toggle::after {
            display: none;
        }

        .dropdown-menu {
            min-width: 180px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .dropdown-item:hover {
            background-color: #f1f1f1;
        }

        .btn-custom {
            background: #fff;
            color: #2c70ceff;
            border-radius: 15px;
            padding: 10px 40px;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-custom:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        /* Main conten */
        .main-container {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            /* background-color: #f4f7f9; */
        }

        .file-row:hover {
            background-color: #f0f8ff;
        }

        .highlighted-row {
            background-color: #e3f2fd;
        }

        .avatar-group {
            display: flex;
            margin-left: 8px;
        }

        .avatar-group>img {
            border: 2px solid white;
            margin-left: -8px;
            transition: transform 0.2s ease-in-out;
        }

        .avatar-group>img:hover {
            transform: translateY(-2px);
            z-index: 1;
        }
    </style>
</head>

<body>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const currentPage = window.location.pathname.split("/").pop();
            const links = document.querySelectorAll(".sidebar .nav-link");

            links.forEach(link => {
                const linkPage = link.getAttribute("href").split("/").pop();
                if (linkPage === currentPage) {
                    const button = link.closest("button");
                    if (button) {
                        button.style.backgroundColor = '#2c70ce';
                        button.style.color = 'white';
                        button.classList.add("shadow-inner", "rounded-lg");
                    }
                    link.classList.add("text-white");
                }
            });
        });
    </script>

    <!-- TOP NAV BAR -->
    <nav class="navbar navbar-expand-sm fixed-top bg-white shadow-sm">
        <div class="container-fluid justify-content-between">
            <!-- Logo -->
            <a class="navbar-brand" href="#">
                <img src="" alt="" width="30" height="24">
            </a> <span>Doogle Drive</span>
            <!-- Search -->
            <form method="get" action="search.php"
                class="relative flex items-center mx-auto bg-white border border-gray-300 rounded-full px-4 py-2 shadow-sm focus-within:ring-2 focus-within:ring-[#387af5] transition-all duration-200 w-[30em]">
                <i class="bi bi-search text-gray-500 text-lg mr-2"></i>
                <input
                    class="flex-1 bg-transparent border-none outline-none text-gray-700 placeholder-gray-400"
                    type="search"
                    placeholder="Search Drive..."
                    aria-label="Search"
                    name="search" required>
            </form>

            <div class="dropdown">
                <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="userDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="me-2">Xin chào, <?php echo $_SESSION['login']['username']; ?></span>
                    <img src="https://via.placeholder.com/40" alt="Avatar"
                        class="rounded-circle border me-2" width="40" height="40" style="margin-left: 10px">
                </a>

                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="profile.php">Trang cá nhân</a></li>
                    <li><a class="dropdown-item" href="settings.php">Cài đặt</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="../logout.php">Đăng xuất</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <!-- Upload -->
        <div class="dropdown w-full">
            <button class="btn btn-custom dropdown-toggle w-full" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-plus-lg me-2"></i> New
            </button>
            <ul class="dropdown-menu shadow-xl" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item flex items-center" href="create_folder.php"><i class="bi bi-folder me-2 text-yellow-600"></i> Thư mục mới</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item flex items-center" href="upload.php"><i class="bi bi-file-earmark-arrow-up me-2 text-gray-500"></i> Tải tệp lên</a></li>
                <li><a class="dropdown-item flex items-center" href="#"><i class="bi bi-folder-fill me-2 text-gray-500"></i> Tải thư mục lên</a></li>
            </ul>
        </div>
        <br>
        <!-- Nav -->
        <nav class="nav flex-column">
            <ul class="w-full flex flex-col gap-2">
                <li
                    class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                    <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                        <a href="../index.php" class="nav-link"><i class="bi bi-house"></i> Trang chủ</a>
                    </button>
                </li>

                <li
                    class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                    <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                        <a href="../drive.php" class="nav-link"><i class="bi bi-cloud"></i> Drive của tôi</a>
                    </button>
                </li>

                <li
                    class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                    <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                        <a href="#" class="nav-link"><i class="bi bi-people"></i> Được chia sẻ với tôi</a>
                    </button>
                </li>

                <li
                    class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                    <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                        <a href="#" class="nav-link"><i class="bi bi-clock-history"></i> Recents</a>
                    </button>
                </li>

                <?php if ($_SESSION['login']['role'] == 1) { ?>
                    <li
                        class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                        <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                            <a href="../user_manage.php" class="nav-link"><i class="bi bi-clock-history"></i> Quản lý tài khoản</a>
                        </button>
                    </li>
                <?php } ?>

                <?php if ($_SESSION['login']['role'] == 1) { ?>
                    <li
                        class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                        <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                            <a href="#" class="nav-link"><i class="bi bi-clock-history"></i> Thống kê</a>
                        </button>
                    </li>
                <?php } ?>

                <li
                    class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                    <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                        <a href="../trash.php" class="nav-link"><i class="bi bi-trash"></i> Trash</a>
                    </button>
                </li>

                <li
                    class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                    <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                        <a href="../storage.php" class="nav-link"><i class="bi bi-database"></i> Bộ nhớ</a>
                    </button>
                </li>

            </ul>

        </nav>
    </div>


    <!-- MAIN CONTENT -->
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
            </button
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

                    <a href="?edit_share=<?php echo $share_id; ?>&type=<?php echo $type; ?>&target_id=<?php echo $target_id; ?>" class="text-blue-600 hover:text-blue-800 mr-3">Sửa</a>
                    <a href="unshare.php?share_id=<?php echo $share_id; ?>&type=<?php echo $type; ?>&target_id=<?php echo $target_id; ?>" class="text-red-600 hover:text-red-800">Hủy</a>
                </div>

            <?php } ?>

    </div>


</body>

</html>