<?php
require_once __DIR__ . '/../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Set page variables for includes
$page_title = 'Dashboard';
$page_icon = 'home';
$breadcrumbs = [
    ['name' => 'Home', 'url' => 'index.php', 'active' => true]
];

$today = date('Y-m-d');

// Always get inputs first
$search = $_GET['search'] ?? '';
$purpose = $_GET['purpose'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// If no filters at all → show today's visitors by default
if (empty($_GET)) {
    $date_from = $today;
    $date_to = $today;
}


// Build query
$query = "SELECT v.visitor_id, v.visitor_name, v.contact_number, v.address, v.school_office, 
          p.purpose_name, v.date_of_visit, v.time_of_visit, v.notes, v.created_at 
          FROM visitors v
          LEFT JOIN visit_purposes p ON v.purpose_id = p.id
          WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (v.visitor_name LIKE ? OR v.contact_number LIKE ? OR v.school_office LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

if (!empty($purpose)) {
    $query .= " AND v.purpose_id = ?"; // Changed filter to use purpose_id
    $params[] = $purpose;
    $types .= 'i';
}

if (!empty($date_from)) {
    $query .= " AND v.date_of_visit >= ?";
    $params[] = $date_from;
    $types .= 's';
}

if (!empty($date_to)) {
    $query .= " AND v.date_of_visit <= ?";
    $params[] = $date_to;
    $types .= 's';
}

$query .= " ORDER BY v.date_of_visit DESC, v.time_of_visit DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$visitors = $stmt->get_result();

$today = date('Y-m-d');
$stats_stmt = $conn->prepare("
    SELECT p.purpose_name, COUNT(*) as count 
    FROM visitors v
    LEFT JOIN visit_purposes p ON v.purpose_id = p.id
    WHERE v.date_of_visit = ? 
    GROUP BY v.purpose_id, p.purpose_name
");
$stats_stmt->bind_param("s", $today);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();

$stats = ['total' => 0, 'exam' => 0, 'inquiry' => 0, 'visit' => 0, 'meeting' => 0, 'consultation' => 0, 'other' => 0];

while ($stat = $stats_result->fetch_assoc()) {
    $purpose_name = strtolower($stat['purpose_name']);
    if (isset($stats[$purpose_name])) {
        $stats[$purpose_name] = $stat['count'];
    }
    $stats['total'] += $stat['count'];
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delete_stmt = $conn->prepare("DELETE FROM visitors WHERE visitor_id = ?");
    $delete_stmt->bind_param("i", $delete_id);
    
    if ($delete_stmt->execute()) {
        header("Location: index.php?deleted=1");
        exit();
    }
}
?>