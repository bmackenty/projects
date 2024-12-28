<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($project['name']) ?></h1>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <a href="<?= $base_url ?>/projects/<?= $project['id'] ?>/tasks/create" class="btn btn-primary">Add Task</a>
        <?php endif; ?>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Project Description</h5>
            <div class="card-text"><?= $project['description'] ?></div>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'];
            unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error'];
            unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <h2>Tasks</h2>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Time Spent</th>
                    <th>Last Updated</th>
                    <th>Due Date</th>
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($tasks)):
                    // First, get all parent tasks (tasks with no parent)
                    $parentTasks = array_filter($tasks, function($task) {
                        return empty($task['parent_task_id']);
                    });

                    // Helper function to render task and its children recursively
                    function renderTaskRow($task, $tasks, $level = 0, $uploadModel, $base_url) {
                        $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                        ?>
                        <tr>
                            <td>
                                <?= $indent ?>
                                <?php if ($level > 0): ?>
                                    <i class="bi bi-arrow-return-right"></i>
                                <?php endif; ?>
                                <a href="<?= $base_url ?>/tasks/view/<?= $task['id'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($task['name']) ?>
                                    <?php if ($uploadModel->hasUploads($task['id'])): ?>
                                        <i class="bi bi-paperclip text-muted" title="Has attachments"></i>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td><?=substr($task['description'], 0, 80) ?>...</td>
                            <td>
                                <span class="badge bg-<?= $task['status'] === 'completed' ? 'success' :
                                    ($task['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
                                    <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                </span>
                            </td>
                            <td><?= $task['time'] ?> hours</td>
                            <td><?= $task['last_updated'] ?></td>
                            <td>
                                <?php if ($task['due_date']): ?>
                                    <?= htmlspecialchars($task['due_date']) ?>
                                    <?php 
                                    $due_date = new DateTime($task['due_date']);
                                    $today = new DateTime();
                                    if ($today > $due_date && $task['status'] !== 'completed'): ?>
                                        <span class="badge bg-danger">Overdue</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                                <td>
                                    <a href="<?= $base_url ?>/tasks/edit/<?= $task['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary">Edit</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php
                        // Find and render child tasks
                        $childTasks = array_filter($tasks, function($t) use ($task) {
                            return $t['parent_task_id'] == $task['id'];
                        });
                        foreach ($childTasks as $childTask) {
                            renderTaskRow($childTask, $tasks, $level + 1, $uploadModel, $base_url);
                        }
                    }

                    // Render all parent tasks and their children
                    foreach ($parentTasks as $parentTask):
                        renderTaskRow($parentTask, $tasks, 0, $uploadModel, $base_url);
                    endforeach;
                endif;
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>