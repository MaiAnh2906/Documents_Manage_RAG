<?php
include("../config/config.php");
include("auto_clean.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

define('ALLOW_ACCESS', true);
$pageTitle = "My Drive";
include "navbar.php";

$user_id = $_SESSION['login']['user_id'];
$username = $_SESSION['login']['username'];
$role = $_SESSION['login']['role'];

$countSharedFolder = 0;
$countSharedFile = 0;

?>

<div class="main-container">



    <!-- SHARED FOLDER SESSION -->
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
                <h2 class="text-xl font-bold text-gray-700 uppercase mt-10 mb-4 tracking-wider border-b pb-2">THƯ MỤC</h2>
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


    <!-- empty -->
    <?php if ($countSharedFolder == 0 && $countSharedFile == 0) { ?>
        <div class="flex flex-col items-center justify-center h-[70vh] text-center">

            <img src="assets/images/share.jpg" 
                alt="Empty shared files"
                class="w-60 h-60 object-contain mb-6 opacity-90">

            <p class="text-lg font-medium text-gray-600">
                Không có tệp hoặc dự án nào được chia sẻ với bạn
            </p>

        </div>
    <?php } ?>


</div>