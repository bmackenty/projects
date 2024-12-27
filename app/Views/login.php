<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 col-lg-4">
            <?php if(isset($_SESSION['user'])): ?>
                <div class="card shadow">
                    <div class="card-body text-center">
                        <h3 class="card-title mb-4">Already Logged In</h3>
                        <p>You are already logged in as <strong><?= htmlspecialchars($_SESSION['user']['email']) ?></strong></p>
                        <div class="mt-4">
                            <a href="<?= $base_url ?>/dashboard" class="btn btn-primary">Go to Dashboard</a>
                            <a href="<?= $base_url ?>/logout" class="btn btn-outline-secondary ms-2">Logout</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Login</h3>
                        
                        <?php if(isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?= $_SESSION['error'] ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= $base_url ?>/login" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="<?= $base_url?>/forgot" class="text-decoration-none">Forgot Password?</a>
                        </div>
                        <div class="text-center mt-2">
                            <span>Don't have an account? </span>
                            <a href="<?= $base_url?>/register" class="text-decoration-none">Register here</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>