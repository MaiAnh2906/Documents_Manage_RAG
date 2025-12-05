<?php
include("../config/config.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

define('ALLOW_ACCESS', true);
$pageTitle = "Folder";
include "navbar.php";

?>

<div class="main-container">
    <!-- PHẦN NỘI DUNG RIÊNG CỦA TRANG INDEX -->
    <h1>Trang chủ</h1>
    ...
    <?php
    $folder_id = isset($_GET['id']) ? $_GET['id'] : 0;

    // lấy list folder con
    $q1 = $conn->prepare("SELECT * FROM folders WHERE parent_id = ?");
    $q1->execute([$folder_id]);
    $subFolders = $q1->fetchAll();

    // lấy list file trong folder
    $q2 = $conn->prepare("SELECT * FROM files WHERE folder_id = ?");
    $q2->execute([$folder_id]);
    $files = $q2->fetchAll();
    ?>

    <h2>Nội dung Folder</h2>

    <h3>Folders:</h3>
    <?php foreach ($subFolders as $f): ?>
        <a href="folder.php?id=<?= $f['id'] ?>"><?= $f['name'] ?></a><br>
    <?php endforeach; ?>

    <h3>Files:</h3>
    <?php foreach ($files as $file): ?>
        <a href="<?= $file['path'] ?>" download><?= $file['name'] ?></a><br>
    <?php endforeach; ?>

    ?>
</div>