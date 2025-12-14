<?php
include("../config/config.php");
include("auto_clean.php");
include("func/function.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

define('ALLOW_ACCESS', true);
$pageTitle = "Trang chủ";
include "navbar.php";
include "detail_folder.php";


$user_id = $_SESSION['login']['user_id'];
$username = $_SESSION['login']['username'];
$role = $_SESSION['login']['role'];

$countSharedFolder = 0;
$countFolder = 0;
$countSharedFile = 0;
$countFile = 0;
?>
<!-- MAIN CONTENT -->
<div class="main-container">

    <!-- FOLDERS SHARED WITH ME -->
    <?php if ($role == 0) { ?>
        <?php
        $sqlSharedFolder = "
                    SELECT folders.*, users.username AS owner_name
                    FROM shares
                    JOIN folders ON shares.folder_id = folders.folder_id
                    JOIN users ON shares.owner_id = users.user_id
                    WHERE shares.target_user_id = $user_id
                    AND shares.is_deleted = 0;";

        $resSharedFolder = mysqli_query($conn, $sqlSharedFolder);
        $countSharedFolder = mysqli_num_rows($resSharedFolder);

        if (mysqli_num_rows($resSharedFolder) > 0) { ?>
            <section class="mb-8 bg-white p-6 rounded-xl">
                <h2 class="text-xl font-bold text-gray-700 uppercase mt-10 mb-4 tracking-wider border-b pb-2">ĐƯỢC CHIA SẺ VỚI TÔI (THƯ MỤC)</h2>
                <div class="grid gap-4 auto-rows-max" style="grid-template-columns: repeat(auto-fill, minmax(200px, max-content));">

                    <?php while ($row = mysqli_fetch_assoc($resSharedFolder)) { ?>
                        <div class="folder-card bg-blue-50 p-4 rounded-xl shadow-sm hover:shadow-lg transition cursor-pointer border border-blue-200">
                            <div class="flex items-center w-full">
                                <a href="folder.php?folder_id=<?= $row['folder_id'] ?>">
                                    <i class="bi bi-folder-symlink-fill text-blue-500 text-2xl mr-2"></i>
                                    <span class="text-sm font-medium text-gray-800 truncate w-full">
                                        <?php echo $row['name']; ?>
                                    </span>
                                </a>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">Shared by: <?= $row['owner_name'] ?></div>
                        </div>
                    <?php } ?>
                </div>
            </section>
        <?php  } ?>

    <?php } ?>

    <!-- FOLDERS SESION-->
    <?php
    if ($role == 1) {
        $sql = "SELECT folders.*, users.username FROM folders
                                JOIN users ON folders.user_id = users.user_id
                                WHERE folders.is_deleted = 0
                                ORDER BY created_at DESC";
    } else {
        $sql = "SELECT folders.*, users.username FROM folders
                                JOIN users ON folders.user_id = users.user_id
                                WHERE folders.user_id = $user_id
                                AND folders.is_deleted = 0
                                ORDER BY created_at DESC";
    }
    $resFolder = mysqli_query($conn, $sql);
    $countFolder = mysqli_num_rows($resFolder);

    if (mysqli_num_rows($resFolder) > 0) { ?>
        <section class="mb-8 bg-white p-6 rounded-xl">
            <h2 class="text-xl font-bold text-gray-700 uppercase mb-4 tracking-wider border-b pb-2">THƯ MỤC</h2>
            <div class="grid gap-4 auto-rows-max" style="grid-template-columns: repeat(auto-fill, minmax(200px, max-content));">

                <?php while ($row = mysqli_fetch_assoc($resFolder)) {
                    $folder_name = htmlspecialchars($row['name']); ?>
                    <div class="folder-card bg-gray-50 p-4 rounded-xl shadow-sm hover:shadow-lg transition duration-300 cursor-pointer border border-gray-200 flex flex-col items-center text-left hover:bg-yellow-50" title="<?php echo $folder_name; ?>">
                        <!-- day la 1 folder con -->
                        <div class="flex items-center w-full">
                            <a href="folder.php?folder_id=<?= $row['folder_id'] ?>">
                                <i class="bi bi-folder-fill text-yellow-500 text-2xl mr-2"></i>
                                <span class="text-sm font-medium text-gray-800 truncate w-full"><?php echo $folder_name; ?></span>

                                <p class="text-xs mt-1">
                                    <?php if ($row['status'] == 1) { ?>
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs flex items-center">
                                            Hoạt động
                                        </span>
                                    <?php } else { ?>
                                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs flex items-center">
                                            Ngừng hoạt động
                                        </span>
                                    <?php } ?>
                                </p>

                            </a>
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
                                        <a class="dropdown-item" href="#" onclick="openFolderDetail(<?= $row['folder_id'] ?>)">
                                            <i class="bi bi-eye"></i> Chi tiết
                                        </a>

                                    </li>
                                    <li>
                                        <a class="dropdown-item flex items-center gap-2" href="edit_folder.php?folder_id=<?= $row['folder_id'] ?>">
                                            <i class="bi bi-pencil-square"></i> Chỉnh sửa
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
                <?php   } ?>
            </div>
        </section>
    <?php  } ?>


    <!-- SHARED FILE SESSION -->
    <?php if ($role == 0) { ?>
        <?php
        $sqlSharedFile = "
                        SELECT files.*, users.username AS owner_name
                        FROM shares
                        JOIN files ON shares.file_id = files.file_id
                        JOIN users ON shares.owner_id = users.user_id
                        WHERE shares.target_user_id = $user_id
                        AND shares.is_deleted = 0; ";

        $resSharedFile = mysqli_query($conn, $sqlSharedFile);
        $countSharedFile = mysqli_num_rows($resSharedFile);

        if (mysqli_num_rows($resSharedFile) > 0) { ?>
            <section class="mb-8 bg-white p-6 rounded-xl">
                <h2 class="text-lg font-bold text-gray-700 uppercase mt-10 mb-4 tracking-wider">
                    ĐƯỢC CHIA SẺ VỚI TÔI (TỆP)
                </h2>

                <div class="overflow-x-auto">
                    <div class="min-w-full">
                        <?php while ($row = mysqli_fetch_assoc($resSharedFile)) {

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

                                <div class="col-span-1 flex space-x-2 justify-end text-gray-400 px-3">
                                    <div>
                                        <i class="bi bi-three-dots-vertical cursor-pointer hover:text-blue-500 text-lg"
                                            data-bs-toggle="dropdown" aria-expanded="false"></i>
                                        <ul class="dropdown-menu shadow-lg rounded-xl">
                                            <li>
                                                <a class="dropdown-item flex items-center gap-2" href="detail_file.php?id=<?php echo $row['file_id']; ?>">
                                                    <i class="bi bi-eye"></i> Chi tiết
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item flex items-center gap-2" href="download_file.php?id=<?php echo $row['file_id']; ?>">
                                                    <i class="bi bi-download"></i> Tải xuống
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </section>
        <?php } ?>
    <?php } ?>

    <!-- ALL FILES SECTION -->
    <?php
    if ($role == 1) {
        $sql = "SELECT * FROM files
                                JOIN users ON files.user_id = users.user_id
                                WHERE files.is_deleted = 0
                                AND content_id IS NULL
                                ORDER BY upload_date DESC";
    } else {
        $sql = "SELECT * FROM files
                                JOIN users ON files.user_id = users.user_id
                                WHERE files.user_id = $user_id
                                AND files.is_deleted = 0
                                AND content_id IS NULL
                                ORDER BY upload_date DESC";
    }
    $kq = mysqli_query($conn, $sql);
    $countFile = mysqli_num_rows($kq);

    if (mysqli_num_rows($kq) > 0) { ?>
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

                    <?php
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
                    <?php } ?>

                </div>
            </div>
        </section>
    <?php } ?>

    <!-- empty -->
    <?php if ($countFolder == 0 && $countFile == 0 && $countSharedFolder == 0 && $countSharedFile == 0) { ?>
        <div class="flex flex-col items-center justify-center h-[70vh] text-center">

            <img src="assets/images/logo_drive.png" class="w-40 h-40 object-contain mb-6 opacity-90">
            <p class="text-lg font-medium text-gray-600">
                Không có tệp hoặc thư mục nào
            </p>
            <p class="text-gray-500 mt-2">Bắt đầu tải lên tệp hoặc tạo thư mục mới để lưu trữ tệp của bạn.</p>
        </div>
    <?php } ?>

</div>