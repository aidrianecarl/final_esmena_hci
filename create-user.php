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

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'staff';

    // Validation
    $errors = [];
    if (empty($username)) $errors[] = "Username is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($password)) $errors[] = "Password is required";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters";

    // Check if username exists
    $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        $errors[] = "Username already exists";
    }

    // Check if email exists
    $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        $errors[] = "Email already exists";
    }

    if (empty($errors)) {
        $status = 'active';

        // Save password AS IS (plain text)
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $password, $role, $status);

        if ($stmt->execute()) {
            $success_message = "User created successfully!";
            $_POST = [];
        } else {
            $error_message = "Error creating user: " . $stmt->error;
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User - <?php echo APP_NAME; ?></title>
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
        .main-content { margin-left: 260px; margin-top: 80px; padding: 32px; min-height: 100vh; }
        .page-header { margin-bottom: 32px; }
        .page-header h1 { font-size: 32px; font-weight: 700; color: #222; margin: 0 0 8px 0; }
        .page-header p { color: #666; margin: 0; font-size: 15px; }
        .form-card { background: white; border-radius: 12px; border: 1px solid var(--card-border); box-shadow: 0 2px 8px rgba(0,0,0,0.04); max-width: 700px; margin: 0 auto; }
        .form-group-section { padding: 24px; }
        .form-label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; font-size: 14px; }
        .form-control { border-radius: 8px; border: 1px solid var(--card-border); padding: 10px 12px; font-size: 14px; }
        .form-control:focus { border-color: var(--primary-red); box-shadow: 0 0 0 3px rgba(220,20,60,0.1); }
        .btn-submit { background: var(--primary-red); color: white; border: none; border-radius: 8px; padding: 10px 24px; font-weight: 600; transition: background 0.2s ease; }
        .btn-submit:hover { background: var(--secondary-red); color: white; }
        .btn-cancel { background: #f8f9fa; color: #555; border: 1px solid var(--card-border); border-radius: 8px; padding: 10px 24px; font-weight: 600; text-decoration: none; transition: all 0.2s ease; }
        .btn-cancel:hover { background: #e9ecef; color: #555; }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-user-tie me-2" style="color: var(--primary-red);"></i>Create New User</h1>
            <p>Add a new user account to the system</p>
        </div>

        <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px; border: none; background: #d4edda; margin-bottom: 24px;">
            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 8px; border: none; background: #f8d7da; margin-bottom: 24px;">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form method="POST" class="form-card">
            <div class="form-group-section">
                <div class="mb-3">
                    <label class="form-label">Username *</label>
                    <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address *</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">User Role *</label>
                    <select class="form-control" name="role" required>
                        <option value="staff" <?php echo ($_POST['role'] ?? '') === 'staff' ? 'selected' : ''; ?>>Staff</option>
                        <option value="manager" <?php echo ($_POST['role'] ?? '') === 'manager' ? 'selected' : ''; ?>>Manager</option>
                        <option value="admin" <?php echo ($_POST['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save me-2"></i>Create User
                    </button>
                    <a href="users.php" class="btn-cancel">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
