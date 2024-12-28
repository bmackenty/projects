<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row">
        <!-- Profile Information and Edit Form -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Profile Settings</h5>
                </div>
                <div class="card-body">
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

                    <form action="<?= $base_url ?>/profile/update" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" 
                                   value="<?= htmlspecialchars($user['email']) ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">Change Password</h6>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" 
                                   name="current_password">
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" 
                                   name="new_password">
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirm" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="new_password_confirm" 
                                   name="new_password_confirm">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assigned Tasks -->
        <div class="col-md-8">
            <h2>Assigned Tasks</h2>
            <?php if (!empty($assignedTasks)): ?>
                <div class="list-group">
                    <?php foreach ($assignedTasks as $task): ?>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">
                                    <a href="<?= $base_url ?>/tasks/view/<?= $task['id'] ?>" 
                                       class="text-decoration-none">
                                        <?= htmlspecialchars($task['name']) ?>
                                    </a>
                                </h5>
                                <small class="text-muted">
                                    in <a href="<?= $base_url ?>/projects/view/<?= $task['project_id'] ?>">
                                        <?= htmlspecialchars($task['project_name']) ?>
                                    </a>
                                </small>
                            </div>
                            <p class="mb-1"><?= substr(htmlspecialchars($task['description']), 0, 100) ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-<?= $task['status'] === 'completed' ? 'success' :
                                    ($task['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
                                    <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                </span>
                                <?php if ($task['due_date']): ?>
                                    <small class="text-muted">Due: <?= htmlspecialchars($task['due_date']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">No tasks assigned yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>