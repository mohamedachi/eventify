<section class="card">
  <h1>Sign in</h1>
  <?php if (!empty($error)): ?><p class="alert"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="post" action="<?= base_url('login') ?>" onsubmit="return validateLogin(this)">
    <label>Email <input type="email" name="email" required></label>
    <label>Password <input type="password" name="password" minlength="6" required></label>
    <input type="hidden" name="<?= (require __DIR__.'/../../../config.php')['security']['csrf_key'] ?>" value="<?= Auth::csrfToken() ?>">
    <!-- Optional reCAPTCHA placeholder (render client-side if site key set) -->
    <button type="submit" class="btn">Login</button>
  </form>
  <p>No account? <a href="<?= base_url('register') ?>">Register</a></p>
  <p><a href="<?= base_url('password/request') ?>">Forgot password?</a></p>
</section>