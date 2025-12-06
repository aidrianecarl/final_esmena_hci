<?php
include 'includes/index.php'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/index.css">
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
        
        <?php include 'includes/dashboard/cards.php'; ?>
        
        <?php include 'includes/dashboard/filters.php'; ?>
        
        <div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="create-visitor.php" class="btn btn-new-visitor">
        <i class="fas fa-user-plus"></i> New Visitor
    </a>

    <div style="font-weight: 600; font-size: 16px; color: #555;">
        <i class="fas fa-calendar-day"></i> Today: 
        <span style="color: var(--primary-red);">
            <?php echo date('F d, Y'); ?>
        </span>
    </div>
</div>

        <?php include 'includes/dashboard/table.php'; ?>
        
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
