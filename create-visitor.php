<?php
require_once __DIR__ . '/config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$success_message = '';
$error_message = '';

$purposes_result = $conn->query("SELECT id, purpose_name FROM visit_purposes ORDER BY purpose_name ASC");
$purposes = [];
while ($row = $purposes_result->fetch_assoc()) {
    $purposes[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitor_name = $_POST['visitor_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $school_office = $_POST['school_office'] ?? '';
    $purpose_id = $_POST['purpose_id'] ?? ''; // Changed from purpose_of_visit to purpose_id
    $date_of_visit = $_POST['date_of_visit'] ?? '';
    $time_of_visit = $_POST['time_of_visit'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $user_id = $_SESSION['user_id'];

    // Validation
    $errors = [];
    if (empty($visitor_name)) $errors[] = "Full name is required";
    if (empty($address)) $errors[] = "Address is required";
    if (empty($contact_number)) $errors[] = "Contact number is required";
    if (empty($school_office)) $errors[] = "School/Office name is required";
    if (empty($purpose_id)) $errors[] = "Purpose of visit is required"; // Updated error message
    if (empty($date_of_visit)) $errors[] = "Date of visit is required";
    if (empty($time_of_visit)) $errors[] = "Time of visit is required";

    if (!preg_match('/^(09|\+639)\d{9}$/', $contact_number)) {
        $errors[] = "Invalid Philippine mobile number format";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO visitors (visitor_name, address, contact_number, school_office, purpose_id, date_of_visit, time_of_visit, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssisssi", $visitor_name, $address, $contact_number, $school_office, $purpose_id, $date_of_visit, $time_of_visit, $notes, $user_id);

        if ($stmt->execute()) {
            $success_message = "Visitor added successfully!";
            // Reset form
            $_POST = [];
        } else {
            $error_message = "Error adding visitor: " . $stmt->error;
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
    <title>Create Visitor - <?php echo APP_NAME; ?></title>
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
        
        .form-card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--card-border);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            max-width: 700px;
            margin: 0 auto;
        }
        
        .form-group-section {
            padding: 24px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-control {
            border-radius: 8px;
            border: 1px solid var(--card-border);
            padding: 10px 12px;
            font-size: 14px;
        }
        
        .form-control:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 3px rgba(220, 20, 60, 0.1);
        }
        
        .btn-submit {
            background: var(--primary-red);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 600;
            transition: background 0.2s ease;
        }
        
        .btn-submit:hover {
            background: var(--secondary-red);
            color: white;
        }
        
        .btn-cancel {
            background: #f8f9fa;
            color: #555;
            border: 1px solid var(--card-border);
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .btn-cancel:hover {
            background: #e9ecef;
            color: #555;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-user-plus me-2" style="color: var(--primary-red);"></i>Create New Visitor</h1>
            <p>Add a new visitor to the system</p>
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
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control" name="visitor_name" value="<?php echo htmlspecialchars($_POST['visitor_name'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Number *</label>
                        <input type="tel" class="form-control" name="contact_number" placeholder="09xxxxxxxxx" value="<?php echo htmlspecialchars($_POST['contact_number'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Address *</label>
                    <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">School/Office *</label>
                    <input type="text" class="form-control" name="school_office" value="<?php echo htmlspecialchars($_POST['school_office'] ?? ''); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Purpose of Visit *</label>
                    <!-- Using dynamic purposes from database -->
                    <select class="form-control" name="purpose_id" required>
                        <option value="">Select purpose...</option>
                        <?php foreach ($purposes as $purpose): ?>
                        <option value="<?php echo $purpose['id']; ?>" <?php echo ($_POST['purpose_id'] ?? '') == $purpose['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($purpose['purpose_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Visit *</label>
                        <input type="date" class="form-control" name="date_of_visit" value="<?php echo htmlspecialchars($_POST['date_of_visit'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Time of Visit *</label>
                        <input type="time" class="form-control" name="time_of_visit" value="<?php echo htmlspecialchars($_POST['time_of_visit'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Additional Notes</label>
                    <textarea class="form-control" name="notes" rows="4" style="resize: vertical;"><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                </div>
                
                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save me-2"></i>Create Visitor
                    </button>
                    <a href="index.php" class="btn-cancel">Cancel</a>
                </div>
            </div>
        </form>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
