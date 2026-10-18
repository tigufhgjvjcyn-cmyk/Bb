<?php
// Bật chế độ ghi lỗi
ini_set('log_errors', 1);
ini_set('error_log', 'debug_log.txt');
error_reporting(E_ALL);

// Kiểm tra nếu người dùng gửi lệnh /create_key
if (isset($_GET['action']) && $_GET['action'] == 'create_key') {
    // Kiểm tra nếu đã có username gửi lệnh tạo key
    if (isset($_GET['username']) && !empty($_GET['username'])) {
        $username = $_GET['username'];

        // Tạo key mới
        $new_key = generateKey();

        // Lưu key vào tệp keys.json
        if (saveKey($new_key)) {
            echo "✅ Key Mới Được Tạo Thành Công ✅\n\nKey: $new_key\nHãy lưu key này để sử dụng công cụ phân tích kết quả.";
            file_put_contents('debug_log.txt', "New key generated for $username: $new_key\n", FILE_APPEND);
        } else {
            echo "❌ Lỗi khi tạo key. Vui lòng thử lại sau ❌";
        }
    } else {
        echo "❌ Vui lòng cung cấp username để tạo key ❌";
    }
} else {
    echo "❌ Không có yêu cầu tạo key ❌";
}

// Tạo key ngẫu nhiên
function generateKey() {
    return bin2hex(random_bytes(8)); // Tạo key ngẫu nhiên dài 16 ký tự
}

// Lưu key vào tệp keys.json
function saveKey($key) {
    // Kiểm tra và lấy danh sách các key đã có
    $keys_file = 'keys.json';
    if (file_exists($keys_file)) {
        $keys = json_decode(file_get_contents($keys_file), true);
    } else {
        $keys = [];
    }

    // Thêm key mới vào danh sách
    $keys[$key] = true;

    // Lưu lại tệp keys.json
    return file_put_contents($keys_file, json_encode($keys, JSON_PRETTY_PRINT));
}
?>
