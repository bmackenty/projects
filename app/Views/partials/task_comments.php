<div class="mt-3">
    <?php if (isset($_SESSION['user'])): ?>
        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" 
                data-bs-target="#comments-<?= $task['id'] ?>">
            Show Comments
        </button>
        
        <div class="collapse mt-2" id="comments-<?= $task['id'] ?>">
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
        </div>
    <?php else: ?>
        <?php require __DIR__ . '/login_prompt.php'; ?>
    <?php endif; ?>
</div> 