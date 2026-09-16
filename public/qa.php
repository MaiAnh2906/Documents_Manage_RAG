<?php
session_start();
include("../config/config.php");
include("func/function.php");

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['login']['user_id'];
$role = $_SESSION['login']['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    // Kiểm tra xem có đúng là request hỏi AI không
    if ($input !== null && isset($input['question'])) {
        header('Content-Type: application/json');
        
        $user_query = trim($input['question']);
        $space_id = $input['space_id'] ?? null;

        if (empty($user_query)) {
            echo json_encode(['success' => false, 'message' => 'Câu hỏi trống.']);
            exit();
        }

        // Gọi sang Python Flask
        $data = [
            "query" => $user_query,
            "user_id" => (string)$user_id
        ];

        $ch = curl_init("http://127.0.0.1:8000/api/ask");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200 && $response !== false) {
            $python_res = json_decode($response, true);

            if (isset($python_res['success']) && $python_res['success'] == true) {
                $raw_answer = $python_res['answer'];
                
                $raw_answer = preg_replace('/\[Trích từ file:\s*(.*?)\]/i', '', $raw_answer);
                
                $document_ids = [];
                $marker_pattern = '/\[\[RAG_DOCUMENT_IDS:([s0-9,]+)\]\]/i';
                
                if (preg_match($marker_pattern, $raw_answer, $matches)) {
                    $document_ids = array_values(array_filter(array_map('intval', explode(',', $matches[1]))));
                    $raw_answer = preg_replace($marker_pattern, '', $raw_answer);
                }
                
                $ai_answer = trim(preg_replace('/(\s)+/', ' ', $raw_answer)); 
                
                if (!empty($document_ids)) {
                    $ids_sql = implode(',', $document_ids);
                    
                    $file_sql = "SELECT file_id, name, path FROM files
                                 WHERE user_id = $user_id
                                 AND file_id IN ($ids_sql)
                                 AND is_deleted = 0";
                    
                    $file_result = mysqli_query($conn, $file_sql);
                    $citations = [];
                    
                    if ($file_result) {
                        while ($file = mysqli_fetch_assoc($file_result)) {
                            $file_name = htmlspecialchars($file['name'], ENT_QUOTES, 'UTF-8');
                            
                            $file_path = htmlspecialchars($file['path'], ENT_QUOTES, 'UTF-8');
                            
                            $citations[] = '<a href="' . $file_path . '" target="_blank" class="citation-link">'
                                         . '📄 ' . $file_name 
                                         . '</a>';
                        }
                    }
                    
                    if (!empty($citations)) {
                        $ai_answer .= "\n\n<b>Nguồn tham khảo:</b>\n" . implode("\n", $citations);
                    }
                }

                
                $safe_query = mysqli_real_escape_string($conn, $user_query);
                $safe_answer = mysqli_real_escape_string($conn, $ai_answer);

                if (!$space_id) {
                    $title = mb_substr($safe_query, 0, 30) . "..."; 
                    $sql_space = "INSERT INTO spaces (user_id, title) VALUES ($user_id, '$title')";
                    if (!mysqli_query($conn, $sql_space)) {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Lỗi tạo cuộc trò chuyện: ' . mysqli_error($conn)
                        ]);
                        exit();
                    }
                    $space_id = mysqli_insert_id($conn);
                }

                $sql_msg = "INSERT INTO messages (space_id, question, answer) 
                            VALUES ($space_id, '$safe_query', '$safe_answer')";
                mysqli_query($conn, $sql_msg);

                echo json_encode([
                    'success' => true,
                    'answer' => $ai_answer,
                    'space_id' => $space_id
                ]);
                exit(); // QUAN TRỌNG: Dừng PHP ngay tại đây để không in ra giao diện HTML bên dưới
            }
        }
        echo json_encode(['success' => false, 'message' => 'Lỗi kết nối AI.']);
        exit(); 
    }
}

define('ALLOW_ACCESS', true);
$pageTitle = "Hỏi đáp AI";
?>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/qa.css">
    <link rel="stylesheet" href="assets/css/gd.css">
</head>

<body>
    <div class="chat-sidebar">
        <div class="dropdown w-full">
            <button class="new-chat" onclick="window.location.href='qa.php'">
                <i class="bi bi-plus-lg"></i>
                Cuộc trò chuyện mới
            </button>
            <a 
            href="index.php"
            class="back-home">
            ← Quay lại trang chủ
            </a>

            <div class="history">

                <h6>Lịch sử trò chuyện</h6>
                <?php
                $space_sql = "SELECT * FROM spaces WHERE user_id = $user_id ORDER BY space_id DESC";
                $space_result = mysqli_query($conn, $space_sql);

                $current_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

                if ($space_result && mysqli_num_rows($space_result) > 0) {
                    while ($sess = mysqli_fetch_assoc($space_result)) {
                        $active_class = ($sess['space_id'] === $current_id) ? 'active' : '';
                        
                        echo '<a href="qa.php?id='.$sess['space_id'].'" class="history-item '.$active_class.'" style="text-decoration: none; color: inherit; display: block;">';
                        echo '<i class="bi bi-chat-dots"></i> ' . htmlspecialchars($sess['title']);
                        echo '</a>';
                    }
                } else {
                    echo '<div style="padding: 10px; font-size: 0.9em; color: #666;">Chưa có lịch sử.</div>';
                }

                ?>
            </div>
        </div>
    </div>


    <main class="chat-main">
        <div class="chat-header">
            <div>
                <h4>💬 Hỏi đáp AI</h4>
            </div>
        </div>

        <div class="chat-body" id="chatBody">
            <?php
            // Kiểm tra xem có đang mở một không gian (space) nào không
            if (isset($_GET['id'])) {
                $space_id = (int)$_GET['id'];
                
                if ($space_id) {
                    $check_sql = "SELECT space_id
                                FROM spaces
                                WHERE space_id = $space_id
                                AND user_id = $user_id
                                LIMIT 1";

                    $check_result = mysqli_query($conn, $check_sql);

                    if (!$check_result || mysqli_num_rows($check_result) === 0) {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Cuộc trò chuyện không hợp lệ.'
                        ]);
                        exit();
                    }
                }

                $msg_sql = "SELECT * FROM messages WHERE space_id = $space_id ORDER BY message_id ASC";
                $msg_result = mysqli_query($conn, $msg_sql);
                
                if ($msg_result && mysqli_num_rows($msg_result) > 0) {
                    while ($msg = mysqli_fetch_assoc($msg_result)) {
                        echo '
                        <div class="message user">
                            <div class="avatar">👤</div>
                            <div class="bubble">'. nl2br(htmlspecialchars($msg['question'])) .'</div>
                        </div>';

                        echo '
                        <div class="message ai">
                            <div class="avatar">🤖</div>
                            <div class="bubble">'. nl2br(htmlspecialchars($msg['answer'])) .'</div>
                        </div>';
                    }
                }
            } else {
                echo '
                <div class="message ai">
                    <div class="avatar">🤖</div>
                    <div class="bubble">Xin chào 👋<br>Tôi có thể trả lời câu hỏi dựa trên tài liệu bạn đã tải lên.</div>
                </div>';
            }
            ?>
        </div>

        <div class="chat-input">
            <textarea
                id="question"
                placeholder="Hỏi AI về tài liệu..."
            ></textarea>
            <button id="sendBtn">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </main>

</body>

<script src="assets/js/qa.js"></script>