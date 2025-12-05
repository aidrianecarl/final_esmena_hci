<?php
require_once __DIR__ . '/config/db.php';

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

// Get filters
$search = $_GET['search'] ?? '';
$purpose = $_GET['purpose'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-red: #DC143C;
            --secondary-red: #C41E3A;
            --light-bg: #f8f9fa;
            --card-border: #e9ecef;
        }
        
        body {
            background: var(--light-bg);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', sans-serif;
        }
        
        navbar {
            height: 80px;
        }
        
        /* Modern card styles for statistics */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid var(--card-border);
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(220, 20, 60, 0.12);
            border-color: var(--primary-red);
        }
        
        .stat-card h4 {
            color: #666;
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 12px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-card .number {
            font-size: 36px;
            font-weight: 700;
            color: var(--primary-red);
            margin: 0;
        }
        
        .stat-card i {
            color: var(--primary-red);
            font-size: 20px;
        }
        
        /* Modern table styling -->
        .table-section {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--card-border);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        
        .table {
            margin: 0;
        }
        
        .table thead {
            background: #f8f9fa;
            border-bottom: 2px solid var(--card-border);
        }
        
        .table thead th {
            color: #666;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px 20px;
            border: none;
        }
        
        .table tbody tr {
            border-bottom: 1px solid var(--card-border);
            transition: background 0.2s ease;
        }
        
        .table tbody tr:hover {
            background: #fafbfc;
        }
        
        .table tbody td {
            padding: 16px 20px;
            color: #555;
            vertical-align: middle;
        }
        
        /* Filter section styling */
        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid var(--card-border);
            margin-bottom: 24px;
        }
        
        .filter-section .form-control,
        .filter-section .form-select {
            border: 1px solid var(--card-border);
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
        }
        
        .filter-section .form-control:focus,
        .filter-section .form-select:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.1);
        }
        
        .btn-search {
            background: var(--primary-red);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .btn-search:hover {
            background: var(--secondary-red);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 20, 60, 0.2);
        }
        
        .btn-new-visitor {
            background: var(--primary-red);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.2s ease;
        }
        
        .btn-new-visitor:hover {
            background: var(--secondary-red);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 20, 60, 0.2);
        }
        
        .main-content {
            margin-left: 260px;
            padding: 32px;
            min-height: 100vh;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 40px;
            color: #999;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            color: #ddd;
        }
        
        .empty-state h4 {
            color: #666;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px; border: none; background: #d4edda;">
            <i class="fas fa-check-circle"></i> Visitor record deleted successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- Modern statistics cards -->
        <div class="row mb-4" style="margin: -8px;">
            <div class="col-md-3 mb-4" style="padding: 8px;">
                <div class="stat-card">
                    <h4><i class="fas fa-calendar-day"></i> Today's Visitors</h4>
                    <div class="number"><?php echo $stats['total']; ?></div>
                </div>
            </div>
            <div class="col-md-3 mb-4" style="padding: 8px;">
                <div class="stat-card">
                    <h4><i class="fas fa-graduation-cap"></i> Exams</h4>
                    <div class="number"><?php echo $stats['exam']; ?></div>
                </div>
            </div>
            <div class="col-md-3 mb-4" style="padding: 8px;">
                <div class="stat-card">
                    <h4><i class="fas fa-handshake"></i> Inquiries</h4>
                    <div class="number"><?php echo $stats['inquiry']; ?></div>
                </div>
            </div>
            <div class="col-md-3 mb-4" style="padding: 8px;">
                <div class="stat-card">
                    <h4><i class="fas fa-tasks"></i> Other</h4>
                    <div class="number"><?php echo $stats['other']; ?></div>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filter-section">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-search"></i> Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, Contact, School" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-list"></i> Purpose</label>
                    <select name="purpose" class="form-select">
                        <option value="">All</option>
                        <?php
                        $purposes_stmt = $conn->prepare("SELECT id, purpose_name FROM visit_purposes ORDER BY purpose_name");
                        $purposes_stmt->execute();
                        $purposes_result = $purposes_stmt->get_result();
                        $purposes = [];
                        while ($p = $purposes_result->fetch_assoc()) {
                            $purposes[] = $p;
                        }
                        foreach ($purposes as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo $purpose === $p['id'] ? 'selected' : ''; ?>>
                                <?php echo ucfirst(htmlspecialchars($p['purpose_name'])); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-calendar"></i> From</label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-calendar"></i> To</label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>">
                </div>
                <div class="col-md-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-search flex-grow-1">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="index.php" class="btn btn-secondary" style="border-radius: 8px; border: 1px solid var(--card-border); background: white; color: #555;">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Action Buttons -->
        <div class="mb-4 d-flex gap-2">
            <a href="create-visitor.php" class="btn btn-new-visitor">
                <i class="fas fa-user-plus"></i> New Visitor
            </a>
        </div>
        
        <!-- Visitors Table -->
        <div class="table-section">
            <?php if ($visitors->num_rows > 0): ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Visitor Name</th>
                        <th>Contact</th>
                        <th>School/Office</th>
                        <th>Purpose</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($visitor = $visitors->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo date('d M Y', strtotime($visitor['date_of_visit'])); ?></strong></td>
                        <td><?php echo date('h:i A', strtotime($visitor['time_of_visit'])); ?></td>
                        <td><?php echo htmlspecialchars($visitor['visitor_name']); ?></td>
                        <td><?php echo htmlspecialchars($visitor['contact_number']); ?></td>
                        <td><?php echo htmlspecialchars($visitor['school_office']); ?></td>
                        <td>
                            <span class="badge" style="background: rgba(220, 20, 60, 0.15); color: var(--primary-red); padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                <?php echo ucfirst(htmlspecialchars($visitor['purpose_name'])); ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $visitor['visitor_id']; ?>">
                                <button type="submit" class="btn btn-sm" style="background: #fee; color: #c82333; border: 1px solid #fcc; border-radius: 6px;" onclick="return confirm('Delete this record?');">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h4>No Visitors Found</h4>
                <p>Try adjusting your filters or add a new visitor.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
