<?php require_once dirname(__DIR__) . '/Views/layouts/header.php'; ?>

<div class="container">
    <h1 class="mb-4">Dashboard</h1>

    <div class="accordion" id="projectAccordion">
        <?php foreach ($projects as $project): ?>
            <div class="accordion-item mb-3 border">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#project-<?= $project['id'] ?>">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <span><?= htmlspecialchars($project['name']) ?></span>
                            <span class="badge bg-secondary ms-2 ">
                                <?= !empty($projectTasks[$project['id']]) ? count($projectTasks[$project['id']]) : 0 ?> tasks
                            </span>
                        </div>
                    </button>
                </h2>
                
                <div id="project-<?= $project['id'] ?>" class="accordion-collapse collapse" 
                     data-bs-parent="#projectAccordion">
                    <div class="accordion-body">
                        <div class="d-flex justify-content-between align-items-start mb-5">
                            <div>
                                <p class="text-muted small">Last updated: <?= $project['last_updated'] ?> <?= TimeHelper::getRelativeTime($project['last_updated']) ?></p>
                                <p><?= $project['description'] ?></p>
                            </div>
                            <?php if($_SESSION['user']['role'] === 'admin'): ?>
                                <a href="<?= $base_url ?>/projects/<?= $project['id'] ?>/tasks/create" 
                                   class="btn btn-sm btn-primary">Add Task</a>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($projectTasks[$project['id']])): ?>
                            <div class="list-group">
                                <?php foreach ($projectTasks[$project['id']] as $task): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="<?= $base_url ?>/tasks/view/<?= $task['id'] ?>" 
                                                       class="text-decoration-none">
                                                        <?= htmlspecialchars($task['name']) ?>
                                                    </a>
                                                </h6>
                                                <p class="mb-1 small text-muted"><?= htmlspecialchars($task['description']) ?></p>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-<?= $task['status'] === 'completed' ? 'success' :
                                                    ($task['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No tasks yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>