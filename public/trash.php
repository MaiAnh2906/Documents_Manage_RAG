<?php

use function PHPSTORM_META\type;

include("../config/config.php");
session_start();
if(!isset($_SESSION['login'])){
    header("Location: login.php");
}
$role = $_SESSION['login']['role'];
$user_id = $_SESSION['login']['user_id'];

define('ALLOW_ACCESS', true);
$pageTitle = "Thùng rác";
include "navbar.php";

?>

    <div class="main-container">
    <h1 class="text-xl font-bold text-gray-700 uppercase mb-4  pb-2">Thùng rác</h1>
        <div >

                <div class="min-w-full">
                    <!-- Table Header -->
                    <div class="grid grid-cols-12 text-xs font-bold text-gray-500 border-b border-gray-200 py-3 uppercase">
                        <div class="col-span-4 lg:col-span-5 px-3">NAME</div>
                        <div class="col-span-3 lg:col-span-2 px-3">OWNERS</div>
                        <div class="col-span-2 px-3">DELETED AT</div>
                        <div class="col-span-2 px-3">EXPIRY DATE</div>
                        <div class="col-span-1 px-3 text-right"></div> <!-- Links/Options -->
                    </div>

                    <!-- Thêm file bằng php -->

                    <?php
                    if ($role == 1) {
                        $sql = "SELECT recycle_bin.*,
                                        files.name AS file_name,
                                        folders.name AS folder_name,
                                        users.username FROM recycle_bin
                                LEFT JOIN users ON recycle_bin.user_id = users.user_id
                                LEFT JOIN files ON recycle_bin.file_id = files.file_id
                                LEFT JOIN folders ON recycle_bin.folder_id = folders.folder_id
                                ";
                    } else {
                        $sql = "SELECT recycle_bin.*,
                                        files.name AS file_name,
                                        folders.name AS folder_name,
                                        users.username FROM recycle_bin
                                LEFT JOIN files ON recycle_bin.file_id = files.file_id
                                LEFT JOIN users ON recycle_bin.user_id = users.user_id
                                LEFT JOIN folders ON recycle_bin.folder_id = folders.folder_id
                                WHERE recycle_bin.user_id = $user_id";
                    }
                    $kq = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($kq) > 0) {
                        while ($row = mysqli_fetch_assoc($kq)) {
                            $isFile = !empty($row['file_id']);
                            $isFolder = !empty($row['folder_id']);
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

                            if ($isFile) {
                                $name = $row['file_name'];  
                                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                $icon = $icons[$ext] ?? 'bi-file-earmark-fill';
                            } elseif ($isFolder) {
                                $name = $row['folder_name']; 
                                $icon = 'bi-folder-fill';   
                            }

                            $firstLetter = mb_substr($row['username'], 0, 1, "UTF-8");

                    ?>
                            <div class="file-row grid grid-cols-12 items-center text-sm border-b border-gray-100 py-3 transition duration-150">
                                <div class="col-span-4 lg:col-span-5 flex items-center space-x-3 px-3">
                                    <i class="bi <?php echo $icon; ?> text-xl"></i>
                                    <span class="font-medium text-gray-800"><?php echo $name; ?></span>
                                </div>

                                <div class="col-span-3 lg:col-span-2 avatar-group">
                                    <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://placehold.co/24x24/dc2626/ffffff?text=<?php echo $firstLetter; ?>" alt="<?php echo "Owner " . $name; ?>">
                                </div>

                                <div class="col-span-2 text-gray-600 px-3"><?php echo $row['deleted_at']; ?></div>

                                <div class="col-span-2 text-gray-600 px-3"><?php echo $row['expiry_date']; ?></div>

                                <div class="col-span-1 flex space-x-2 justify-end text-gray-400 px-3">
                                    <div class="dropdown">
                                        <i class="bi bi-three-dots-vertical cursor-pointer hover:text-blue-500 text-lg"
                                            data-bs-toggle="dropdown" aria-expanded="false"></i>
                                        <ul class="dropdown-menu shadow-lg rounded-xl">
                                            <li>
                                                <a class="dropdown-item flex items-center gap-2" href="restore.php?id=<?php echo $row['id']; ?>">
                                                    <i class="bi bi-eye"></i> Khôi phục
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item flex items-center gap-2 text-danger" href="delete.php?id=<?php echo $row['id']; ?>">
                                                    <i class="bi bi-trash"></i> Xóa vĩnh viễn
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
        
    </div>
