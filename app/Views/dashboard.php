<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Project Dashboard</h1>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($projects as $project): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?= htmlspecialchars($project['name']) ?></h5>
                        <?php if($_SESSION['user']['role'] === 'admin'): ?>
                            <a href="<?= $base_url ?>/projects/<?= $project['id'] ?>/tasks/create" 
                               class="btn btn-sm btn-primary">Add Task</a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Last updated: <?= htmlspecialchars($project['last_updated']) ?> <?= TimeHelper::getRelativeTime($project['last_updated']) ?></p>
                        <p><?= $project['description'] ?></p>
                        
                        <?php if (!empty($projectTasks[$project['id']])): ?>
                            <div class="list-group mt-3">
                                <?php foreach ($projectTasks[$project['id']] as $task): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($task['name']) ?></h6>
                                                <p class="mb-1 small text-muted"><?= $task['description'] ?></p>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-<?= $task['status'] === 'completed' ? 'success' : ($task['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
                                                    <?= htmlspecialchars($task['status']) ?>
                                                </span>
                                                <div class="small text-muted mt-1"><?= htmlspecialchars($task['time']) ?> hours</div>
                                                <?php if($_SESSION['user']['role'] === 'admin'): ?>
                                                    <a href="<?= $base_url ?>/tasks/edit/<?= $task['id'] ?>" 
                                                       class="btn btn-sm btn-outline-primary mt-1">Edit</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mt-3">No tasks yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>