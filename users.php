<?php
require_once __DIR__ . '/config/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Get all users
$users_stmt = $conn->prepare("SELECT user_id, username, email, role, status, created_at FROM users ORDER BY created_at DESC");
$users_stmt->execute();
$users_result = $users_stmt->get_result();

// Handle user status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status' && isset($_POST['user_id']) && isset($_POST['status'])) {
        $user_id = $_POST['user_id'];
        $status = $_POST['status'];
        
        $update_stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $status, $user_id);
        
        if ($update_stmt->execute()) {
            header("Location: users.php?updated=1");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - <?php echo APP_NAME; ?></title>
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
        
        .main-content {
            margin-left: 260px;
            margin-top: 80px;
            padding: 32px;
            min-height: 100vh;
        }
        
        .page-header {
            margin-bottom: 32px;
        }
        
        .page-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: #222;
            margin: 0 0 8px 0;
        }
        
        .page-header p {
            color: #666;
            margin: 0;
            font-size: 15px;
        }
        
        .users-card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--card-border);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        
        .users-header {
            background: #f8f9fa;
            border-bottom: 1px solid var(--card-border);
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .users-table {
            margin: 0;
        }
        
        .users-table thead {
            background: #f8f9fa;
            border-bottom: 2px solid var(--card-border);
        }
        
        .users-table thead th {
            color: #666;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px 20px;
            border: none;
        }
        
        .users-table tbody tr {
            border-bottom: 1px solid var(--card-border);
            transition: background 0.2s ease;
        }
        
        .users-table tbody tr:hover {
            background: #fafbfc;
        }
        
        .users-table tbody td {
            padding: 16px 20px;
            color: #555;
            vertical-align: middle;
        }
        
        .role-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .role-admin {
            background: rgba(220, 20, 60, 0.15);
            color: var(--primary-red);
        }
        
        .role-manager {
            background: rgba(0, 123, 255, 0.15);
            color: #007bff;
        }
        
        .role-staff {
            background: rgba(108, 117, 125, 0.15);
            color: #6c757d;
        }
        
        .status-active {
            color: #28a745;
        }
        
        .status-inactive {
            color: #dc3545;
        }
        
        .btn-action {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 6px;
            border: 1px solid var(--card-border);
            background: white;
            color: #555;
            transition: all 0.2s ease;
        }
        
        .btn-action:hover {
            background: var(--primary-red);
            color: white;
            border-color: var(--primary-red);
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
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px; border: none; background: #d4edda; margin-bottom: 24px;">
            <i class="fas fa-check-circle"></i> User status updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <div class="page-header">
            <h1><i class="fas fa-users me-2" style="color: var(--primary-red);"></i>User Management</h1>
            <p>Manage system users and their access roles</p>
        </div>
        
        <div class="users-card">
            <div class="users-header">
                <h5 style="margin: 0; color: #333; font-weight: 600;">All Users</h5>
                <span style="background: rgba(220, 20, 60, 0.1); color: var(--primary-red); padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600;">
                    <?php echo $users_result->num_rows; ?> Users
                </span>
            </div>
            
            <?php if ($users_result->num_rows > 0): ?>
            <table class="table users-table table-hover">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users_result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(220, 20, 60, 0.1); display: flex; align-items: center; justify-content: center; color: var(--primary-red);">
                                    <i class="fas fa-user"></i>
                                </div>
                                <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="role-badge role-<?php echo htmlspecialchars($user['role']); ?>">
                                <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-<?php echo htmlspecialchars($user['status']); ?>" style="font-weight: 600;">
                                <i class="fas fa-circle" style="font-size: 8px; margin-right: 6px;"></i>
                                <?php echo ucfirst(htmlspecialchars($user['status'])); ?>
                            </span>
                        </td>
                        <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <input type="hidden" name="status" value="<?php echo $user['status'] === 'active' ? 'inactive' : 'active'; ?>">
                                <button type="submit" class="btn-action">
                                    <i class="fas fa-<?php echo $user['status'] === 'active' ? 'ban' : 'check'; ?> me-1"></i>
                                    <?php echo $user['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h4>No Users Found</h4>
                <p>There are currently no users in the system.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
