<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitor_name = $_POST['visitor_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $school_office = $_POST['school_office'] ?? '';
    $purpose_of_visit = $_POST['purpose_of_visit'] ?? '';
    $date_of_visit = $_POST['date_of_visit'] ?? '';
    $time_of_visit = $_POST['time_of_visit'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $user_id = $_POST['user_id'] ?? null;

    // Validation
    $errors = [];

    if (empty($visitor_name)) $errors[] = "Full name is required";
    if (empty($address)) $errors[] = "Address is required";
    if (empty($contact_number)) $errors[] = "Contact number is required";
    if (empty($school_office)) $errors[] = "School/Office name is required";
    if (empty($purpose_of_visit)) $errors[] = "Purpose of visit is required";
    if (empty($date_of_visit)) $errors[] = "Date of visit is required";
    if (empty($time_of_visit)) $errors[] = "Time of visit is required";

    // Phone validation
    if (!preg_match('/^(09|\+639)\d{9}$/', $contact_number)) {
        $errors[] = "Invalid Philippine mobile number format";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO visitors (visitor_name, address, contact_number, school_office, purpose_of_visit, date_of_visit, time_of_visit, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssssssi", $visitor_name, $address, $contact_number, $school_office, $purpose_of_visit, $date_of_visit, $time_of_visit, $notes, $user_id);

        if ($stmt->execute()) {
            header("Location: index.php?user_id=$user_id&added=1");
            exit();
        } else {
            die("Error inserting record: " . $stmt->error);
        }
    } else {
        header("Location: index.php?user_id=$user_id&error=" . urlencode(implode(", ", $errors)));
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
?>
