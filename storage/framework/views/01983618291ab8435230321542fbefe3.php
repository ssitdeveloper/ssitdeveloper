

<?php $__env->startSection('title', 'Manage Tests'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-page-container">
    <div class="page-header">
        <div>
            <h1>Test Management</h1>
            <p>Create and manage entrance exam tests</p>
        </div>
        <a href="<?php echo e(route('admin.tests.create')); ?>" class="btn-primary">+ Create New Test</a>
    </div>

    <!-- Success Message -->
    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <strong>Success!</strong> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <!-- Filter Section -->
    <div class="filter-panel">
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <input type="text" name="search" placeholder="Search by test title..."
                       value="<?php echo e(request('search')); ?>" class="filter-input">
            </div>
            <div class="filter-group">
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Active</option>
                    <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter">Filter</button>
                <a href="<?php echo e(route('admin.tests.index')); ?>" class="btn-reset">Clear</a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Tests</div>
            <div class="stat-value"><?php echo e($tests->total()); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Active</div>
            <div class="stat-value" style="color: #10b981;"><?php echo e($tests->filter(fn($t) => $t->is_active)->count()); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Inactive</div>
            <div class="stat-value" style="color: #f59e0b;"><?php echo e($tests->filter(fn($t) => !$t->is_active)->count()); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Questions</div>
            <div class="stat-value"><?php echo e($tests->sum(fn($t) => $t->questions_count ?? 0)); ?></div>
        </div>
    </div>

    <!-- Tests Table -->
    <?php if($tests->count() > 0): ?>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 35%;">Test Name</th>
                        <th style="width: 12%;">Questions</th>
                        <th style="width: 12%;">Duration</th>
                        <th style="width: 12%;">Passing %</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $tests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $test): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="table-row">
                            <td><?php echo e(($tests->currentPage() - 1) * 15 + $index + 1); ?></td>
                            <td>
                                <div class="test-info">
                                    <strong><?php echo e($test->title); ?></strong>
                                    <?php if($test->description): ?>
                                        <p class="text-muted"><?php echo e(Str::limit($test->description, 50)); ?></p>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info"><?php echo e($test->questions_count ?? 0); ?></span>
                            </td>
                            <td><?php echo e($test->duration_minutes); ?> min</td>
                            <td><?php echo e($test->passing_percentage); ?>%</td>
                            <td>
                                <?php if($test->is_active): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="action-cell">
                                <a href="<?php echo e(route('admin.tests.show', $test)); ?>" class="btn-action btn-view" title="View">👁️</a>
                                <a href="<?php echo e(route('admin.tests.edit', $test)); ?>" class="btn-action btn-edit" title="Edit">✏️</a>
                                <form action="<?php echo e(route('admin.tests.destroy', $test)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Delete this test?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn-action btn-delete" title="Delete">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            <?php echo e($tests->links('pagination::bootstrap-4')); ?>

        </div>
    <?php else: ?>
        <div class="empty-state">
            <p>No tests found.</p>
            <a href="<?php echo e(route('admin.tests.create')); ?>" class="btn-primary">Create your first test</a>
        </div>
    <?php endif; ?>
</div>

<style>
    .admin-page-container {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .page-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .page-header p {
        color: #6b7280;
        margin: 5px 0 0 0;
    }

    .btn-primary {
        background-color: #3b82f6;
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        cursor: pointer;
        border: none;
        transition: background-color 0.2s;
    }

    .btn-primary:hover {
        background-color: #2563eb;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        border-left: 4px solid;
    }

    .alert-success {
        background-color: rgba(16, 185, 129, 0.1);
        color: #059669;
        border-left-color: #059669;
    }

    .filter-panel {
        background: #f9fafb;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .filter-form {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-input,
    .filter-select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
    }

    .filter-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
        align-items: flex-end;
    }

    .btn-filter,
    .btn-reset {
        padding: 10px 16px;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-filter {
        background-color: #3b82f6;
        color: white;
    }

    .btn-filter:hover {
        background-color: #2563eb;
    }

    .btn-reset {
        background-color: #e5e7eb;
        color: #374151;
        text-decoration: none;
    }

    .btn-reset:hover {
        background-color: #d1d5db;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: linear-gradient(135deg, #f3f4f6, #ffffff);
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
    }

    .stat-label {
        color: #6b7280;
        font-size: 13px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
        margin: 10px 0 0 0;
    }

    .table-container {
        background: white;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table thead {
        background-color: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }

    .admin-table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .admin-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        color: #4b5563;
        font-size: 14px;
    }

    .admin-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .test-info strong {
        display: block;
        margin-bottom: 3px;
    }

    .text-muted {
        color: #9ca3af;
        font-size: 0.85em;
    }

    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-info {
        background-color: #dbeafe;
        color: #0c4a6e;
    }

    .badge-success {
        background-color: #dcfce7;
        color: #166534;
    }

    .badge-warning {
        background-color: #fef3c7;
        color: #7c2d12;
    }

    .action-cell {
        text-align: center;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 4px;
        background-color: #f3f4f6;
        border: 1px solid #d1d5db;
        text-decoration: none;
        font-size: 16px;
        transition: all 0.2s;
        margin: 0 2px;
        cursor: pointer;
    }

    .btn-action:hover {
        background-color: #e5e7eb;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
        background: #f9fafb;
        border-radius: 8px;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\neet\resources\views/admin/tests/index.blade.php ENDPATH**/ ?>