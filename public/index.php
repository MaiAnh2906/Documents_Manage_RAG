<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Trang chủ</title>
    <style>
        .search {
            border: none;
            margin-left: 10px;
        }

        .search_form {
            border: solid 1px gray;
            border-radius: 10px;
            margin: 5px;
            padding: 3px;
            width: 30em;
        }

        .bi {
            margin-left: 10px;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #387af5;
            color: #fff;
            border-radius: 0 60px 40px 0;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            align-items: center;
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

        .sidebar .nav-link {
            color: #fff;
            /* font-size: 15px; */
            /* margin: 10px 0; */
            align-items: center;
        }


        .dropdown-toggle::after {
            display: none;
        }

        .dropdown-menu {
            min-width: 180px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .dropdown-item:hover {
            background-color: #f1f1f1;
        }

        .btn-custom {
            background: #fff;
            color: #2c70ceff;
            border-radius: 15px;
            padding: 10px 40px;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-custom:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-sm">
        <div class="container-fluid justify-content-between">

            <a class="navbar-brand" href="#">
                <img src="" alt="" width="30" height="24">
            </a> <span>Doogle Drive</span>

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
        <!-- UPLOAD -->
        <div class="dropdown">
            <button class="btn btn-custom dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                + New
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="#">📁 Thư mục mới</a></li>
                <li><a class="dropdown-item" href="#">⬆️ Tải tệp lên</a></li>
                <li><a class="dropdown-item" href="#">📂 Tải thư mục lên</a></li>
            </ul>
        </div>
        <br>
        <!-- NAVIGATION -->
        <nav class="nav flex-column">
            <div class="">
                <ul class="w-full flex flex-col gap-2">

                    <li 
                        class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                        <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                            <a href="#" class="nav-link"><i class="bi bi-cloud"></i> My Drive</a>
                        </button>
                    </li>

                    <li
                        class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                        <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                            <a href="#" class="nav-link"><i class="bi bi-people"></i> Shared With Me</a>
                        </button>
                    </li>

                    <li
                        class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                        <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                            <a href="#" class="nav-link"><i class="bi bi-clock-history"></i> Recents</a>
                        </button>
                    </li>

                    <li
                        class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                        <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                            <a href="#" class="nav-link"><i class="bi bi-trash"></i> Trash</a>
                        </button>
                    </li>

    

                </ul>
            </div>


        </nav>
    </div>





</body>

</html>