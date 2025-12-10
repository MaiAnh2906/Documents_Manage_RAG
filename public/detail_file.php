<?php
include("../config/config.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

$user_id = $_SESSION['login']['user_id'];
$username = $_SESSION['login']['username'];
$role = $_SESSION['login']['role'];

$id = $_GET['id'];
$sql = "SELECT * FROM files 
        JOIN users ON files.user_id = users.user_id
        WHERE files.file_id = $id";
$kq = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($kq);

$name = $row['name'];
$path = $row['path'];
$type = $row['type'];
$size = $row['size'];
$username = $row['username'];
$created_at = $row['upload_date'];

define('ALLOW_ACCESS', true);
$pageTitle = "Chi tiết file";
include "navbar.php";
?>

    <!-- MAIN CONTENT -->
    <div class="main-container">
        <h2 class="text-xl font-bold text-gray-800 uppercase mb-6 tracking-wide">Chi tiết</h2>

        <div class="flex gap-8 items-start">

            <div class="w-1/2 bg-white p-4 rounded-xl shadow border border-gray-200">
                <h2 class="font-semibold text-gray-700 text-lg mb-3"><?php echo $name; ?></h2>

                <?php if (in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) { ?>
                    <img src="<?php echo $path; ?>" 
                        alt="Preview" 
                        class="max-w-full rounded-lg shadow-md border border-gray-300">
                
                <?php } elseif ($type == 'pdf') { ?>
                    <iframe src="<?php echo $path; ?>" 
                            class="w-full h-[500px] rounded-lg shadow-md border border-gray-300">
                    </iframe>
                <?php } ?>
            </div>

            <div class="w-1/2 bg-white p-4 rounded-xl shadow border border-gray-200">
                <h3 class="font-bold text-gray-700 text-lg mb-4">Thông tin về tệp</h3>

                <div class="space-y-2 text-gray-600">
                    <p><span class="font-semibold text-gray-700">Tên file:</span> <?php echo $name; ?></p>
                    <p><span class="font-semibold text-gray-700">Loại file:</span> <?php echo $type; ?></p>
                    <p><span class="font-semibold text-gray-700">Kích thước:</span> <?php echo $size; ?> KB</p>
                    <p><span class="font-semibold text-gray-700">Ngày tạo:</span> <?php echo $created_at; ?></p>
                    <p><span class="font-semibold text-gray-700">Chủ sở hữu:</span> <?php echo $username; ?></p>
                </div>
            </div>

        </div>


    </div>