<section class="card">
  <h1>Set a new password</h1>
  <?php if (!empty($error)): ?><p class="alert"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="post" action="<?= base_url('reset_password') ?>">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
    <label>New password <input type="password" name="password" minlength="6" required></label>
    <input type="hidden" name="<?= (require __DIR__.'/../../../config.php')['security']['csrf_key'] ?>" value="<?= Auth::csrfToken() ?>">
    <button class="btn" type="submit">Reset password</button>
  </form>
</section>