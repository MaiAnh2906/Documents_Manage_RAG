<?php
include("../config/config.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

define('ALLOW_ACCESS', true);
$pageTitle = "My Drive";
include "navbar.php";

$user_id = $_SESSION['login']['user_id'];
$username = $_SESSION['login']['username'];

// lấy ô name="search" (từ khóa từ URL dạng ?search=abc)
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

?>

<div class="main-container">
    <section class="mb-8 bg-white p-6 rounded-xl">
        <h2 class="text-xl font-bold text-gray-700 uppercase mb-4 tracking-wider border-b pb-2">KẾT QUẢ TÌM KIẾM</h2>

        <?php
        if ($keyword != "") {
            $keywordLike = "%$keyword%";

            $stmtFl = $conn->prepare("SELECT * FROM files WHERE name LIKE ?");
            $stmtFl->bind_param("s", $keywordLike);
            $stmtFl->execute();
            $filesResult = $stmtFl->get_result();

            $stmtFd = $conn->prepare("SELECT * FROM folders WHERE user_id = ? AND name LIKE ?");
            $stmtFd->bind_param("is", $user_id, $keywordLike);
            $stmtFd->execute();
            $foldersResult = $stmtFd->get_result();

            // $sql = "SELECT * FROM files WHERE name LIKE '$keywordLike'";
            // $result = $conn->query($sql);
        ?>

            <!-- hiển thị -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                <?php
                if ($foldersResult->num_rows > 0) {
                    while ($row = $foldersResult->fetch_assoc()) {
                        $folder_name = htmlspecialchars($row['name']); ?>
                        <div class="folder-card bg-gray-50 p-4 rounded-xl shadow-sm hover:shadow-lg transition duration-300 cursor-pointer border border-gray-200 flex flex-col items-center text-left hover:bg-yellow-50" title="<?php echo $folder_name; ?>">
                            <div class="flex items-center w-full">
                                <i class="bi bi-folder-fill text-yellow-500 text-2xl mr-2"></i>
                                <span class="text-sm font-medium text-gray-800 truncate w-full"><?php echo $folder_name; ?></span>
                                <i class="bi bi-three-dots-vertical text-gray-400 hover:text-gray-700 ml-auto"></i>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>
            </div>


        <?php

            if ($filesResult->num_rows > 0) {
                echo "<div class='overflow-x-auto'>";
                echo "<div class='min-w-full'>";
                echo "<div class='grid grid-cols-12 text-xs font-bold text-gray-500 border-b border-gray-200 py-3 uppercase'>";
                echo "<div class='col-span-4 lg:col-span-5 px-3'>NAME</div>";
                echo "<div class='col-span-3 lg:col-span-2 px-3'>OWNERS</div>";
                echo "<div class='col-span-2 px-3'>LAST MODIFIED</div>";
                echo "<div class='col-span-2 px-3'>FILE SIZE</div>";
                echo "<div class='col-span-1 px-3 text-right'></div>";
                echo "</div>";

                while ($row = $filesResult->fetch_assoc()) {
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

                    $firstLetter = mb_substr($username, 0, 1, "UTF-8");

                    echo "<div class='file-row grid grid-cols-12 items-center text-sm border-b border-gray-100 py-3 transition duration-150'>";
                    echo "<div class='col-span-4 lg:col-span-5 flex items-center space-x-3 px-3'>";
                    echo "<i class='bi $icon text-xl'></i>";
                    echo "<a class='font-medium text-gray-800' href='" . $row['path'] . "' download>" . $row['name'] . "</a>";
                    echo "</div>";
                    echo "<div class='col-span-3 lg:col-span-2 avatar-group'>";
                    echo "<img class='inline-block h-6 w-6 rounded-full ring-2 ring-white' src='https://placehold.co/24x24/dc2626/ffffff?text=$firstLetter' alt='Owner " . $row['name'] . "'>";
                    echo "</div>";
                    echo "<div class='col-span-2 text-gray-600 px-3'>" . $row['upload_date'] . "</div>";
                    echo "<div class='col-span-2 text-gray-600 px-3'>" . round($row['size'] / (1024 * 1024), 2) . " MB</div>";
                    echo "<div class='col-span-1 flex space-x-2 justify-end text-gray-400 px-3'>";
                    echo "<i class='bi bi-link-45deg cursor-pointer hover:text-blue-500 text-lg'></i>";
                    echo "<i class='bi bi-three-dots-vertical cursor-pointer hover:text-blue-500 text-lg'></i>";
                    echo "</div>";
                    echo "</div>";
                }
                echo "</div>";
                echo "</div>";
            } else {
                echo "<p class='text-red-600'>Không tìm thấy file nào!</p>";
            }
        } else {
            echo "<p class='text-gray-600'>Không có từ khóa tìm kiếm.</p>";
        }
        ?>
    </section>
</div>