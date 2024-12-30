<div class="mt-3">
    <?php if (isset($_SESSION['user'])): ?>
        <?php 
        $comments = $commentModel->getCommentsByTaskId($task['id']);
        $hasComments = !empty($comments);
        ?>
        
        <?php if (!$hasComments): ?>
            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#comments-<?= $task['id'] ?>">
                Be the first to comment
            </button>
        <?php endif; ?>
        
        <div class="<?= $hasComments ? '' : 'collapse' ?> mt-2" id="comments-<?= $task['id'] ?>">
            <?php if ($hasComments): ?>
                <?php foreach ($comments as $comment): ?>
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
                            
                            <!-- Reply button -->
                            <div class="mt-2">
                                <button class="btn btn-sm btn-link p-0 reply-trigger" 
                                        data-comment-id="<?= $comment['id'] ?>">
                                    Reply
                                </button>
                            </div>

                            <!-- Reply form (hidden by default) -->
                            <div class="reply-form mt-2 d-none" id="reply-form-<?= $comment['id'] ?>">
                                <form method="POST" action="<?= $base_url ?>/tasks/<?= $task['id'] ?>/comment">
                                    <input type="hidden" name="parent_id" value="<?= $comment['id'] ?>">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" name="content" 
                                               placeholder="Write a reply..." required>
                                        <button class="btn btn-primary btn-sm" type="submit">Reply</button>
                                        <button type="button" class="btn btn-light btn-sm cancel-reply">Cancel</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Replies -->
                            <?php if (!empty($comment['replies'])): ?>
                                <div class="ms-4 mt-2">
                                    <?php foreach ($comment['replies'] as $reply): ?>
                                        <div class="card mb-2">
                                            <div class="card-body py-2">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="card-subtitle mb-1 text-muted small">
                                                        <?= htmlspecialchars($reply['user_name']) ?>
                                                    </h6>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars($reply['created_at']) ?>
                                                    </small>
                                                </div>
                                                <p class="card-text small mb-0">
                                                    <?= htmlspecialchars($reply['content']) ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Main comment form -->
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle reply button clicks
    document.querySelectorAll('.reply-trigger').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            replyForm.classList.remove('d-none');
            this.classList.add('d-none');
        });
    });

    // Handle cancel reply button clicks
    document.querySelectorAll('.cancel-reply').forEach(button => {
        button.addEventListener('click', function() {
            const replyForm = this.closest('.reply-form');
            replyForm.classList.add('d-none');
            replyForm.previousElementSibling.querySelector('.reply-trigger').classList.remove('d-none');
        });
    });
});
</script> 