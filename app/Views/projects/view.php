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
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($tasks)):
                    foreach ($tasks as $task):
                        ?>
                        <tr>
                            <td>
                                <a href="<?= $base_url ?>/tasks/view/<?= $task['id'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($task['name']) ?>
                                    <?php if ($uploadModel->hasUploads($task['id'])): ?>
                                        <i class="bi bi-paperclip text-muted" title="Has attachments"></i>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td class="text-truncate" style="max-width: 200px;">
                                <?= $task['description'] ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $task['status'] === 'completed' ? 'success' :
                                    ($task['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
                                    <?= htmlspecialchars(str_replace('_', ' ', ucfirst($task['status']))) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($task['time']) ?> hours</td>
                            <td><?= htmlspecialchars($task['last_updated']) ?></td>
                            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                                <td>
                                    <a href="<?= $base_url ?>/tasks/edit/<?= $task['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php
                    endforeach;
                else:
                    ?>
                    <tr>
                        <td colspan="6" class="text-center">No tasks found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>