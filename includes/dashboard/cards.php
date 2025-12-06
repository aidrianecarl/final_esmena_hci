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