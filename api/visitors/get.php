<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $action = $_GET['action'] ?? '';

    if ($action === 'get_all') {
        $search = $_GET['search'] ?? '';
        $purpose = $_GET['purpose'] ?? '';
        $date_from = $_GET['date_from'] ?? '';
        $date_to = $_GET['date_to'] ?? '';
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $query = "SELECT v.visitor_id, v.visitor_name, v.contact_number, v.address, v.school_office, 
                  p.purpose_name, v.date_of_visit, v.time_of_visit, v.notes, v.created_at 
                  FROM visitors v
                  LEFT JOIN purposes p ON v.purpose_id = p.id
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
            $query .= " AND v.purpose_id = ?";
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

        $query .= " ORDER BY v.date_of_visit DESC, v.time_of_visit DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt = $conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $visitors = [];
        while ($row = $result->fetch_assoc()) {
            $visitors[] = $row;
        }

        echo json_encode(['success' => true, 'data' => $visitors]);
        
    } else if ($action === 'get_single') {
        $visitor_id = $_GET['visitor_id'] ?? 0;

        if (empty($visitor_id)) {
            throw new Exception("Visitor ID is required");
        }

        $stmt = $conn->prepare("SELECT v.*, p.purpose_name FROM visitors v LEFT JOIN purposes p ON v.purpose_id = p.id WHERE v.visitor_id = ?");
        $stmt->bind_param("i", $visitor_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Visitor not found");
        }

        $visitor = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $visitor]);

    } else if ($action === 'get_statistics') {
        $date = $_GET['date'] ?? date('Y-m-d');

        $stmt = $conn->prepare("
            SELECT p.purpose_name, COUNT(*) as count 
            FROM visitors v
            LEFT JOIN purposes p ON v.purpose_id = p.id
            WHERE v.date_of_visit = ? 
            GROUP BY v.purpose_id, p.purpose_name
        ");
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();

        $stats = [
            'total' => 0,
            'exam' => 0,
            'inquiry' => 0,
            'visit' => 0,
            'meeting' => 0,
            'consultation' => 0,
            'other' => 0,
            'by_purpose' => []
        ];

        while ($row = $result->fetch_assoc()) {
            $purpose_key = strtolower(str_replace(' ', '_', $row['purpose_name']));
            $stats['by_purpose'][] = [
                'name' => $row['purpose_name'],
                'count' => $row['count']
            ];
            $stats['total'] += $row['count'];
            
            if (isset($stats[$purpose_key])) {
                $stats[$purpose_key] = $row['count'];
            }
        }

        echo json_encode(['success' => true, 'data' => $stats]);

    } else if ($action === 'get_daily_stats') {
        $date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
        $date_to = $_GET['date_to'] ?? date('Y-m-d');

        $stmt = $conn->prepare("
            SELECT DATE(v.date_of_visit) as visit_date, COUNT(*) as total 
            FROM visitors v
            WHERE v.date_of_visit BETWEEN ? AND ? 
            GROUP BY DATE(v.date_of_visit)
            ORDER BY visit_date DESC
        ");
        $stmt->bind_param("ss", $date_from, $date_to);
        $stmt->execute();
        $result = $stmt->get_result();

        $stats = [];
        while ($row = $result->fetch_assoc()) {
            $stats[] = $row;
        }

        echo json_encode(['success' => true, 'data' => $stats]);

    } else if ($action === 'get_purposes') {
        $stmt = $conn->prepare("SELECT id, purpose_name FROM purposes ORDER BY purpose_name");
        $stmt->execute();
        $result = $stmt->get_result();

        $purposes = [];
        while ($row = $result->fetch_assoc()) {
            $purposes[] = $row;
        }

        echo json_encode(['success' => true, 'data' => $purposes]);

    } else {
        throw new Exception("Invalid action");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
