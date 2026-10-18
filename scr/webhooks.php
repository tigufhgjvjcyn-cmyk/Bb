<?php
// Bật chế độ ghi lỗi
ini_set('log_errors', 1);
ini_set('error_log', 'debug_log.txt');
error_reporting(E_ALL);

// Lấy dữ liệu từ Telegram
$update = json_decode(file_get_contents('php://input'), TRUE);

// Kiểm tra xem dữ liệu có hợp lệ không
if (json_last_error() != JSON_ERROR_NONE) {
    file_put_contents('debug_log.txt', "JSON Decode Error: " . json_last_error_msg() . "\n", FILE_APPEND);
    exit;
}

// Đặt Token bot của bạn
$token = "7901838840:AAF6rDUN-PtSZzNOBXv0WtjD0Deb8MNEOSk";
$apiUrl = "https://api.telegram.org/bot" . $token . "/";

// Kiểm tra nếu có tin nhắn
if (isset($update["message"])) {
    $message = $update["message"];
    $chat_id = $message["chat"]["id"];
    $text = $message["text"];

    // Nếu người dùng gửi /start
    if ($text == "/start") {
        sendMessage($chat_id, "🎲 Chào bạn! Vui lòng nhập /create_key để tạo key.");
    }

    // Nếu người dùng gửi /create_key
    elseif ($text == "/create_key") {
        sendMessage($chat_id, "🤖 Bạn muốn tạo key? Vui lòng nhập tên key.");
    }

    // Kiểm tra nếu người dùng nhập tên key
    elseif (isset($message["text"]) && !empty($message["text"])) {
        $key_name = $message["text"];
        $new_key = generateKey();

        // Lưu key vào tệp trong thư mục khác
        if (saveKey($key_name, $new_key)) {
            sendMessage($chat_id, "✅ Key mới đã được tạo thành công!\n\nTên Key: $key_name\nKey: $new_key\nHãy lưu lại key này để sử dụng công cụ phân tích.");
        } else {
            sendMessage($chat_id, "❌ Lỗi khi tạo key. Vui lòng thử lại sau.");
        }
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

// Tạo key ngẫu nhiên
function generateKey() {
    return bin2hex(random_bytes(8)); // Tạo key ngẫu nhiên dài 16 ký tự
}

// Lưu key vào tệp trong thư mục khác
function saveKey($key_name, $key_value) {
    // Đặt đường dẫn tệp nơi bạn muốn lưu key
    $keys_file = 'keys.json'; // Đổi đường dẫn đến thư mục bạn muốn

    // Kiểm tra và lấy danh sách các key đã có từ tệp keys.json trong thư mục khác
    if (file_exists($keys_file)) {
        $keys = json_decode(file_get_contents($keys_file), true);
    } else {
        $keys = [];
    }

    // Thêm key mới vào danh sách với giá trị "true"
    $keys[$key_value] = "true";

    // Lưu lại tệp trong thư mục khác
    return file_put_contents($keys_file, json_encode($keys, JSON_PRETTY_PRINT));
}
?>
