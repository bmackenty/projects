<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <!-- Task Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= $base_url ?>/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a
                            href="<?= $base_url ?>/projects/view/<?= $project['id'] ?>"><?= htmlspecialchars($project['name']) ?></a>
                    </li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($task['name']) ?></li>
                </ol>
            </nav>
            <h1 class="mb-0">
                <?= htmlspecialchars($task['name']) ?>
                <?php if (!empty($uploads)): ?>
                    <i class="bi bi-paperclip text-muted small"></i>
                <?php endif; ?>
            </h1>
        </div>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <a href="<?= $base_url ?>/tasks/edit/<?= $task['id'] ?>" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit Task
            </a>
        <?php endif; ?>
    </div>

    <div class="row">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            <!-- Task Hierarchy -->
            <?php if (!empty($taskHierarchy)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Task Hierarchy</h5>
                    </div>
                    <div class="card-body">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <?php foreach ($taskHierarchy as $index => $hierarchyTask): ?>
                                    <?php if ($index === array_key_last($taskHierarchy)): ?>
                                        <li class="breadcrumb-item active"><?= htmlspecialchars($hierarchyTask['name']) ?></li>
                                    <?php else: ?>
                                        <li class="breadcrumb-item">
                                            <a href="<?= $base_url ?>/tasks/view/<?= $hierarchyTask['id'] ?>">
                                                <?= htmlspecialchars($hierarchyTask['name']) ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ol>
                        </nav>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Child Tasks -->
            <?php if (!empty($childTasks)): ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Subtasks</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($childTasks as $childTask): ?>
                            <a href="<?= $base_url ?>/tasks/view/<?= $childTask['id'] ?>" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= htmlspecialchars($childTask['name']) ?></h6>
                                    <span class="badge bg-<?= $childTask['status'] === 'completed' ? 'success' :
                                        ($childTask['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
                                        <?= ucfirst(str_replace('_', ' ', $childTask['status'])) ?>
                                    </span>
                                </div>
                                <small class="text-muted">Last updated: <?= $childTask['last_updated'] ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Task Description Card -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Description</h5>
                </div>
                <div class="card-body">


                    <div class="card-text mb-4"><?= $task['description'] ?></div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <h6 class="text-muted mb-1">Status</h6>
                            <span class="badge bg-<?= $task['status'] === 'completed' ? 'success' :
                                ($task['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
                                <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                            </span>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-1">Time Spent</h6>
                            <p class="mb-0"><?= htmlspecialchars($task['time']) ?> hours</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-1">Last Updated</h6>
                            <p class="mb-0"><?= TimeHelper::getRelativeTime($task['last_updated']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Comments</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['user'])): ?>
                        <form method="POST" action="<?= $base_url ?>/tasks/<?= $task['id'] ?>/comment" class="mb-4">
                            <div class="input-group">
                                <input type="text" class="form-control" name="content" placeholder="Add a comment..."
                                    required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-send"></i> Send
                                </button>
                            </div>
                        </form>

                        <?php
                        $comments = $commentModel->getCommentsByTaskId($task['id']);
                        if (!empty($comments)):
                            foreach ($comments as $comment):
                                ?>
                                <div class="d-flex mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-initial rounded-circle bg-light text-primary">
                                            <?= strtoupper(substr($comment['user_email'], 0, 1)) ?>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0"><?= htmlspecialchars($comment['user_email']) ?></h6>
                                            <small class="text-muted">
                                                <?= TimeHelper::getRelativeTime($comment['created_at']) ?>
                                            </small>
                                        </div>
                                        <p class="mb-0"><?= htmlspecialchars($comment['content']) ?></p>
                                    </div>
                                </div>
                            <?php
                            endforeach;
                        else:
                            ?>
                            <p class="text-muted text-center mb-0">No comments yet</p>
                        <?php
                        endif;
                        ?>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i>
                            Please <a href="<?= $base_url ?>/login" class="alert-link">login</a> to access comments.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <!-- Attachments Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Attachments</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['user'])): ?>
                        <form action="<?= $base_url ?>/tasks/<?= $task['id'] ?>/upload" method="POST"
                            enctype="multipart/form-data" class="mb-3">
                            <div class="input-group">
                                <input type="file" class="form-control form-control-sm" name="file"
                                    accept=".pdf,.png,.jpg,.jpeg" required>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-upload"></i>
                                </button>
                            </div>
                            <small class="text-muted">Allowed: PDF, PNG, JPG (max 5MB)</small>
                        </form>

                        <?php if (!empty($uploads)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($uploads as $upload): ?>
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark me-2"></i>
                                                <div>
                                                    <span class="filename-display">
                                                        <?= htmlspecialchars($upload['original_filename']) ?>
                                                    </span>
                                                    <form class="rename-form d-none"
                                                        action="<?= $base_url ?>/tasks/upload/<?= $upload['id'] ?>/rename"
                                                        method="POST">
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" class="form-control" name="new_filename"
                                                                value="<?= htmlspecialchars($upload['original_filename']) ?>"
                                                                required>
                                                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                                            <button type="button"
                                                                class="btn btn-secondary btn-sm cancel-rename">Cancel</button>
                                                        </div>
                                                    </form>
                                                    <small class="text-muted d-block">
                                                        <?= number_format($upload['file_size'] / 1024, 2) ?> KB
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-secondary rename-btn">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="<?= $base_url ?>/uploads/<?= $upload['filename'] ?>"
                                                    class="btn btn-outline-primary" target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No attachments yet</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i>
                            Please <a href="<?= $base_url ?>/login" class="alert-link">login</a> to access attachments.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.rename-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const item = this.closest('.list-group-item');
                item.querySelector('.filename-display').classList.add('d-none');
                item.querySelector('.rename-form').classList.remove('d-none');
                this.classList.add('d-none');
            });
        });

        document.querySelectorAll('.cancel-rename').forEach(btn => {
            btn.addEventListener('click', function () {
                const item = this.closest('.list-group-item');
                item.querySelector('.filename-display').classList.remove('d-none');
                item.querySelector('.rename-form').classList.add('d-none');
                item.querySelector('.rename-btn').classList.remove('d-none');
            });
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>