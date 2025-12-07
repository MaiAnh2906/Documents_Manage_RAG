<?php
include("../config/config.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}
if ($_SESSION['login']['role'] != 1) {
    header("Location: ../public/assets/images/403.php");
    exit();
}

define('ALLOW_ACCESS', true);
$pageTitle = "Quản lý người dùng";
include "navbar.php";

?>
<div class="main-container">
    <h2 class="text-lg font-bold text-gray-700 uppercase mb-4 tracking-wider">QUẢN LÝ NGƯỜI DÙNG</h2>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>STT</th>
                <th>Username</th>
                <th>Email</th>
                <th>Trạng thái</th>
                <th>Role</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <?php
        $stt = 1;
        $sql = "SELECT * FROM users WHERE `role` != 1";
        $kq = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($kq)) {
        ?>
            <tr>
                <td><?php echo $stt++; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td>
                    <?php if($row['status'] == 1){
                        echo "active";
                    }else{
                        echo "blocked";
                    }
                     ?>
                </td>
                <td><?php echo "user"; ?></td>
                <td>
                    <?php if($row['status'] == 1){
                    ?>
                    <a href="?id=<?php echo $row['user_id']; ?>&status=<?php echo $row['status']; ?>" class="btn btn-warning btn-sm">Khóa</a>
                    <?php
                    }else{
                    ?>
                    <a href="?id=<?php echo $row['user_id']; ?>&status=<?php echo $row['status']; ?>" class="btn btn-warning btn-sm">Kích hoạt</a>
                    <?php
                    }
                     ?>
                    
                    <a href="?delete=<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm"
                        onclick="return confirm('Xóa người dùng này?')">Xóa</a>
                </td>
            <?php
        }
            ?>
            </tr>
    </table>

    <?php
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $sql = "DELETE FROM users WHERE user_id = $id";
        mysqli_query($conn, $sql);
    }

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $status = $_GET['status'];
        if($status == 0){
            $sql = "UPDATE users SET status = 1 WHERE user_id = $id";
            mysqli_query($conn, $sql);
        }else{
            $sql = "UPDATE users SET status = 0 WHERE user_id = $id";
            mysqli_query($conn, $sql);
        }

    }
    ?>
</div>