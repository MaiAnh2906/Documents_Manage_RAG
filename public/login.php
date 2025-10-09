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
</head>
<body>
    <img src="../imgs/AoH.png" alt="" class="aoh-img">

    <div class="container">
        <a href="../Tuyển sinh/Tuyển sinh.html"><img src="../imgs/logo.png" alt="" width="100px"></a>
        <div class="hvah">HỌC VIỆN ANH HÙNG</div>
        <div class="cttts">CỔNG THÔNG TIN TUYỂN SINH</div>


        <div class="infor-box">
            <form onsubmit="login()">
                <h2>ĐĂNG NHẬP</h2>

                <div class="input-box">
                    <span class="icon"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" id="email" required>
                    <label for="">Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" id="password" required>
                    <label for="">Mật khẩu </label>
                </div>
                <button type="submit"><a href="">Đăng nhập</a></button>
                
                <hr>
                <p>Chưa có tài khoản? <a href="./register.php">Đăng ký</a></p>
            </form>
        </div>
        <div class="copyright">&copy;Copyright by Phanh dep try and Manh xinh gai 2025</div>
    </div>

    <script src="loglog.js"></script>
</body>
</html>