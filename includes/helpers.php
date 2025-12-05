<?php

/**
 * Format date to display format
 */
function formatDate($date) {
    return date('d M Y', strtotime($date));
}

/**
 * Format time to display format
 */
function formatTime($time) {
    return date('h:i A', strtotime($time));
}

/**
 * Get purpose badge HTML
 */
function getPurposeBadge($purpose) {
    $colors = [
        'exam' => 'badge-warning',
        'inquiry' => 'badge-info',
        'visit' => 'badge-success',
        'meeting' => 'badge-danger',
        'consultation' => 'badge-purple',
        'other' => 'badge-secondary'
    ];
    
    $color = $colors[$purpose] ?? 'badge-secondary';
    return '<span class="badge ' . $color . '">' . ucfirst($purpose) . '</span>';
}

/**
 * Check user permission
 */
function hasPermission($required_role) {
    $user_role = $_SESSION['role'] ?? '';
    
    $permissions = [
        'staff' => ['staff', 'manager', 'admin'],
        'manager' => ['manager', 'admin'],
        'admin' => ['admin']
    ];
    
    return in_array($user_role, $permissions[$required_role] ?? []);
}

/**
 * Sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone number
 */
function isValidPhone($phone) {
    return preg_match('/^(09|\+639)\d{9}$/', str_replace(['-', ' '], '', $phone));
}

/**
 * Get total visitors
 */
function getTotalVisitors($date = null) {
    global $conn;
    $date = $date ?? date('Y-m-d');
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM visitors WHERE date_of_visit = ?");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'];
}

/**
 * Get visitor by ID
 */
function getVisitorById($visitor_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM visitors WHERE visitor_id = ?");
    $stmt->bind_param("i", $visitor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

?>
