<?php 
require_once __DIR__ . '/layouts/header.php';

// Get total counts
$totalProjects = count($projects);
$totalTasks = 0;
$completedTasks = 0;
$totalComments = 0;

foreach ($projects as $project) {
    if (isset($projectTasks[$project['id']])) {
        $projectTaskList = $projectTasks[$project['id']];
        $totalTasks += count($projectTaskList);
        foreach ($projectTaskList as $task) {
            if ($task['status'] === 'completed') {
                $completedTasks++;
            }
            $totalComments += count($commentModel->getCommentsByTaskId($task['id']));
        }
    }
}

$completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
?>

<div class="container">
    <div class="px-4 py-5 my-5 text-center">
        <h1 class="display-4 fw-bold">Project Management System</h1>
        <div class="col-lg-6 mx-auto">
            <p class="lead mb-4">Track projects, manage tasks, and collaborate with your team efficiently.</p>
        </div>
    </div>



    <!-- Feature Matrix -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4">Features</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <!-- Project Management -->
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary">
                                <i class="bi bi-folder"></i> Project Management
                            </h5>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle text-success"></i> Create and edit projects</li>
                                <li><i class="bi bi-check-circle text-success"></i> Project description with rich text</li>
                                <li><i class="bi bi-check-circle text-success"></i> Project progress tracking</li>
                                <li><i class="bi bi-check-circle text-success"></i> Last updated timestamps</li>
                                <li><i class="bi bi-check-circle text-success"></i> Project analytics</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Task Management -->
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-success">
                                <i class="bi bi-list-check"></i> Task Management
                            </h5>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle text-success"></i> Create and edit tasks</li>
                                <li><i class="bi bi-check-circle text-success"></i> Hierarchical task structure</li>
                                <li><i class="bi bi-check-circle text-success"></i> Task status tracking</li>
                                <li><i class="bi bi-check-circle text-success"></i> Due date management</li>
                                <li><i class="bi bi-check-circle text-success"></i> Time tracking</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Collaboration -->
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-info">
                                <i class="bi bi-people"></i> Collaboration
                            </h5>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle text-success"></i> Comment system</li>
                                <li><i class="bi bi-check-circle text-success"></i> File attachments</li>
                                <li><i class="bi bi-check-circle text-success"></i> Activity tracking</li>
                                <li><i class="bi bi-check-circle text-success"></i> User roles</li>
                                <li><i class="bi bi-check-circle text-success"></i> Task assignments</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Analytics -->
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-warning">
                                <i class="bi bi-graph-up"></i> Analytics
                            </h5>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle text-success"></i> Project progress metrics</li>
                                <li><i class="bi bi-check-circle text-success"></i> Task completion rates</li>
                                <li><i class="bi bi-check-circle text-success"></i> Due date tracking</li>
                                <li><i class="bi bi-check-circle text-success"></i> Time spent analysis</li>
                                <li><i class="bi bi-check-circle text-success"></i> Activity monitoring</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- User Management -->
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-danger">
                                <i class="bi bi-person-gear"></i> User Management
                            </h5>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle text-success"></i> User authentication</li>
                                <li><i class="bi bi-check-circle text-success"></i> Role-based access control</li>
                                <li><i class="bi bi-check-circle text-success"></i> Admin privileges</li>
                                <li><i class="bi bi-check-circle text-success"></i> User activity tracking</li>
                                <li><i class="bi bi-check-circle text-success"></i> Secure sessions</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Interface -->
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-purple">
                                <i class="bi bi-window"></i> Interface
                            </h5>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle text-success"></i> Responsive design</li>
                                <li><i class="bi bi-check-circle text-success"></i> Rich text editor</li>
                                <li><i class="bi bi-check-circle text-success"></i> Interactive dashboards</li>
                                <li><i class="bi bi-check-circle text-success"></i> Progress visualization</li>
                                <li><i class="bi bi-check-circle text-success"></i> Intuitive navigation</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Projects -->
    <h2 class="mb-4">Recent Projects</h2>
    <div class="row row-cols-1 row-cols-md-2 g-4 mb-5">
        <?php 
        $recentProjects = array_slice($projects, 0, 4);
        foreach ($recentProjects as $project): 
        ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="<?= $base_url ?>/projects/view/<?= $project['id'] ?>" 
                               class="text-decoration-none">
                                <?= htmlspecialchars($project['name']) ?>
                            </a>
                        </h5>
                        <p class="card-text"><?= htmlspecialchars($project['description']) ?></p>
                        <?php 
                        $taskCount = !empty($projectTasks[$project['id']]) ? count($projectTasks[$project['id']]) : 0;
                        ?>
                        <span class="badge bg-secondary"><?= $taskCount ?> tasks</span>
                    </div>
                    <div class="card-footer bg-transparent">
                        <small class="text-muted">Last updated: <?= htmlspecialchars($project['last_updated']) ?> <?= TimeHelper::getRelativeTime($project['last_updated']) ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
