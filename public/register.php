<?php
@include '../config/config.php';

if(isset($_POST['sbt_register'])){
    $name = $_POST['name'] ?? "";
    $password = $_POST['password'] ?? "";
    $email = $_POST['email'] ?? "";
    $phone_number = $_POST['phone_number'] ?? "";
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);


    if($name == "" || $password == "" || $email == "" || $phone_number == ""){
        if(empty($_POST['name'])){
            $errorName = "*Vui lòng nhập họ và tên!";
        }

        if(empty($_POST['password'])){
            $errorPassword = "*Vui lòng nhập mật khẩu!";
        }elseif(strlen($password) < 8){
            $errorPassword = "*Vui lòng nhập lớn hơn 8 kí tự!";
        }

        if(empty($_POST['email'])){
            $errorEmail = "*Vui lòng nhập địa chỉ email!";
        }
        elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $errorEmail = "*Vui lòng nhập đúng định dạng email!";
        }
        
        if(empty($_POST['phone_number'])){
            $errorPhone = "*Vui lòng nhập số điện thoại!";
        }
        elseif(strlen($_POST['phone_number']) != 10 || !is_numeric($_POST['phone_number']) || $_POST['phone_number'][0] != 0){
            $errorPhone = "*Vui lòng nhập đúng định dạng số điện thoại!";
        }
        
    }else{
        $select = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $select);
        if(mysqli_num_rows($result) > 0){
            echo "<script>alert('Email đã tồn tại!');</script>";
        }else{
            $insert = "INSERT INTO users (username, password, email, phone_number) VALUES('$name', '$passwordHash', '$email', '$phone_number')";
            mysqli_query($conn, $insert);
            echo "<script>alert('Đăng kí thành công!');</script>";
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
    <title>Đăng ký</title>
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
        <a href="../Tuyển sinh/Tuyển sinh.html"><img src="../imgs/logo.png" alt="" width="80px"></a>
        <div class="hvah">HỌC VIỆN ANH HÙNG</div>
        <div class="cttts">CỔNG THÔNG TIN TUYỂN SINH</div>


        <div class="infor-box">
            <form method="post">
                <h2>ĐĂNG KÝ</h2>
                <div class="input-box <?php echo (isset($errorName)) ? 'error_border' : '';?>">
                    <span class="icon"><i class="fa-solid fa-user"></i></span>
                    <input type="text" id="name" name="name" value="<?php if(isset($_POST['name'])) {echo $_POST['name'];} ?>" >
                    <label for="">Họ và tên</label>
                </div>
                <div class="error"><?php echo (isset($errorName)) ? $errorName : "" ?></div>

                <div class="input-box <?php echo (isset($errorPassword)) ? 'error_border' : '';?>">
                    <span class="icon"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" id="password" name="password" value="<?php if(isset($_POST['password'])) {echo $_POST['password'];} ?>">
                    <label for="">Mật khẩu </label>
                </div>
                <div class="error"><?php echo (isset($errorPassword)) ? $errorPassword : "" ?></div>

                <div class="input-box <?php echo (isset($errorEmail)) ? 'error_border' : '';?>">
                    <span class="icon"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" id="email" name="email" value="<?php if(isset($_POST['email'])) {echo $_POST['email'];} ?>">
                    <label for="">Email</label>
                </div>
                <div class="error"><?php echo (isset($errorEmail)) ? $errorEmail : "" ?></div>
                
                <div class="input-box <?php echo (isset($errorPhone)) ? 'error_border' : '';?>">
                    <span class="icon"><i class="fa-solid fa-phone"></i></span>
                    <input type="tel" id="phone" name="phone_number" value="<?php if(isset($_POST['phone_number'])) {echo $_POST['phone_number'];} ?>">
                    <label for="">Số điện thoại</label>
                </div>
                <div class="error"><?php echo (isset($errorPhone)) ? $errorPhone : "" ?></div>

                <button type="submit" name="sbt_register">Đăng ký</button>
                
                <hr>
                <p>Đã có tài khoản? <a href="./login.php">Đăng nhập</a></p>
            </form>
        </div>
        <div class="copyright">&copy;Copyright by Phanh dep try and Manh xinh gai 2025</div>
    </div>


</body>
</html>