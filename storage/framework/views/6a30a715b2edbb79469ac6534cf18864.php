

<?php $__env->startSection('title', 'View Test'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-content">
    <div style="margin-bottom: var(--spacing-4); display: flex; justify-content: space-between; align-items: center;">
        <a href="<?php echo e(route('admin.tests.index')); ?>" style="color: var(--color-primary); text-decoration: none;">← Back to Tests</a>
        <div style="display: flex; gap: var(--spacing-2);">
            <a href="<?php echo e(route('admin.tests.edit', $test)); ?>" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-primary); color: var(--color-white); text-decoration: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); display: inline-block;">
                Edit
            </a>
            <form method="POST" action="<?php echo e(route('admin.tests.destroy', $test)); ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this test?');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" style="padding: var(--spacing-2) var(--spacing-4); background-color: var(--color-danger); color: var(--color-white); border: none; border-radius: var(--radius-lg); font-weight: var(--font-weight-semibold); cursor: pointer;">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); box-shadow: var(--shadow-sm); margin-bottom: var(--spacing-4);">
        <!-- Test Header -->
        <div style="margin-bottom: var(--spacing-4); padding-bottom: var(--spacing-3); border-bottom: 1px solid var(--color-gray-200);">
            <h1 style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-900);"><?php echo e($test->name); ?></h1>
            <p style="margin: 0; color: var(--color-gray-600); line-height: 1.6;"><?php echo e($test->description); ?></p>
        </div>

        <!-- Test Details Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-4); margin-bottom: var(--spacing-4);">
            <div style="padding: var(--spacing-3); background-color: var(--color-gray-50); border-radius: var(--radius-lg);">
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Total Questions</p>
                <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);"><?php echo e($test->total_questions); ?></p>
            </div>
            <div style="padding: var(--spacing-3); background-color: var(--color-gray-50); border-radius: var(--radius-lg);">
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Duration</p>
                <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);"><?php echo e($test->duration_minutes); ?> min</p>
            </div>
            <div style="padding: var(--spacing-3); background-color: var(--color-gray-50); border-radius: var(--radius-lg);">
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Passing Score</p>
                <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);"><?php echo e($test->passing_score); ?>%</p>
            </div>
            <div style="padding: var(--spacing-3); background-color: var(--color-gray-50); border-radius: var(--radius-lg);">
                <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Total Attempts</p>
                <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: var(--color-primary);"><?php echo e($test->attempts->count()); ?></p>
            </div>
        </div>

        <!-- Test Statistics -->
        <div style="margin-bottom: var(--spacing-4); padding: var(--spacing-3); background-color: rgba(59, 130, 246, 0.1); border-radius: var(--radius-lg); border-left: 4px solid #3b82f6;">
            <h3 style="margin: 0 0 var(--spacing-2) 0; color: var(--color-gray-900);">Performance Statistics</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: var(--spacing-3);">
                <div>
                    <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Average Score</p>
                    <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: #3b82f6;">
                        <?php if($test->attempts->count() > 0): ?>
                            <?php echo e(round($test->attempts->avg('marks_obtained'))); ?>%
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Highest Score</p>
                    <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: #10b981;">
                        <?php if($test->attempts->count() > 0): ?>
                            <?php echo e($test->attempts->max('marks_obtained')); ?>%
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <p style="margin: 0 0 var(--spacing-1) 0; color: var(--color-gray-600); font-size: var(--font-size-sm);">Pass Rate</p>
                    <p style="margin: 0; font-size: 1.5rem; font-weight: var(--font-weight-bold); color: #f59e0b;">
                        <?php if($test->attempts->count() > 0): ?>
                            <?php echo e(round(($test->attempts->where('status', 'passed')->count() / $test->attempts->count()) * 100)); ?>%
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attempts Table -->
    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: var(--spacing-4); box-shadow: var(--shadow-sm);">
        <h2 style="margin-top: 0; margin-bottom: var(--spacing-3); color: var(--color-gray-900);">Recent Attempts</h2>

        <?php if($test->attempts->count() > 0): ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background-color: var(--color-gray-50); border-bottom: 2px solid var(--color-gray-200);">
                        <tr>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Student</th>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Date</th>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Score</th>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Status</th>
                            <th style="padding: var(--spacing-2); text-align: left; font-weight: var(--font-weight-semibold); color: var(--color-gray-900);">Time Taken</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $test->attempts->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr style="border-bottom: 1px solid var(--color-gray-200);">
                                <td style="padding: var(--spacing-2); color: var(--color-gray-900); font-weight: var(--font-weight-medium);"><?php echo e($attempt->user->name); ?></td>
                                <td style="padding: var(--spacing-2); color: var(--color-gray-700);"><?php echo e($attempt->created_at->format('M d, Y')); ?></td>
                                <td style="padding: var(--spacing-2); color: var(--color-gray-900); font-weight: var(--font-weight-semibold);"><?php echo e($attempt->marks_obtained); ?>%</td>
                                <td style="padding: var(--spacing-2);">
                                    <?php if($attempt->status->value === 'passed'): ?>
                                        <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background-color: #d4edda; color: #155724; border-radius: var(--radius-lg); font-size: var(--font-size-sm);">Passed</span>
                                    <?php else: ?>
                                        <span style="display: inline-block; padding: var(--spacing-1) var(--spacing-2); background-color: #f8d7da; color: #721c24; border-radius: var(--radius-lg); font-size: var(--font-size-sm);">Failed</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: var(--spacing-2); color: var(--color-gray-700);"><?php echo e($attempt->time_taken_minutes ?? 'N/A'); ?> min</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="color: var(--color-gray-600); text-align: center; padding: var(--spacing-4);">No attempts yet for this test.</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\neet\resources\views/admin/tests/show.blade.php ENDPATH**/ ?>