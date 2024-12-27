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

    <!-- Statistics Cards -->
    <div class="row row-cols-1 row-cols-md-4 g-4 mb-5">
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary">Projects</h5>
                    <h2 class="card-text"><?= $totalProjects ?></h2>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-success">Tasks</h5>
                    <h2 class="card-text"><?= $totalTasks ?></h2>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-info">Comments</h5>
                    <h2 class="card-text"><?= $totalComments ?></h2>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-warning">Completion Rate</h5>
                    <h2 class="card-text"><?= $completionRate ?>%</h2>
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
