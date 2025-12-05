<?php
$current_year = date('Y');
?>

<footer class="bg-dark text-white mt-5 pt-5" style="border-top: 2px solid #DC143C;">
    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3" style="color: #FF6B6B;">
                    <i class="fas fa-building me-2"></i><?php echo APP_NAME; ?>
                </h5>
                <p class="text-muted small">
                    Professional visitor management system for <?php echo APP_LOCATION; ?>.
                </p>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3" style="color: #FF6B6B;">Contact</h5>
                <p class="text-muted small mb-2">
                    <i class="fas fa-envelope me-2"></i><?php echo APP_EMAIL; ?>
                </p>
                <p class="text-muted small">
                    <i class="fas fa-map-marker-alt me-2"></i><?php echo APP_LOCATION; ?>
                </p>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3" style="color: #FF6B6B;">Quick Links</h5>
                <ul class="list-unstyled text-muted small">
                    <li class="mb-2"><a href="index.php" class="text-decoration-none" style="color: #aaa;">Dashboard</a></li>
                    <li class="mb-2"><a href="logout.php" class="text-decoration-none" style="color: #aaa;">Logout</a></li>
                </ul>
            </div>
        </div>
        <hr class="bg-secondary">
        <div class="row">
            <div class="col-12 text-center text-muted small pb-3">
                <p>&copy; <?php echo $current_year; ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
