<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Edit Task</h2>
                </div>
                <div class="card-body">
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Task Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($task['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="wysiwyg-editor form-control" id="description" name="description" 
                                    rows="10" required><?= htmlspecialchars($task['description']) ?></textarea>
                        </div>

                        <!-- Add parent task selection -->
                        <div class="mb-3">
                            <label for="parent_task_id" class="form-label">Parent Task (Optional)</label>
                            <select class="form-control" id="parent_task_id" name="parent_task_id">
                                <option value="">No Parent Task</option>
                                <?php foreach ($projectTasks as $projectTask): ?>
                                    <?php if ($projectTask['id'] !== $task['id']): ?>
                                        <option value="<?= $projectTask['id'] ?>" 
                                            <?= ($task['parent_task_id'] == $projectTask['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($projectTask['name']) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending" <?= $task['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="in_progress" <?= $task['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="completed" <?= $task['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="time" class="form-label">Time Spent (hours)</label>
                            <input type="number" class="form-control" id="time" name="time" 
                                   value="<?= htmlspecialchars($task['time']) ?>" step="0.5" min="0" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Update Task</button>
                            <a href="<?= $base_url ?>/projects" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Add comments section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="h5 mb-0">Comments</h3>
                </div>
                <div class="card-body">
                    <?php require __DIR__ . '/../partials/task_comments.php'; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>