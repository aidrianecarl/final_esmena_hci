<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $visitor_id = $_POST['visitor_id'] ?? 0;
    $visitor_name = $_POST['visitor_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $school_office = $_POST['school_office'] ?? '';
    $purpose_of_visit = $_POST['purpose_of_visit'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $user_id = $_POST['user_id'] ?? null;

    if (empty($visitor_id)) {
        throw new Exception("Visitor ID is required");
    }

    $errors = [];
    if (empty($visitor_name)) $errors[] = "Visitor name is required";
    if (empty($address)) $errors[] = "Address is required";
    if (empty($contact_number)) $errors[] = "Contact number is required";
    if (!preg_match('/^(09|\+639)\d{9}$/', str_replace(['-', ' '], '', $contact_number))) {
        $errors[] = "Invalid Philippine mobile number format";
    }

    if (!empty($errors)) {
        throw new Exception(implode(", ", $errors));
    }

    $stmt = $conn->prepare("
        UPDATE visitors 
        SET visitor_name = ?, address = ?, contact_number = ?, school_office = ?, 
            purpose_of_visit = ?, notes = ? 
        WHERE visitor_id = ?
    ");

    $stmt->bind_param("ssssssi", $visitor_name, $address, $contact_number, $school_office, 
                      $purpose_of_visit, $notes, $visitor_id);

    if ($stmt->execute()) {
        $log_stmt = $conn->prepare("
            INSERT INTO visitor_logs (visitor_id, action, performed_by) 
            VALUES (?, ?, ?)
        ");
        $action = 'updated';
        $log_stmt->bind_param("isi", $visitor_id, $action, $user_id);
        $log_stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Visitor updated successfully']);
    } else {
        throw new Exception("Error updating visitor: " . $stmt->error);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
