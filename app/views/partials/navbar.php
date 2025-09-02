<header class="navbar">
  <a class="brand" href="<?= base_url() ?>">🎟️ Eventify</a>
  <nav>
    <a href="<?= base_url('events') ?>">Events</a>
    <?php if (Auth::check()): ?>
      <a href="<?= base_url('dashboard') ?>">Dashboard</a>
      <form method="post" action="<?= base_url('logout') ?>" class="inline">
        <input type="hidden" name="<?= (require __DIR__.'/../../../config.php')['security']['csrf_key'] ?>" value="<?= Auth::csrfToken() ?>">
        <button type="submit" class="btn">Logout</button>
      </form>
    <?php else: ?>
      <a href="<?= base_url('login') ?>">Login</a>
      <a href="<?= base_url('register') ?>" class="btn">Register</a>
    <?php endif; ?>
  </nav>
</header>