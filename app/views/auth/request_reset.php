<section class="card">
  <h1>Reset password</h1>
  <?php if (!empty($message)): ?><p class="alert"><?= htmlspecialchars($message) ?></p><?php endif; ?>
  <?php if (!empty($error)): ?><p class="alert"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="post" action="<?= base_url('password/send') ?>">
    <label>Email <input type="email" name="email" required></label>
    <input type="hidden" name="<?= (require __DIR__.'/../../../config.php')['security']['csrf_key'] ?>" value="<?= Auth::csrfToken() ?>">
    <button class="btn" type="submit">Send reset link</button>
  </form>
  
</section>