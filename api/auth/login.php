<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        throw new Exception("Username and password are required");
    }

    $stmt = $conn->prepare("SELECT user_id, username, email, role, status FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Invalid username or password");
    }

    $user = $result->fetch_assoc();

    if ($user['status'] !== 'active') {
        throw new Exception("Your account is inactive");
    }

    $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
    $update_stmt->bind_param("i", $user['user_id']);
    $update_stmt->execute();

    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $activity_stmt = $conn->prepare("INSERT INTO user_activity_logs (user_id, action, ip_address) VALUES (?, ?, ?)");
    $action = 'login';
    $activity_stmt->bind_param("iss", $user['user_id'], $action, $ip_address);
    $activity_stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Login successful', 'user' => $user]);

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
