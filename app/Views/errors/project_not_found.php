<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="display-1 text-muted">404</h1>
                    <h2 class="mb-4">Project Not Found</h2>
                    <p class="text-muted mb-4">The project you're looking for doesn't exist or may have been removed.</p>
                    <div class="mb-4">
                        <a href="<?= $base_url ?>/dashboard" class="btn btn-primary">Return to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 