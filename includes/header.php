<?php
$page_title = $page_title ?? 'Dashboard';
$breadcrumbs = $breadcrumbs ?? [];
?>

<header class="py-4 mb-4" style="background: linear-gradient(135deg, rgba(220,20,60,0.1) 0%, rgba(196,30,58,0.1) 100%); border-bottom: 2px solid #DC143C;">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 fw-bold" style="color: #333;">
                    <i class="fas fa-<?php echo $page_icon ?? 'home'; ?> me-2" style="color: #DC143C;"></i>
                    <?php echo htmlspecialchars($page_title); ?>
                </h1>
                <?php if (!empty($breadcrumbs)): ?>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb mb-0">
                        <?php foreach ($breadcrumbs as $breadcrumb): ?>
                            <li class="breadcrumb-item<?php echo ($breadcrumb['active'] ?? false) ? ' active' : ''; ?>">
                                <?php if (isset($breadcrumb['url'])): ?>
                                    <a href="<?php echo $breadcrumb['url']; ?>" style="color: #DC143C; text-decoration: none;">
                                        <?php echo htmlspecialchars($breadcrumb['name']); ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($breadcrumb['name']); ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
                <?php endif; ?>
            </div>
            <?php if (isset($header_action)): ?>
                <div>
                    <?php echo $header_action; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>
