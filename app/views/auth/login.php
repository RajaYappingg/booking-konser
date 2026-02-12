<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Login</h1>
                <p class="text-muted">Welcome back. Please sign in to continue.</p>

                <form action="<?= base_url('login') ?>" method="post" autocomplete="off">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            required
                            placeholder="you@example.com"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            required
                            minlength="6"
                            placeholder="Your password"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

                <div class="text-center mt-3">
                    <span class="text-muted">New here?</span>
                    <a href="<?= base_url('register') ?>">Create an account</a>
                </div>
            </div>
        </div>
    </div>
</div>
