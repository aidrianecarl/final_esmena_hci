<?php
$username = $_SESSION['username'] ?? 'Guest';
$user_role = $_SESSION['role'] ?? 'staff';
$user_id = $_SESSION['user_id'] ?? 0;
?>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: #DC143C; box-shadow: 0 2px 12px rgba(220, 20, 60, 0.15); border-bottom: 1px solid rgba(220, 20, 60, 0.2); z-index: 1000;">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php" style="gap: 1px;">
            <img src="public/cropped-ccdilogo.png" alt="Logo" style="height: 32px; width: auto;">
            <span><?php echo APP_NAME; ?></span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="display: flex; align-items: center; gap: 8px; color: white !important;">
                        <i class="fas fa-user-circle" style="font-size: 24px;"></i>
                        <span><?php echo htmlspecialchars($username); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" style="min-width: 220px; border: 1px solid #f0f0f0; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 8px; margin-top: 12px;">
                        <li><span class="dropdown-item" style="color: #666; font-size: 12px; pointer-events: none;">Role: <strong><?php echo ucfirst(htmlspecialchars($user_role)); ?></strong></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#manageAccountModal" style="color: #333; font-weight: 500;"><i class="fas fa-cog me-2" style="color: #DC143C;"></i>Manage Account</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php" style="font-weight: 500;"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Manage Account Modal -->
<div class="modal fade" id="manageAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #DC143C; color: white; border: none;">
                <h5 class="modal-title"><i class="fas fa-user-cog me-2"></i>Manage Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($username); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" value="<?php echo ucfirst(htmlspecialchars($user_role)); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" disabled>
                </div>
            </div>
        </div>
    </div>
</div>
