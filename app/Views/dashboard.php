<?php require_once dirname(__DIR__) . '/Views/layouts/header.php'; ?>

<div class="container">
    <h1 class="mb-4">Project Analytics Dashboard</h1>

    <!-- Summary Cards Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Projects</h6>
                    <h2 class="mb-0"><?= count($projects) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <?php
                    $totalTasks = 0;
                    foreach ($projectTasks as $tasks) {
                        $totalTasks += count($tasks);
                    }
                    ?>
                    <h6 class="card-title">Total Tasks</h6>
                    <h2 class="mb-0"><?= $totalTasks ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <?php
                    $inProgressTasks = 0;
                    foreach ($projectTasks as $tasks) {
                        foreach ($tasks as $task) {
                            if ($task['status'] === 'in_progress') {
                                $inProgressTasks++;
                            }
                        }
                    }
                    ?>
                    <h6 class="card-title">In Progress</h6>
                    <h2 class="mb-0"><?= $inProgressTasks ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <?php
                    $completedTasks = 0;
                    foreach ($projectTasks as $tasks) {
                        foreach ($tasks as $task) {
                            if ($task['status'] === 'completed') {
                                $completedTasks++;
                            }
                        }
                    }
                    $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                    ?>
                    <h6 class="card-title">Completion Rate</h6>
                    <h2 class="mb-0"><?= $completionRate ?>%</h2>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Add the new due date statistics here
    $overdueTasks = 0;
    $upcomingTasks = 0;
    $noDateTasks = 0;
    $today = new DateTime();

    foreach ($projects as $project) {
        if (isset($projectTasks[$project['id']])) {
            foreach ($projectTasks[$project['id']] as $task) {
                if ($task['status'] !== 'completed') {  // Only count non-completed tasks
                    if (!$task['due_date']) {
                        $noDateTasks++;
                    } else {
                        $dueDate = new DateTime($task['due_date']);
                        if ($today > $dueDate) {
                            $overdueTasks++;
                        } else {
                            $upcomingTasks++;
                        }
                    }
                }
            }
        }
    }
    ?>

    <!-- Task Status Distribution -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Task Status Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="taskStatusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Due Date Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="dueDateChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Timeline -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Project Timeline</h5>
                </div>
                <div class="card-body">
                    <?php
                    $oldestTask = null;
                    $newestTask = null;
                    foreach ($projectTasks as $project_id => $tasks) {
                        foreach ($tasks as $task) {
                            if (!$oldestTask || strtotime($task['last_updated']) < strtotime($oldestTask['last_updated'])) {
                                $oldestTask = $task;
                                $oldestTask['project_id'] = $project_id;
                            }
                            if (!$newestTask || strtotime($task['last_updated']) > strtotime($newestTask['last_updated'])) {
                                $newestTask = $task;
                                $newestTask['project_id'] = $project_id;
                            }
                        }
                    }
                    ?>
                    <div class="mb-3">
                        <h6 class="text-muted">Newest Task</h6>
                        <?php if ($newestTask): ?>
                            <p class="mb-1">
                                <a href="<?= $base_url ?>/tasks/view/<?= $newestTask['id'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($newestTask['name']) ?>
                                </a>
                                <br>
                                <small class="text-muted">
                                    in <a href="<?= $base_url ?>/projects/view/<?= $newestTask['project_id'] ?>" class="text-muted">
                                        <?= htmlspecialchars($projects[array_search($newestTask['project_id'], array_column($projects, 'id'))]['name']) ?>
                                    </a>
                                </small>
                            </p>
                            <small class="text-muted">Updated <?= TimeHelper::getRelativeTime($newestTask['last_updated']) ?></small>
                        <?php else: ?>
                            <p class="text-muted">No tasks yet</p>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted">Oldest Task</h6>
                        <?php if ($oldestTask): ?>
                            <p class="mb-1">
                                <a href="<?= $base_url ?>/tasks/view/<?= $oldestTask['id'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($oldestTask['name']) ?>
                                </a>
                                <br>
                                <small class="text-muted">
                                    in <a href="<?= $base_url ?>/projects/view/<?= $oldestTask['project_id'] ?>" class="text-muted">
                                        <?= htmlspecialchars($projects[array_search($oldestTask['project_id'], array_column($projects, 'id'))]['name']) ?>
                                    </a>
                                </small>
                            </p>
                            <small class="text-muted">Updated <?= TimeHelper::getRelativeTime($oldestTask['last_updated']) ?></small>
                        <?php else: ?>
                            <p class="text-muted">No tasks yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Task Due Dates Overview</h5>
                </div>
                <div class="card-body">
                    <?php
                    $upcomingDeadlines = [];
                    foreach ($projectTasks as $project_id => $tasks) {
                        foreach ($tasks as $task) {
                            if ($task['due_date'] && $task['status'] !== 'completed') {
                                $dueDate = new DateTime($task['due_date']);
                                $today = new DateTime();
                                $interval = $today->diff($dueDate);
                                $daysUntilDue = $interval->days * ($interval->invert ? -1 : 1);
                                
                                if ($daysUntilDue >= -7 && $daysUntilDue <= 14) { // Show tasks due within 2 weeks or overdue up to a week
                                    $upcomingDeadlines[] = [
                                        'task' => $task,
                                        'project_id' => $project_id,
                                        'days' => $daysUntilDue
                                    ];
                                }
                            }
                        }
                    }

                    // Sort by due date
                    usort($upcomingDeadlines, function($a, $b) {
                        return $a['days'] - $b['days'];
                    });

                    if (!empty($upcomingDeadlines)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach (array_slice($upcomingDeadlines, 0, 5) as $deadline): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <a href="<?= $base_url ?>/tasks/view/<?= $deadline['task']['id'] ?>" 
                                               class="text-decoration-none">
                                                <?= htmlspecialchars($deadline['task']['name']) ?>
                                            </a>
                                        </h6>
                                        <?php if ($deadline['days'] < 0): ?>
                                            <span class="badge bg-danger">Overdue by <?= abs($deadline['days']) ?> days</span>
                                        <?php elseif ($deadline['days'] == 0): ?>
                                            <span class="badge bg-warning">Due today</span>
                                        <?php else: ?>
                                            <span class="badge bg-info">Due in <?= $deadline['days'] ?> days</span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">
                                        in <a href="<?= $base_url ?>/projects/view/<?= $deadline['project_id'] ?>" class="text-muted">
                                            <?= htmlspecialchars($projects[array_search($deadline['project_id'], array_column($projects, 'id'))]['name']) ?>
                                        </a>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No upcoming deadlines</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recent Updates</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Last Task Updated</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recentProjects = array_slice($projects, 0, 5);
                        foreach ($recentProjects as $project):
                            $lastUpdatedTask = null;
                            if (!empty($projectTasks[$project['id']])) {
                                $projectTasksList = $projectTasks[$project['id']];
                                usort($projectTasksList, function($a, $b) {
                                    return strtotime($b['last_updated']) - strtotime($a['last_updated']);
                                });
                                $lastUpdatedTask = $projectTasksList[0];
                            }
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($project['name']) ?></td>
                                <td>
                                    <?php if ($lastUpdatedTask): ?>
                                        <?= htmlspecialchars($lastUpdatedTask['name']) ?>
                                        <br>
                                        <small class="text-muted">
                                            <?= TimeHelper::getRelativeTime($lastUpdatedTask['last_updated']) ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">No tasks</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($lastUpdatedTask): ?>
                                        <span class="badge bg-<?= $lastUpdatedTask['status'] === 'completed' ? 'success' :
                                            ($lastUpdatedTask['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
                                            <?= ucfirst(str_replace('_', ' ', $lastUpdatedTask['status'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $lastUpdatedTask ? $lastUpdatedTask['time'] . ' hours' : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Task Status Distribution Chart
    const statusCtx = document.getElementById('taskStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'In Progress', 'Pending'],
            datasets: [{
                data: [<?= $completedTasks ?>, <?= $inProgressTasks ?>, 
                       <?= $totalTasks - ($completedTasks + $inProgressTasks) ?>],
                backgroundColor: ['#198754', '#ffc107', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Due Date Distribution Chart
    const dueDateCtx = document.getElementById('dueDateChart').getContext('2d');
    new Chart(dueDateCtx, {
        type: 'pie',
        data: {
            labels: ['Overdue', 'Upcoming', 'No Due Date'],
            datasets: [{
                data: [<?= $overdueTasks ?>, <?= $upcomingTasks ?>, <?= $noDateTasks ?>],
                backgroundColor: ['#dc3545', '#0dcaf0', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>