<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            z-index: 1000;
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

        /* Main conten */
        .main-container {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            background-color: #f4f7f9;
        }

        .file-row:hover {
            background-color: #f0f8ff;
        }

        .highlighted-row {
            background-color: #e3f2fd;
        }

        .avatar-group {
            display: flex;
            margin-left: 8px;
        }

        .avatar-group>img {
            border: 2px solid white;
            margin-left: -8px;
            transition: transform 0.2s ease-in-out;
        }

        .avatar-group>img:hover {
            transform: translateY(-2px);
            z-index: 1;
        }
    </style>
</head>

<body>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const currentPage = window.location.pathname.split("/").pop();
            const links = document.querySelectorAll(".sidebar .nav-link");

            links.forEach(link => {
                const linkPage = link.getAttribute("href").split("/").pop();
                if (linkPage === currentPage) {
                    const button = link.closest("button");
                    if (button) {
                        button.style.backgroundColor = '#2c70ce';
                        button.style.color = 'white';
                        button.classList.add("shadow-inner", "rounded-lg");
                    }
                    link.classList.add("text-white");
                }
            });
        });
    </script>

    <!-- TOP NAV BAR -->
    <nav class="navbar navbar-expand-sm">
        <div class="container-fluid justify-content-between">
            <!-- Logo -->
            <a class="navbar-brand" href="#">
                <img src="" alt="" width="30" height="24">
            </a> <span>Doogle Drive</span>
            <!-- Search -->
            <form method="get" action="search.php"
                class="relative flex items-center mx-auto bg-white border border-gray-300 rounded-full px-4 py-2 shadow-sm focus-within:ring-2 focus-within:ring-[#387af5] transition-all duration-200 w-[30em]">
                <i class="bi bi-search text-gray-500 text-lg mr-2"></i>
                <input
                    class="flex-1 bg-transparent border-none outline-none text-gray-700 placeholder-gray-400"
                    type="search"
                    placeholder="Search Drive..."
                    aria-label="Search"
                    name="search" required>
            </form>

            <div class="d-flex align-items-center">
                <span class="me-2">Xin chào, Admin</span>
                <img src="https://via.placeholder.com/40" alt="Avatar" class="rounded-circle border" width="40" height="40">
            </div>
        </div>
    </nav>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <!-- Upload -->
        <div class="dropdown w-full">
            <button class="btn btn-custom dropdown-toggle w-full" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-plus-lg me-2"></i> New
            </button>
            <ul class="dropdown-menu shadow-xl" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item flex items-center" href="#"><i class="bi bi-folder me-2 text-yellow-600"></i> Thư mục mới</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item flex items-center" href="#"><i class="bi bi-file-earmark-arrow-up me-2 text-gray-500"></i> Tải tệp lên</a></li>
                <li><a class="dropdown-item flex items-center" href="#"><i class="bi bi-folder-fill me-2 text-gray-500"></i> Tải thư mục lên</a></li>
            </ul>
        </div>
        <br>
        <!-- Nav -->
        <nav class="nav flex-column">
            <ul class="w-full flex flex-col gap-2">
                <li
                    class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                    <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                        <a href="admin_index.php" class="nav-link"><i class="bi bi-house"></i> Trang chủ</a>
                    </button>
                </li>

                <li
                    class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                    <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                        <a href="drive.php" class="nav-link"><i class="bi bi-cloud"></i> Drive của tôi</a>
                    </button>
                </li>

                <li
                    class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                    <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                        <a href="#" class="nav-link"><i class="bi bi-people"></i> Được chia sẻ với tôi</a>
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

                <li
                    class="flex-center cursor-pointer p-16-semibold w-full whitespace-nowrap">
                    <button class="p-16-semibold flex size-full gap-4 p-2 group font-semibold rounded-lg hover:bg-blue-100 hover:shadow-inner focus:bg-[#2c70ceff] focus:text-white text-gray-700 transition-all ease-linear">
                        <a href="#" class="nav-link"><i class="bi bi-database"></i> Bộ nhớ</a>
                    </button>
                </li>

            </ul>

        </nav>
    </div>


    <!-- MAIN CONTENT -->
    <div class="main-container">
        
        <!-- ALL FILES SECTION -->
        <section class="mt-8 bg-white p-6 rounded-xl shadow-lg">
            <h2 class="text-lg font-bold text-gray-700 uppercase mb-4 tracking-wider">ALL FILES</h2>
            
            <div class="overflow-x-auto">
                <div class="min-w-full">
                    <!-- Table Header -->
                    <div class="grid grid-cols-12 text-xs font-bold text-gray-500 border-b border-gray-200 py-3 uppercase">
                        <div class="col-span-4 lg:col-span-5 px-3">NAME</div>
                        <div class="col-span-3 lg:col-span-2 px-3">OWNERS</div>
                        <div class="col-span-2 px-3">LAST MODIFIED</div>
                        <div class="col-span-2 px-3">FILE SIZE</div>
                        <div class="col-span-1 px-3 text-right"></div> <!-- Links/Options -->
                    </div>

                    <!-- File Rows (Demo Data) -->
                    
                    <!-- Row 1: Weekly Report Docs (Doc) -->
                    <div class="file-row grid grid-cols-12 items-center text-sm border-b border-gray-100 py-3 transition duration-150">
                        <div class="col-span-4 lg:col-span-5 flex items-center space-x-3 px-3">
                            <i class="bi bi-file-earmark-word-fill text-blue-500 text-xl"></i>
                            <span class="font-medium text-gray-800">Weekly Report Docs</span>
                        </div>
                        <div class="col-span-3 lg:col-span-2 avatar-group">
                            <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://placehold.co/24x24/1d4ed8/ffffff?text=A" alt="Owner A">
                            <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://placehold.co/24x24/dc2626/ffffff?text=B" alt="Owner B">
                        </div>
                        <div class="col-span-2 text-gray-600 px-3">Sep 9, 2019 - 4:30 AM</div>
                        <div class="col-span-2 text-gray-600 px-3">20 MB</div>
                        <div class="col-span-1 flex space-x-2 justify-end text-gray-400 px-3">
                            <i class="bi bi-link-45deg cursor-pointer hover:text-blue-500 text-lg"></i>
                            <i class="bi bi-three-dots-vertical cursor-pointer hover:text-blue-500 text-lg"></i>
                        </div>
                    </div>

                    <!-- Row 2: Design Checklist.xlsx (Excel) -->
                    <div class="file-row grid grid-cols-12 items-center text-sm border-b border-gray-100 py-3 transition duration-150">
                        <div class="col-span-4 lg:col-span-5 flex items-center space-x-3 px-3">
                            <i class="bi bi-file-earmark-excel-fill text-green-500 text-xl"></i>
                            <span class="font-medium text-gray-800">Design Checklist.xlsx</span>
                        </div>
                        <div class="col-span-3 lg:col-span-2 avatar-group">
                            <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://placehold.co/24x24/dc2626/ffffff?text=B" alt="Owner B">
                        </div>
                        <div class="col-span-2 text-gray-600 px-3">Sep 9, 2019 - 4:30 AM</div>
                        <div class="col-span-2 text-gray-600 px-3">20 MB</div>
                        <div class="col-span-1 flex space-x-2 justify-end text-gray-400 px-3">
                            <i class="bi bi-link-45deg cursor-pointer hover:text-blue-500 text-lg"></i>
                            <i class="bi bi-three-dots-vertical cursor-pointer hover:text-blue-500 text-lg"></i>
                        </div>
                    </div>

                    <!-- Row 3: Weekly reports.pdf (PDF) -->
                    <div class="file-row grid grid-cols-12 items-center text-sm border-b border-gray-100 py-3 transition duration-150">
                        <div class="col-span-4 lg:col-span-5 flex items-center space-x-3 px-3">
                            <i class="bi bi-file-earmark-pdf-fill text-red-500 text-xl"></i>
                            <span class="font-medium text-gray-800">Weekly reports.pdf</span>
                        </div>
                        <div class="col-span-3 lg:col-span-2 avatar-group">
                            <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://placehold.co/24x24/dc2626/ffffff?text=B" alt="Owner B">
                        </div>
                        <div class="col-span-2 text-gray-600 px-3">Sep 9, 2019 - 4:30 AM</div>
                        <div class="col-span-2 text-gray-600 px-3">20 MB</div>
                        <div class="col-span-1 flex space-x-2 justify-end text-gray-400 px-3">
                            <i class="bi bi-link-45deg cursor-pointer hover:text-blue-500 text-lg"></i>
                            <i class="bi bi-three-dots-vertical cursor-pointer hover:text-blue-500 text-lg"></i>
                        </div>
                    </div>

                    <!-- Row 4: Wedding Planner List.Doc (Doc) -->
                    <div class="file-row grid grid-cols-12 items-center text-sm border-b border-gray-100 py-3 transition duration-150">
                        <div class="col-span-4 lg:col-span-5 flex items-center space-x-3 px-3">
                            <i class="bi bi-file-earmark-word-fill text-blue-500 text-xl"></i>
                            <span class="font-medium text-gray-800">Wedding Planner List.Doc</span>
                        </div>
                        <div class="col-span-3 lg:col-span-2 avatar-group">
                            <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://placehold.co/24x24/dc2626/ffffff?text=B" alt="Owner B">
                        </div>
                        <div class="col-span-2 text-gray-600 px-3">Sep 9, 2019 - 4:30 AM</div>
                        <div class="col-span-2 text-gray-600 px-3">20 MB</div>
                        <div class="col-span-1 flex space-x-2 justify-end text-gray-400 px-3">
                            <i class="bi bi-link-45deg cursor-pointer hover:text-blue-500 text-lg"></i>
                            <i class="bi bi-three-dots-vertical cursor-pointer hover:text-blue-500 text-lg"></i>
                        </div>
                    </div>

                    <!-- Row 5: Team JB Picture.jpg (Image) - Highlighted Row -->
                    <div id="team-jb-row" class="file-row grid grid-cols-12 items-center text-sm border-b border-gray-100 py-3 transition duration-150 cursor-pointer">
                        <div class="col-span-4 lg:col-span-5 flex items-center space-x-3 px-3">
                            <i class="bi bi-image-fill text-yellow-600 text-xl"></i>
                            <span class="font-medium text-gray-800">Team JB Picture.jpg</span>
                        </div>
                        <div class="col-span-3 lg:col-span-2 avatar-group">
                            <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://placehold.co/24x24/dc2626/ffffff?text=B" alt="Owner B">
                        </div>
                        <div class="col-span-2 text-gray-600 px-3">Sep 9, 2019 - 4:30 AM</div>
                        <div class="col-span-2 text-gray-600 px-3">20 MB</div>
                        <div class="col-span-1 flex space-x-2 justify-end text-gray-400 px-3">
                            <i class="bi bi-link-45deg cursor-pointer hover:text-blue-500 text-lg"></i>
                            <i class="bi bi-three-dots-vertical cursor-pointer hover:text-blue-500 text-lg"></i>
                        </div>
                    </div>
                    
                    <!-- Row 6: Team Bert Picture.jpg (Image) -->
                    <div class="file-row grid grid-cols-12 items-center text-sm py-3 transition duration-150">
                        <div class="col-span-4 lg:col-span-5 flex items-center space-x-3 px-3">
                            <i class="bi bi-image-fill text-yellow-600 text-xl"></i>
                            <span class="font-medium text-gray-800">Team Bert Picture.jpg</span>
                        </div>
                        <div class="col-span-3 lg:col-span-2 avatar-group">
                            <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white" src="https://placehold.co/24x24/dc2626/ffffff?text=B" alt="Owner B">
                        </div>
                        <div class="col-span-2 text-gray-600 px-3">Sep 9, 2019 - 4:30 AM</div>
                        <div class="col-span-2 text-gray-600 px-3">20 MB</div>
                        <div class="col-span-1 flex space-x-2 justify-end text-gray-400 px-3">
                            <i class="bi bi-link-45deg cursor-pointer hover:text-blue-500 text-lg"></i>
                            <i class="bi bi-three-dots-vertical cursor-pointer hover:text-blue-500 text-lg"></i>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        
        <!-- Thêm file bằng php -->

    </div>


</body>

</html>