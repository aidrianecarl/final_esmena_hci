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
    
    if ($_POST['action'] === 'edit_user' && isset($_POST['user_id']) && isset($_POST['email']) && isset($_POST['role'])) {
        $user_id = $_POST['user_id'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        
        $edit_stmt = $conn->prepare("UPDATE users SET email = ?, role = ? WHERE user_id = ?");
        $edit_stmt->bind_param("ssi", $email, $role, $user_id);
        
        if ($edit_stmt->execute()) {
            header("Location: users.php?edited=1");
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
    <link rel="stylesheet" href="assets/css/users.css">
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
        
        <?php if (isset($_GET['edited'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px; border: none; background: #d4edda; margin-bottom: 24px;">
            <i class="fas fa-check-circle"></i> User details updated successfully!
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
                <!-- Added Create User button next to user count -->
                <div style="display: flex; gap: 12px; align-items: center;">
                    <span style="background: rgba(220, 20, 60, 0.1); color: var(--primary-red); padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600;">
                        <?php echo $users_result->num_rows; ?> Users
                    </span>
                    <a href="create-user.php" style="background: var(--primary-red); color: white; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; transition: background 0.2s ease;">
                        <i class="fas fa-user-plus me-1"></i>Create User
                    </a>
                </div>
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
                            <div style="display: flex; gap: 8px;">
                                <button class="btn-action" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $user['user_id']; ?>">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <input type="hidden" name="status" value="<?php echo $user['status'] === 'active' ? 'inactive' : 'active'; ?>">
                                    <button type="submit" class="btn-action">
                                        <i class="fas fa-<?php echo $user['status'] === 'active' ? 'ban' : 'check'; ?> me-1"></i>
                                        <?php echo $user['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal<?php echo $user['user_id']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border: 1px solid #e9ecef; border-radius: 12px;">
                                <div class="modal-header" style="border-bottom: 1px solid #e9ecef; padding: 20px 24px;">
                                    <h5 class="modal-title" style="color: #222; font-weight: 600;">Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" style="display: inline;">
                                    <div class="modal-body" style="padding: 24px;">
                                        <input type="hidden" name="action" value="edit_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                        
                                        <div class="mb-3">
                                            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600; font-size: 14px;">Username</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled style="border-radius: 8px; border: 1px solid #e9ecef; padding: 10px 12px;">
                                        </div>
                                        <div class="mb-3">
                                            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600; font-size: 14px;">Email</label>
                                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" style="border-radius: 8px; border: 1px solid #e9ecef; padding: 10px 12px;">
                                        </div>
                                        <div class="mb-3">
                                            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600; font-size: 14px;">Role</label>
                                            <select class="form-control" name="role" style="border-radius: 8px; border: 1px solid #e9ecef; padding: 10px 12px;">
                                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                <option value="manager" <?php echo $user['role'] === 'manager' ? 'selected' : ''; ?>>Manager</option>
                                                <option value="staff" <?php echo $user['role'] === 'staff' ? 'selected' : ''; ?>>Staff</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 16px 24px;">
                                        <button type="button" class="btn" data-bs-dismiss="modal" style="border-radius: 8px; background: #f8f9fa; color: #555; border: 1px solid #e9ecef; padding: 8px 16px; font-weight: 600;">Cancel</button>
                                        <button type="submit" class="btn" style="border-radius: 8px; background: #DC143C; color: white; border: none; padding: 8px 16px; font-weight: 600;">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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
