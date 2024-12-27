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

    <div class="accordion" id="projectAccordion">
        <?php foreach ($projects as $project): ?>
            <div class="accordion-item mb-3 border">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#project-<?= $project['id'] ?>">
                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                            <span><?= htmlspecialchars($project['name']) ?></span>
                            <span class="badge bg-secondary">
                                <?= !empty($projectTasks[$project['id']]) ? count($projectTasks[$project['id']]) : 0 ?> tasks
                            </span>
                        </div>
                    </button>
                </h2>
                <div id="project-<?= $project['id'] ?>" class="accordion-collapse collapse" 
                     data-bs-parent="#projectAccordion">
                    <div class="accordion-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-muted small">Last updated: <?= TimeHelper::getRelativeTime($project['last_updated']) ?></p>
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
                                                <p class="mb-1 small text-muted"><?= $task['description'] ?></p>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-<?= $task['status'] === 'completed' ? 'success' : 
                                                    ($task['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
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
                            <p class="text-muted">No tasks yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Statistics Panel -->
    <div class="card mt-4 border-0 bg-light">
        <div class="card-body">
            <h3 class="h5 mb-4">Project Statistics</h3>
            <div class="row g-4">
                <!-- Project Stats -->
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-folder text-primary fs-3"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Total Projects</h6>
                                    <h3 class="mb-0"><?= count($projects) ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Task Stats -->
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-list-check text-success fs-3"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Total Tasks</h6>
                                    <h3 class="mb-0">
                                        <?php
                                        $totalTasks = 0;
                                        foreach ($projectTasks as $tasks) {
                                            $totalTasks += count($tasks);
                                        }
                                        echo $totalTasks;
                                        ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Completed Tasks -->
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-check-circle text-info fs-3"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Completed Tasks</h6>
                                    <h3 class="mb-0">
                                        <?php
                                        $completedTasks = 0;
                                        foreach ($projectTasks as $tasks) {
                                            foreach ($tasks as $task) {
                                                if ($task['status'] === 'completed') {
                                                    $completedTasks++;
                                                }
                                            }
                                        }
                                        echo $completedTasks;
                                        ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Completion Rate -->
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-graph-up text-warning fs-3"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Completion Rate</h6>
                                    <h3 class="mb-0">
                                        <?= $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 ?>%
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>