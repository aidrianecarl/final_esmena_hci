<!-- Visitors Table -->
        <div class="table-section">
            <?php if ($visitors->num_rows > 0): ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Visitor Name</th>
                        <th>Contact</th>
                        <th>School/Office</th>
                        <th>Purpose</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($visitor = $visitors->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo date('d M Y', strtotime($visitor['date_of_visit'])); ?></strong></td>
                        <td><?php echo date('h:i A', strtotime($visitor['time_of_visit'])); ?></td>
                        <td><?php echo htmlspecialchars($visitor['visitor_name']); ?></td>
                        <td><?php echo htmlspecialchars($visitor['contact_number']); ?></td>
                        <td><?php echo htmlspecialchars($visitor['school_office']); ?></td>
                        <td>
                            <span class="badge" style="background: rgba(220, 20, 60, 0.15); color: var(--primary-red); padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                <?php echo ucfirst(htmlspecialchars($visitor['purpose_name'])); ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $visitor['visitor_id']; ?>">
                                <button type="submit" class="btn btn-sm" style="background: #fee; color: #c82333; border: 1px solid #fcc; border-radius: 6px;" onclick="return confirm('Delete this record?');">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h4>No Visitors Found</h4>
                <p>Try adjusting your filters or add a new visitor.</p>
            </div>
            <?php endif; ?>
        </div>