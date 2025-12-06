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
// if ($role != 1) {
//     header('Location: error.php');
// }
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
                    <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
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
                        <a href="index.php" class="nav-link"><i class="bi bi-house"></i> Trang chủ</a>
                    </button>
                </li>

                <li
                    class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                    <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                        <a href="drive.php" class="nav-link"><i class="bi bi-cloud"></i> Drive của tôi</a>
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
                            <a href="user_manage.php" class="nav-link"><i class="bi bi-clock-history"></i> Quản lý tài khoản</a>
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
                        <a href="trash.php" class="nav-link"><i class="bi bi-trash"></i> Trash</a>
                    </button>
                </li>

                <li
                    class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                    <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                        <a href="#" class="nav-link"><i class="bi bi-database"></i> Bộ nhớ</a>
                    </button>
                </li>

            </ul>

        </nav>
    </div>


    <!-- MAIN CONTENT -->
    <div class="main-container">
        <!-- FOLDERS SHARED WITH ME -->
        <h2 class="text-xl font-bold text-gray-700 uppercase mt-10 mb-4 tracking-wider border-b pb-2">ĐƯỢC CHIA SẺ VỚI TÔI (THƯ MỤC)</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            <?php
            $sqlSharedFolder = "
                SELECT folders.*, users.username AS owner_name
                FROM shares
                JOIN folders ON shares.folder_id = folders.folder_id
                JOIN users ON shares.owner_id = users.user_id
                WHERE shares.target_user_id = $user_id
            ";

            $resSharedFolder = mysqli_query($conn, $sqlSharedFolder);

            if (mysqli_num_rows($resSharedFolder) > 0) {
                while ($row = mysqli_fetch_assoc($resSharedFolder)) {
            ?>
                    <div class="folder-card bg-blue-50 p-4 rounded-xl shadow-sm hover:shadow-lg transition cursor-pointer border border-blue-200">
                        <div class="flex items-center w-full">
                            <i class="bi bi-folder-symlink-fill text-blue-500 text-2xl mr-2"></i>
                            <span class="text-sm font-medium text-gray-800 truncate w-full">
                                <?php echo $row['name']; ?>
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">Shared by: <?= $row['owner_name'] ?></div>
                    </div>
            <?php
                }
            }
            ?>
        </div>

        <!-- FOLDERS SESION-->
        <section class="mb-8 bg-white p-6 rounded-xl">
            <h2 class="text-xl font-bold text-gray-700 uppercase mb-4 tracking-wider border-b pb-2">THƯ MỤC</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                <?php
                if ($role == 1) {
                    $sql = "SELECT * FROM folders
                                JOIN users ON folders.user_id = users.user_id
                                WHERE folders.is_deleted = 0
                                ORDER BY created_at DESC";
                } else {
                    $sql = "SELECT * FROM folders
                                JOIN users ON folders.user_id = users.user_id
                                WHERE folders.user_id = $user_id
                                AND folders.is_deleted = 0
                                ORDER BY created_at DESC";
                }
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $folder_name = htmlspecialchars($row['name']); ?>
                        <div class="folder-card bg-gray-50 p-4 rounded-xl shadow-sm hover:shadow-lg transition duration-300 cursor-pointer border border-gray-200 flex flex-col items-center text-left hover:bg-yellow-50" title="<?php echo $folder_name; ?>">
                            <div class="flex items-center w-full">
                                <i class="bi bi-folder-fill text-yellow-500 text-2xl mr-2"></i>
                                <span class="text-sm font-medium text-gray-800 truncate w-full"><?php echo $folder_name; ?></span>
                                <!-- dropdown -->
                                <div class="dropdown">
                                    <i class="bi bi-three-dots-vertical cursor-pointer text-gray-400 hover:text-gray-700 text-lg" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                    <ul class="dropdown-menu shadow-lg rounded-xl">
                                        <li>
                                            <a class="dropdown-item flex items-center gap-2" href="share/share.php?folder_id=<?= $row['folder_id'] ?>">
                                                <i class="bi bi-share"></i>Chia sẻ
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item flex items-center gap-2" href="#">
                                                <i class="bi bi-eye"></i> Chi tiết
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item flex items-center gap-2" href="#">
                                                <i class="bi bi-pencil-square"></i> Chỉnh sửa
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item flex items-center gap-2" href="#">
                                                <i class="bi bi-download"></i> Tải xuống
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item flex items-center gap-2 text-danger" href="move_trash.php?folder_id=<?php echo $row['folder_id']; ?>">
                                                <i class="bi bi-trash"></i> Chuyển vào thùng rác
                                            </a>
                                        </li>
                                    </ul>
                                </div>


                            </div>
                        </div>

                <?php   }
                }
                ?>
            </div>
        </section>

        <!-- SHARED FILE SESSION -->
        <h2 class="text-lg font-bold text-gray-700 uppercase mt-10 mb-4 tracking-wider">
            ĐƯỢC CHIA SẺ VỚI TÔI (TỆP)
        </h2>

        <div class="overflow-x-auto">
            <div class="min-w-full">
                <?php
                $sqlSharedFile = "
                    SELECT files.*, users.username AS owner_name
                    FROM shares
                    JOIN files ON shares.file_id = files.file_id
                    JOIN users ON shares.owner_id = users.user_id
                    WHERE shares.target_user_id = $user_id
                ";

                $resSharedFile = mysqli_query($conn, $sqlSharedFile);

                if (mysqli_num_rows($resSharedFile) > 0) {
                    while ($row = mysqli_fetch_assoc($resSharedFile)) {

                        $icons = [
                            'pdf' => 'bi-file-earmark-pdf-fill',
                            'doc' => 'bi-file-earmark-word-fill',
                            'docx' => 'bi-file-earmark-word-fill',
                            'xls' => 'bi-file-earmark-excel-fill',
                            'xlsx' => 'bi-file-earmark-excel-fill',
                            'ppt' => 'bi-file-earmark-ppt-fill',
                            'pptx' => 'bi-file-earmark-ppt-fill',
                            'jpg' => 'bi-file-earmark-image-fill',
                            'png' => 'bi-file-earmark-image-fill',
                            'zip' => 'bi-file-earmark-zip-fill'
                        ];

                        $ext = strtolower(pathinfo($row['name'], PATHINFO_EXTENSION));
                        $icon = $icons[$ext] ?? 'bi-file-earmark-fill';
                ?>
                        <div class="file-row grid grid-cols-12 items-center text-sm border-b border-gray-100 py-3">
                            <div class="col-span-4 lg:col-span-5 flex items-center space-x-3 px-3">
                                <i class="bi <?= $icon ?> text-xl text-blue-500"></i>
                                <span class="font-medium text-gray-800"><?= $row['name'] ?></span>
                            </div>
                            <div class="col-span-3 lg:col-span-2 text-gray-600 px-3">
                                Shared by: <?= $row['owner_name'] ?>
                            </div>
                            <div class="col-span-2 text-gray-600 px-3"><?= $row['upload_date'] ?></div>
                            <div class="col-span-2 text-gray-600 px-3"><?= round($row['size'] / (1024 * 1024), 2) ?> MB</div>

                            <div class="col-span-1 flex space-x-2 justify-end px-3">
                                <i class="bi bi-three-dots-vertical text-lg text-gray-500"></i>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>

        <!-- ALL FILES SECTION -->
        <section class="mt-8 bg-white p-6 rounded-xl">
            <h2 class="text-lg font-bold text-gray-700 uppercase mb-4 tracking-wider">TẤT CẢ TỆP</h2>

            <div>
                <div class="min-w-full">
                    <!-- Table Header -->
                    <div class="grid grid-cols-12 text-xs font-bold text-gray-500 border-b border-gray-200 py-3 uppercase">
                        <div class="col-span-4 lg:col-span-5 px-3">NAME</div>
                        <div class="col-span-3 lg:col-span-2 px-3">OWNERS</div>
                        <div class="col-span-2 px-3">LAST MODIFIED</div>
                        <div class="col-span-2 px-3">FILE SIZE</div>
                        <div class="col-span-1 px-3 text-right"></div> <!-- Links/Options -->
                    </div>

                    <!-- Thêm file bằng php -->

                    <?php
                    if ($role == 1) {
                        $sql = "SELECT * FROM files
                                JOIN users ON files.user_id = users.user_id
                                WHERE files.is_deleted = 0
                                ORDER BY upload_date DESC";
                    } else {
                        $sql = "SELECT * FROM files
                                JOIN users ON files.user_id = users.user_id
                                WHERE files.user_id = $user_id
                                AND files.is_deleted = 0
                                ORDER BY upload_date DESC";
                    }
                    $kq = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($kq) > 0) {
                        while ($row = mysqli_fetch_assoc($kq)) {

                            $icons = [
                                'pdf' => 'bi-file-earmark-pdf-fill',
                                'doc' => 'bi-file-earmark-word-fill',
                                'docx' => 'bi-file-earmark-word-fill',
                                'xls' => 'bi-file-earmark-excel-fill',
                                'xlsx' => 'bi-file-earmark-excel-fill',
                                'ppt' => 'bi-file-earmark-ppt-fill',
                                'pptx' => 'bi-file-earmark-ppt-fill',
                                'jpg' => 'bi-file-earmark-image-fill',
                                'png' => 'bi-file-earmark-image-fill',
                                'zip' => 'bi-file-earmark-zip-fill'
                            ];
                            $ext = strtolower(pathinfo($row['name'], PATHINFO_EXTENSION));
                            $icon = $icons[$ext] ?? 'bi-file-earmark-fill';

                            $firstLetter = mb_substr($row['username'], 0, 1, "UTF-8");

                    ?>
                            <div class="file-row grid grid-cols-12 items-center text-sm border-b border-gray-100 py-3 transition duration-150">
                                <div class="col-span-4 lg:col-span-5 flex items-center space-x-3 px-3">
                                    <i class="bi <?php echo $icon; ?> text-xl"></i>
                                    <span class="font-medium text-gray-800"><?php echo $row['name']; ?></span>
                                </div>
                                <div class="col-span-3 lg:col-span-2 avatar-group">
                                    <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://placehold.co/24x24/dc2626/ffffff?text=<?php echo $firstLetter; ?>" alt="<?php echo "Owner " . $row['name']; ?>">
                                </div>
                                <div class="col-span-2 text-gray-600 px-3"><?php echo $row['upload_date']; ?></div>
                                <div class="col-span-2 text-gray-600 px-3"><?php echo round($row['size'] / (1024 * 1024), 2) . " MB"; ?></div>
                                <div class="col-span-1 flex space-x-2 justify-end text-gray-400 px-3">
                                    <!-- share -->
                                    <a href="./share/share.php?file_id=<?= $row['file_id'] ?>"><i class="bi bi-link-45deg cursor-pointer hover:text-blue-500 text-lg"></i>
                                    </a>
                                    <div class="dropdown">
                                        <i class="bi bi-three-dots-vertical cursor-pointer hover:text-blue-500 text-lg"
                                            data-bs-toggle="dropdown" aria-expanded="false"></i>
                                        <ul class="dropdown-menu shadow-lg rounded-xl">
                                            <li>
                                                <a class="dropdown-item flex items-center gap-2" href="detail_file.php?id=<?php echo $row['file_id']; ?>">
                                                    <i class="bi bi-eye"></i> Chi tiết
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item flex items-center gap-2" href="edit_file.php?id=<?php echo $row['file_id']; ?>">
                                                    <i class="bi bi-pencil-square"></i> Chỉnh sửa
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item flex items-center gap-2" href="download_file.php?id=<?php echo $row['file_id']; ?>">
                                                    <i class="bi bi-download"></i> Tải xuống
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item flex items-center gap-2 text-danger" href="move_trash.php?file_id=<?php echo $row['file_id']; ?>">
                                                    <i class="bi bi-trash"></i> Chuyển vào thùng rác
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </section>
    </div>


</body>

</html>