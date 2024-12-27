<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Projects</h1>
        <?php if($_SESSION['user']['role'] === 'admin'): ?>
            <a href="<?= $base_url ?>/projects/create" class="btn btn-primary">Add New Project</a>
        <?php endif; ?>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Tasks</th>
                <th>Last Updated</th>
                <?php if($_SESSION['user']['role'] === 'admin'): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
            <tr>
                <td><a href="<?= $base_url ?>/projects/view/<?= $project['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($project['name']) ?></a></td>
                <td><?= htmlspecialchars($project['description']) ?></td>
                <td>
                    <?php 
                    $taskCount = !empty($projectTasks[$project['id']]) ? count($projectTasks[$project['id']]) : 0;
                    ?>
                    <div class="d-flex flex-column align-items-start">
                        <span class="badge bg-secondary mb-2"><?= $taskCount ?> tasks</span>
                        <a href="<?= $base_url ?>/projects/<?= $project['id'] ?>/tasks/create" 
                           class="btn btn-sm btn-outline-primary">Add Task</a>
                    </div>
                </td>
                <td><?= htmlspecialchars($project['last_updated']) ?></td>
                <?php if($_SESSION['user']['role'] === 'admin'): ?>
                    <td>
                        <a href="<?= $base_url ?>/projects/edit/<?= $project['id'] ?>" 
                           class="btn btn-sm btn-primary">Edit</a>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>