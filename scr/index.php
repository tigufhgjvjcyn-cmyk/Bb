<?php
// Bật chế độ ghi lỗi
ini_set('log_errors', 1);
ini_set('error_log', 'debug_log.txt');
error_reporting(E_ALL);

// Lấy dữ liệu từ Telegram
$update = json_decode(file_get_contents('php://input'), TRUE);

// Kiểm tra nếu có lỗi JSON
if (json_last_error() != JSON_ERROR_NONE) {
    file_put_contents('debug_log.txt', "JSON Decode Error: " . json_last_error_msg() . "\n", FILE_APPEND);
    exit;
}

// Kiểm tra nếu có tin nhắn
if (isset($update["message"])) {
    $message = $update["message"];
    $chat_id = $message["chat"]["id"];
    $text = $message["text"];

    // Nếu người dùng gửi /start
    if ($text == "/start") {
        sendMessage($chat_id, "🎲 Chào bạn! Vui lòng nhập key để sử dụng công cụ phân tích.");
        sendMessage($chat_id, "🔑Vui lòng nhập Key để Tool phân tích kết quả cho bạn🔑");
    }
}

// Hàm gửi tin nhắn
function sendMessage($chat_id, $text) {
    global $apiUrl;
    $url = $apiUrl . "sendMessage?chat_id=" . $chat_id . "&text=" . urlencode($text);
    $response = file_get_contents($url);
    // Ghi lại phản hồi từ Telegram API vào debug_log.txt
    file_put_contents('debug_log.txt', "SendMessage Response: " . $response . "\n", FILE_APPEND);
}
?>
