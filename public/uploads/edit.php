<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Cập nhật ảnh</title>
  <style>
    body { font-family: Arial; margin: 40px; }
    img { width: 200px; height: auto; border-radius: 8px; display:block; margin-bottom:10px; }
    .wrap { max-width: 520px; }
    .note { background: #fffbe6; border: 1px solid #ffe58f; padding: 10px; border-radius: 6px; margin-bottom: 16px; }
  </style>
  </head>
<body>
  <div class="wrap">
    <h2>Cập nhật ảnh</h2>
    <a href="index.php">⬅ Quay lại danh sách</a>
    <hr>
    <div class="note">
      Đã xóa toàn bộ PHP. Sinh viên tự xử lý:
      <ul>
        <li>Lấy id từ query string, truy vấn ảnh theo id.</li>
        <li>Đổ dữ liệu vào form; nếu upload ảnh mới thì thay file cũ, cập nhật CSDL.</li>
      </ul>
    </div>
    <form action="" method="post" enctype="multipart/form-data">
      <label>Tiêu đề:</label><br>
      <input type="text" name="title" value="" required style="width:100%; padding:6px" placeholder="Nhập tiêu đề..."><br><br>

      <label>Ảnh hiện tại:</label><br>
      <img src="uploads/example.jpg" alt="(Ảnh hiện tại - bản mẫu)">

      <label>Chọn ảnh mới (nếu muốn thay):</label><br>
      <input type="file" name="image" accept="image/*"><br><br>

      <button type="submit" name="update">Lưu thay đổi</button>
    </form>
  </div>
</body>
</html>
