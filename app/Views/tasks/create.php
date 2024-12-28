<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Add New Task</h2>
                </div>
                <div class="card-body">
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/projects/<?= $project_id ?>/tasks/create" id="createTaskForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Task Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="wysiwyg-editor form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="parent_task_id" class="form-label">Parent Task (Optional)</label>
                            <select class="form-control" id="parent_task_id" name="parent_task_id">
                                <option value="">No Parent Task</option>
                                <?php foreach ($projectTasks as $projectTask): ?>
                                    <option value="<?= $projectTask['id'] ?>">
                                        <?= htmlspecialchars($projectTask['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="time" class="form-label">Time Spent (hours)</label>
                            <input type="number" class="form-control" id="time" name="time" step="0.5" min="0" value="0" required>
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date (Optional)</label>
                            <input type="date" class="form-control" id="due_date" name="due_date">
                        </div>

                        <div class="mb-3">
                            <label for="assigned_user" class="form-label">Assign To (Optional)</label>
                            <select class="form-control" id="assigned_user" name="assigned_user">
                                <option value="">Unassigned</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>">
                                        <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Create Task</button>
                            <a href="/projects" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<?php require_once __DIR__ . '/../layouts/footer.php'; ?>