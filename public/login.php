<?php
session_start();
@include '../config/config.php';

if (isset($_POST['btn_login'])) {
    $email = $_POST['email'] ?? "";
    $password = $_POST['password'] ?? "";
    $pwd = md5($password);

    if ($email == "" || $password == "") {
        if (empty($_POST['email'])) {
            $errorEmail = "Vui lòng nhập địa chỉ email!";
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errorEmail = "*Vui lòng nhập đúng định dạng email!";
        }

        if (empty($_POST['password'])) {
            $errorPassword = "*Vui lòng nhập mật khẩu!";
        } 
    } else {
        $select = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $select);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['login'] = [];

            if ($pwd ==  $row['password']) {
                // Lưu thông tin người dùng vào session
                // session_start();
                // $_SESSION['user_id'] = $row['id'];
                // $_SESSION['username'] = $row['username'];
                // $_SESSION['email'] = $row['email'];
                // $_SESSION['phone_number'] = $row['phone_number'];

                // echo "<script>alert('Đăng nhập thành công!'); window.location.href='dashboard.php';</script>";
                $_SESSION['login'] = $row;
                if($row['role'] == 1){
                    header('Location: admin_index.php');
                }else{
                    header('Location: index.php');
                }
            } else {
                echo "<script>alert('Mật khẩu không đúng!');</script>";
            }
        } else {
            echo "<script>alert('Email không tồn tại!');</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/gd.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
    <link rel="icon" href="../imgs/logo.png">
    <title>Đăng nhập</title>
    <style>
        .error{
            color: red;
            font-size: 13px;
            margin: 5px 0px;
        }
        .error_border{
            border: none;
            border-bottom: 2px solid red;
        }

    </style>
</head>
<body>
    <img src="../imgs/AoH.png" alt="" class="aoh-img">

    <div class="container">
        <a href="../Tuyển sinh/Tuyển sinh.html"><img src="../imgs/logo.png" alt="" width="100px"></a>
        <div class="hvah">HỌC VIỆN ANH HÙNG</div>
        <div class="cttts">CỔNG THÔNG TIN TUYỂN SINH</div>


        <div class="infor-box">
            <form method="post">
                <h2>ĐĂNG NHẬP</h2>

                <!-- Email -->
                <div class="input-box <?php echo (isset($errorEmail)) ? 'error_border' : ''; ?>">
                    <span class="icon"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <label for="">Email</label>
                </div>
                <div class="error"><?php echo (isset($errorEmail)) ? $errorEmail : "" ?></div>

                <!-- Pass -->
                <div class="input-box <?php echo (isset($errorEmail)) ? 'error_border' : ''; ?>">
                    <span class="icon"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" id="password" name="password" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>">
                    <label for="">Mật khẩu </label>
                </div>
                <div class="error"><?php echo (isset($errorPassword)) ? $errorPassword : "" ?></div>

                <!-- Btn -->
                <button type="submit" name="btn_login">Đăng nhập</button>
                
                <hr>
                <p>Chưa có tài khoản? <a href="./register.php">Đăng ký</a></p>
            </form>
        </div>
        <div class="copyright">&copy;Copyright by Phanh dep try and Manh xinh gai 2025</div>
    </div>

    <script src="loglog.js"></script>
</body>
</html>