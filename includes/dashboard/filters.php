<!-- Filters -->
        <div class="filter-section">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-search"></i> Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, Contact, School" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-list"></i> Purpose</label>
                    <select name="purpose" class="form-select">
                        <option value="">All</option>
                        <?php
                        $purposes_stmt = $conn->prepare("SELECT id, purpose_name FROM visit_purposes ORDER BY purpose_name");
                        $purposes_stmt->execute();
                        $purposes_result = $purposes_stmt->get_result();
                        $purposes = [];
                        while ($p = $purposes_result->fetch_assoc()) {
                            $purposes[] = $p;
                        }
                        foreach ($purposes as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo $purpose === $p['id'] ? 'selected' : ''; ?>>
                                <?php echo ucfirst(htmlspecialchars($p['purpose_name'])); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-calendar"></i> From</label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-calendar"></i> To</label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>">
                </div>
                <div class="col-md-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-search flex-grow-1">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="index.php" class="btn btn-secondary" style="border-radius: 8px; border: 1px solid var(--card-border); background: white; color: #555;">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>