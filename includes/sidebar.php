<?php
$user_role = $_SESSION['role'] ?? 'staff';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar" style="width: 260px; background: #f8f9fa; border-right: 1px solid #e9ecef; min-height: 100vh; padding: 20px 0; position: fixed; left: 0; top: 80px; bottom: 0; z-index: 999; overflow-y: auto;">
    <div class="sidebar-content">
        <div class="px-3 mb-4">
            <h6 style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px;">Navigation</h6>
            
            <a href="index.php" class="sidebar-item <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; text-decoration: none; color: #555; border-radius: 8px; margin-bottom: 8px; transition: all 0.2s ease; font-weight: 500;">
                <i class="fas fa-home" style="width: 20px; text-align: center; color: #DC143C;"></i>
                <span>Dashboard</span>
            </a>
            
            <?php if ($user_role === 'admin'): ?>
            <a href="users.php" class="sidebar-item <?php echo $current_page === 'users.php' ? 'active' : ''; ?>" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; text-decoration: none; color: #555; border-radius: 8px; margin-bottom: 8px; transition: all 0.2s ease; font-weight: 500;">
                <i class="fas fa-users" style="width: 20px; text-align: center; color: #DC143C;"></i>
                <span>Users</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
        .sidebar-item {
            transition: all 0.2s ease;
        }
        .sidebar-item:hover {
            background: rgba(220, 20, 60, 0.08);
            color: #DC143C !important;
        }
        .sidebar-item.active {
            background: rgba(220, 20, 60, 0.15);
            color: #DC143C !important;
            border-left: 3px solid #DC143C;
            padding-left: 13px;
        }
    </style>
</div>

<!-- Main content offset for sidebar -->
<style>
    .main-content {
        margin-left: 260px;
        margin-top: 30px;
        padding: 32px;
    }
    
    body {
        padding-top: 30px;
    }
</style>
