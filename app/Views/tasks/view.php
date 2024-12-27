<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>
                <?= htmlspecialchars($task['name']) ?>
                <?php if (!empty($uploads)): ?>
                    <i class="bi bi-paperclip text-muted small"></i>
                <?php endif; ?>
            </h1>
            <p class="text-muted">
                Project: <a href="<?= $base_url ?>/projects/view/<?= $project['id'] ?>"><?= htmlspecialchars($project['name']) ?></a>
            </p>
        </div>
        <?php if($_SESSION['user']['role'] === 'admin'): ?>
            <a href="<?= $base_url ?>/tasks/edit/<?= $task['id'] ?>" class="btn btn-primary">Edit Task</a>
        <?php endif; ?>
    </div>

    <div class="card mb-4">
        <div class="card-body">
        <h5 class="card-title">Task Description</h5>
        <div class="card-text"><?= $task['description'] ?></div>
            
            <div class="row mt-4">
                <div class="col-md-3">
                    <h6>Status</h6>
                    <span class="badge bg-<?= $task['status'] === 'completed' ? 'success' : 
                        ($task['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
                        <?= htmlspecialchars($task['status']) ?>
                    </span>
                </div>
                <div class="col-md-3">
                    <h6>Time Spent</h6>
                    <p><?= htmlspecialchars($task['time']) ?> hours</p>
                </div>
                <div class="col-md-3">
                    <h6>Last Updated</h6>
                    <p><?= htmlspecialchars($task['last_updated']) ?> <?= TimeHelper::getRelativeTime($task['last_updated']) ?></p>
                </div>
                <div class="col-md-3">
                    <h6>Attachments</h6>
                    <?php if (isset($_SESSION['user'])): ?>
                    <?php 
                    $uploads = $uploadModel->getUploadsByTaskId($task['id']);
                    
                    // Debug output
                    // echo "<!-- Debug: Task ID = " . $task['id'] . " -->";
                    // echo "<!-- Debug: Upload count = " . count($uploads) . " -->";
                    
                    if (!empty($uploads)): 
                    ?>
                        <div class="list-group">
                            <?php foreach ($uploads as $upload): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-file-earmark"></i>
                                        <span class="filename-display"><?= htmlspecialchars($upload['original_filename']) ?></span>
                                        <form class="rename-form d-none" 
                                              action="<?= $base_url ?>/tasks/upload/<?= $upload['id'] ?>/rename" 
                                              method="POST">
                                            <div class="input-group input-group-sm" style="max-width: 300px;">
                                                <input type="text" class="form-control" name="new_filename" 
                                                       value="<?= htmlspecialchars($upload['original_filename']) ?>" required>
                                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                                <button type="button" class="btn btn-secondary btn-sm cancel-rename">Cancel</button>
                                            </div>
                                        </form>
                                        <small class="text-muted">
                                            (<?= number_format($upload['file_size'] / 1024, 2) ?> KB)
                                        </small>
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-secondary rename-btn">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="<?= $base_url ?>/uploads/<?= $upload['filename'] ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           target="_blank">View</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="mb-0">
                            <span class="badge bg-secondary">No attachments</span>
                        </p>
                    <?php endif; ?>
                    <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Please <a href="<?= $base_url ?>/login" class="alert-link">login</a> to access attachments.
                </div>
            <?php endif; ?>

               



                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="h5 mb-0">Comments</h3>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['user'])): ?>
                <?php 
                $comments = $commentModel->getCommentsByTaskId($task['id']);
                foreach ($comments as $comment): 
                ?>
                    <div class="card mb-2">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between">
                                <h6 class="card-subtitle mb-1 text-muted small">
                                    <?= htmlspecialchars($comment['user_name']) ?>
                                </h6>
                                <small class="text-muted">
                                    <?= htmlspecialchars($comment['created_at']) ?>
                                </small>
                            </div>
                            <p class="card-text small mb-0"><?= htmlspecialchars($comment['content']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <form method="POST" action="<?= $base_url ?>/tasks/<?= $task['id'] ?>/comment" class="mt-2">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" name="content" 
                               placeholder="Add a comment..." required>
                        <button class="btn btn-sm btn-primary" type="submit">Send</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Please <a href="<?= $base_url ?>/login" class="alert-link">login</a> to access comments.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="h5 mb-0">Attachments</h3>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['user'])): ?>
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= $base_url ?>/tasks/<?= $task['id'] ?>/upload" 
                      method="POST" enctype="multipart/form-data" class="mb-3">
                    <div class="input-group">
                        <input type="file" class="form-control" name="file" 
                               accept=".pdf,.png,.jpg,.jpeg" required>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Upload
                        </button>
                    </div>
                    <small class="text-muted">Allowed files: PDF, PNG, JPG, JPEG (max 5MB)</small>
                </form>

                <?php 
                $uploadModel = new Upload($pdo);
                $uploads = $uploadModel->getUploadsByTaskId($task['id']);
                if (!empty($uploads)): 
                ?>
                    <div class="list-group">
                        <?php foreach ($uploads as $upload): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-file-earmark"></i>
                                    <span class="filename-display"><?= htmlspecialchars($upload['original_filename']) ?></span>
                                    <form class="rename-form d-none" 
                                          action="<?= $base_url ?>/tasks/upload/<?= $upload['id'] ?>/rename" 
                                          method="POST">
                                                <div class="input-group input-group-sm" style="max-width: 300px;">
                                                    <input type="text" class="form-control" name="new_filename" 
                                                           value="<?= htmlspecialchars($upload['original_filename']) ?>" required>
                                                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                                    <button type="button" class="btn btn-secondary btn-sm cancel-rename">Cancel</button>
                                                </div>
                                            </form>
                                    <small class="text-muted">
                                        (<?= number_format($upload['file_size'] / 1024, 2) ?> KB)
                                    </small>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-secondary rename-btn">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="<?= $base_url ?>/uploads/<?= $upload['filename'] ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       target="_blank">View</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No attachments yet</p>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Please <a href="<?= $base_url ?>/login" class="alert-link">login</a> to access attachments.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="mb-5"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.rename-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const item = this.closest('.list-group-item');
            item.querySelector('.filename-display').classList.add('d-none');
            item.querySelector('.rename-form').classList.remove('d-none');
            this.classList.add('d-none');
        });
    });

    document.querySelectorAll('.cancel-rename').forEach(btn => {
        btn.addEventListener('click', function() {
            const item = this.closest('.list-group-item');
            item.querySelector('.filename-display').classList.remove('d-none');
            item.querySelector('.rename-form').classList.add('d-none');
            item.querySelector('.rename-btn').classList.remove('d-none');
        });
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>