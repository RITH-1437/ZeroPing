<?php

use App\Support\Flash;

?>

<div class="container">
    <div class="card">

        <h1>Login</h1>

        <?php if (Flash::has()):
            $flash = Flash::get();
            ?>

            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="/login">

            <div class="form-group">
                <label>Email</label>

                <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                >
            </div>

            <div class="form-group">
                <label>Password</label>

                <input
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                >
            </div>

            <button type="submit">
                Login
            </button>

        </form>

        <p class="text-center" style="margin-top:20px">
            Don't have an account?
            <a href="/register">Register</a>
        </p>

    </div>
</div>