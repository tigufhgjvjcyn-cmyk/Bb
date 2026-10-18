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
$token = "7893895483:AAFx8gx4KqG0wnwjvL1bpV-qkQDCdDIMXD8";
$apiUrl = "https://api.telegram.org/bot" . $token . "/";

// Kiểm tra nếu có tin nhắn
if (isset($update["message"])) {
    $message = $update["message"];
    $chat_id = $message["chat"]["id"];
    $text = isset($message["text"]) ? $message["text"] : null;
    $photo = isset($message["photo"]) ? $message["photo"] : null;

    // Nếu người dùng gửi /start
    if ($text == "/start") {
        sendMessage($chat_id, "🎲 Chào bạn! Vui lòng nhập key để sử dụng công cụ phân tích.");
        sendMessage($chat_id, "🔑 Vui lòng nhập Key để Tool phân tích kết quả cho bạn 🔑");
    }

    // Kiểm tra nếu người dùng đã nhập key
    elseif (isset($message["text"]) && !empty($message["text"])) {
        $key = $message["text"];
        if (isValidKey($key)) {
            sendMessage($chat_id, "✅ Nhập Key Thành Công ✅");
            sendMessage($chat_id, "Bạn đã có thể sử dụng chức năng phân tích.");
            sendMessage($chat_id, "📸 Vui lòng gửi ảnh để Bot phân tích kết quả tài xỉu.");
        } else {
            sendMessage($chat_id, "❌ Key Sai! Vui lòng thử lại ❌");
        }
    }

    // Nếu người dùng gửi ảnh, thực hiện phân tích
    elseif ($photo) {
        // Lấy ID của ảnh người dùng gửi
        $file_id = $message["photo"][count($message["photo"]) - 1]["file_id"];

        // Lấy thông tin file ảnh từ Telegram API
        $file_info = getFileInfo($file_id);
        if ($file_info) {
            $file_path = $file_info['file_path'];
            $image_url = "https://api.telegram.org/file/bot" . $token . "/" . $file_path;

            // Gửi thông báo "Đang phân tích" trước khi tiến hành phân tích ảnh
            sendMessage($chat_id, "🎲Tool Đang Phân Tích Kết Quả...🎲");

            // Tạm dừng 5 giây để giả lập thời gian phân tích
            sleep(5);

            // Thực hiện phân tích ảnh và dự đoán kết quả
            $result = analyzeImage($image_url); // Hàm phân tích ảnh (sẽ tùy chỉnh theo yêu cầu)

            // Trả kết quả với định dạng theo yêu cầu
            sendMessage($chat_id, "🤖 TOOL ĐÃ PHÂN TÍCH RA KẾT QUẢ 🤖\n-🎲 TRẢ KẾT QUẢ 🎲\n\n-⭕ Kết Quả Dự Đoán : " . $result['outcome'] . " (tỉ lệ ~" . $result['probability'] . "%)\n-〽️ Tool Báo Đánh : " . $result['outcome'] . " ✔️\n-Kết Quả Sẽ Có Tỉ Lệ Không Đúng Đừng Quá Lạm Dụng .");
        } else {
            sendMessage($chat_id, "❌ Không thể tải ảnh, vui lòng thử lại ❌");
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

// Kiểm tra key hợp lệ
function isValidKey($key) {
    // Kiểm tra key từ tệp keys.json
    $keys = json_decode(file_get_contents('keys.json'), true);
    return isset($keys[$key]);
}

// Hàm lấy thông tin về file từ Telegram API
function getFileInfo($file_id) {
    global $apiUrl;
    $url = $apiUrl . "getFile?file_id=" . $file_id;
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    if ($data["ok"]) {
        return $data["result"];
    }
    return null;
}

// Hàm phân tích ảnh (giả lập trong trường hợp này)
function analyzeImage($image_url) {
    // Đây là hàm giả lập phân tích ảnh, có thể sử dụng một mô hình học máy, OCR hoặc API phân tích ảnh khác
    // Trong thực tế, bạn có thể tích hợp một hệ thống phân tích ảnh cụ thể vào đây.
    // Ví dụ: sử dụng API Google Vision, hoặc một API phân tích ảnh tài xỉu nếu có.

    // Dự đoán ngẫu nhiên kết quả tài xỉu (Tài hoặc Xỉu) và xác suất từ 50% đến 99%
    $outcome = rand(0, 1) == 0 ? 'Xỉu' : 'Tài';
    $probability = rand(50, 99);

    // Trả kết quả phân tích
    return [
        'outcome' => $outcome,
        'probability' => $probability
    ];
}
?>
