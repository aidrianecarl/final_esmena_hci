<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $visitor_name = $_POST['visitor_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $school_office = $_POST['school_office'] ?? '';
    $purpose_id = $_POST['purpose_id'] ?? ''; // Changed from purpose_of_visit to purpose_id
    $date_of_visit = $_POST['date_of_visit'] ?? date('Y-m-d');
    $time_of_visit = $_POST['time_of_visit'] ?? date('H:i:s');
    $notes = $_POST['notes'] ?? '';
    $user_id = $_POST['user_id'] ?? null;

    // Validation
    $errors = [];
    if (empty($visitor_name)) $errors[] = "Visitor name is required";
    if (empty($address)) $errors[] = "Address is required";
    if (empty($contact_number)) $errors[] = "Contact number is required";
    if (empty($school_office)) $errors[] = "School/Office name is required";
    if (empty($purpose_id)) $errors[] = "Purpose of visit is required";
    if (empty($date_of_visit)) $errors[] = "Date of visit is required";
    if (empty($time_of_visit)) $errors[] = "Time of visit is required";

    // Phone validation
    if (!preg_match('/^(09|\+639)\d{9}$/', str_replace(['-', ' '], '', $contact_number))) {
        $errors[] = "Invalid Philippine mobile number format";
    }

    if (!empty($errors)) {
        throw new Exception(implode(", ", $errors));
    }

    $stmt = $conn->prepare("
        INSERT INTO visitors (visitor_name, address, contact_number, school_office, 
                             purpose_id, date_of_visit, time_of_visit, notes, created_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("ssssiissi", $visitor_name, $address, $contact_number, $school_office, 
                      $purpose_id, $date_of_visit, $time_of_visit, $notes, $user_id);

    if ($stmt->execute()) {
        $visitor_id = $stmt->insert_id;

        $log_stmt = $conn->prepare("
            INSERT INTO visitor_logs (visitor_id, action, timestamp) 
            VALUES (?, ?, NOW())
        ");
        $action = 'created';
        $log_stmt->bind_param("is", $visitor_id, $action);
        $log_stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Visitor added successfully', 'visitor_id' => $visitor_id]);
    } else {
        throw new Exception("Error adding visitor: " . $stmt->error);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
