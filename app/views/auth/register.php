<section class="card">
  <h1>Create account</h1>
  <?php if (!empty($error)): ?><p class="alert"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="post" action="<?= base_url('register') ?>" onsubmit="return validateRegister(this)">
    <label>Name <input type="text" name="name" required></label>
    <label>Email <input type="email" name="email" required></label>
    <label>Password <input type="password" name="password" minlength="6" required></label>
    <label>Role
      <select name="role" required>
        <option value="participant">Participant</option>
        <option value="organizer">Organizer</option>
        <option value="admin">Admin</option>
      </select>
    </label>
    <input type="hidden" name="<?= (require __DIR__.'/../../../config.php')['security']['csrf_key'] ?>" value="<?= Auth::csrfToken() ?>">
    <button type="submit" class="btn">Register</button>
  </form>
</section>