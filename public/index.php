<?php
@include '../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <title>Trang chủ</title>
    <style>
        .search{
            border: none;
            margin-left: 10px;
        }
        .search_form{
            border: solid 1px gray;
            border-radius: 10px;
            margin: 5px;
            padding: 3px;
            width: 30em;
        }
        .bi{
            margin-left: 10px;
        }

        .sidebar{
            width: 250px;
            height: 100vh;
            background-color: #387af5;
            color: #fff;
            border-radius: 0 40px 40px 0;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
        }

        .sidebar .btn-upload {
            background: #fff;
            color: #1e73e8;
            font-weight: 500;
            border-radius: 30px;
            padding: 10px 20px;
            width: 100%;
            border: none;
        }

        .sidebar .nav-link{
            color: #fff;
            font-size: 15px;
            margin: 10px 0;
            align-items: center;

        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-sm">
        <div class="container-fluid justify-content-between">
            
            <a class="navbar-brand" href="#">
            <img src="" alt="" width="30" height="24">
            </a>

            <form class="search-bar d-flex align-items-center search_form mx-auto">
            <i class="bi bi-search"></i>
            <input class="search-input search" type="search" placeholder="Search Drive..." aria-label="Search">
            </form>

            <div class="d-flex align-items-center">
                <span class="me-2">Xin chào, Admin</span>
                <img src="https://via.placeholder.com/40" alt="Avatar" class="rounded-circle border" width="40" height="40">
            </div>
        </div>
    </nav>

    <div class="sidebar">
        <button class="btn-upload mb-4">Upload New Files</button>
        <nav class="nav flex-column">
            <a href="#" class="nav-link"><i class="bi bi-cloud"></i> My Drive</a>
            <a href="#" class="nav-link"><i class="bi bi-people"></i> Shared With Me</a>
            <a href="#" class="nav-link"><i class="bi bi-clock-history"></i> Recents</a>
            <a href="#" class="nav-link"><i class="bi bi-trash"></i> Trash</a>
        </nav>
    </div>

</body>
</html>