<?php
include("../config/config.php");
include("auto_clean.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

define('ALLOW_ACCESS', true);
$pageTitle = "Drive của tôi";
include "navbar.php";

$user_id = $_SESSION['login']['user_id'];
$username = $_SESSION['login']['username'];
$role = $_SESSION['login']['role'];

$countFolder = 0;
$countFile = 0;

?>

<div class="main-container">
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

    <!-- ALL FILES SECTION -->
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
    <?php if ($countFolder == 0 && $countFile == 0) { ?>
        <div class="flex flex-col items-center justify-center h-[70vh] text-center">

            <img src="assets/images/empty.jpg"
                alt="Empty files"
                class="w-60 h-60 object-contain mb-6 opacity-90">

            <p class="text-lg font-medium text-gray-600">
                Không có tệp hoặc thư mục nào
            </p>

        </div>
    <?php } ?>
</div>