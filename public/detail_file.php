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

$participants = [];
$sql = "SELECT users.username, shares.permission FROM shares 
        JOIN users ON shares.target_user_id = users.user_id
        WHERE shares.file_id = $id";
$kq = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($kq)){
    $participants[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

    <div class="main-container max-w-7xl mx-auto px-6 py-6">
        <div class="flex items-center justify-between mb-6">
            <?php if(isset($_GET['folder_id'])){  ?>
                <a href="./folder.php?folder_id=<?php echo $_GET['folder_id']; ?>" 
                class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                    ← Quay lại
                </a>
            <?php }else{ ?>
                <a href="./index.php" 
                class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                    ← Quay lại
                </a>
            <?php } ?>
            
            <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-wide">
                Chi tiết file
            </h2>
        </div>

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
                    <p><span class="font-semibold text-gray-700">Người có quyền truy cập:</span>
                        <?php
                        echo $username . ", ";
                        $count = count($participants);
                        $i = 1;

                        foreach ($participants as $value) {
                            if ($i < $count) {
                                echo $value['username'] . ", ";
                            } else {
                                echo $value['username'] . ".";
                            }
                            $i++;
                        }
                        ?>
                    </p>

                </div>
            </div>

        </div>


    </div>
</body>
</html>