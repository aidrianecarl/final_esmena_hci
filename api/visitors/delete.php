<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $visitor_id = $_POST['visitor_id'] ?? 0;
    $user_id = $_POST['user_id'] ?? null;

    if (empty($visitor_id)) {
        throw new Exception("Visitor ID is required");
    }

    $check_stmt = $conn->prepare("SELECT visitor_id FROM visitors WHERE visitor_id = ?");
    $check_stmt->bind_param("i", $visitor_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        throw new Exception("Visitor not found");
    }

    $stmt = $conn->prepare("DELETE FROM visitors WHERE visitor_id = ?");
    $stmt->bind_param("i", $visitor_id);

    if ($stmt->execute()) {
        $log_stmt = $conn->prepare("
            INSERT INTO visitor_logs (visitor_id, action, timestamp) 
            VALUES (?, ?, NOW())
        ");
        $action = 'deleted';
        $log_stmt->bind_param("is", $visitor_id, $action);
        $log_stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Visitor deleted successfully']);
    } else {
        throw new Exception("Error deleting visitor: " . $stmt->error);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
