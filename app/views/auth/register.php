<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Create Account</h1>
                <p class="text-muted">Join to start booking concerts.</p>

                <form action="<?= base_url('register') ?>" method="post" autocomplete="off">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            required
                            minlength="3"
                            maxlength="80"
                            placeholder="Your name"
                        >
                    </div>

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
                            placeholder="Create a password"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>

                <div class="text-center mt-3">
                    <span class="text-muted">Already have an account?</span>
                    <a href="<?= base_url('login') ?>">Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
