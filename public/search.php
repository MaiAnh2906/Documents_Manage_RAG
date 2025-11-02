<?php
@include '../config/config.php';

// Lấy từ khóa từ URL (dạng ?search=abc)
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả tìm kiếm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<h3>Kết quả tìm kiếm cho: <span class="text-primary">"<?= htmlspecialchars($keyword) ?>"</span></h3>
<hr>

<?php
if ($keyword == '') {
    echo "<p>Vui lòng nhập từ khóa để tìm kiếm.</p>";
    exit;
}

$sql = "SELECT * FROM files WHERE filename LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%{$keyword}%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<ul class='list-group'>";
    while ($row = $result->fetch_assoc()) {
        echo "<li class='list-group-item d-flex align-items-center'>";
        echo "<i class='bi bi-file-earmark me-2 text-primary'></i> ";
        echo htmlspecialchars($row['filename']);
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Không tìm thấy kết quả nào.</p>";
}
?>

</body>
</html>
