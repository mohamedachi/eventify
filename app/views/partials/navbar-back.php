<header class="navbar back-nav">
  <a class="brand" href="<?= base_url('dashboard') ?>">⚙️ Back Office</a>
  <nav>
    <a href="<?= base_url('dashboard') ?>">Home</a>
    <?php if (Auth::role() === 'admin'): ?>
    <a href="<?= base_url('users') ?>">Users</a>
    <?php endif; ?>
    <a href="<?= base_url('events') ?>">Front</a>
    <form method="post" action="<?= base_url('logout') ?>" class="inline">
      <input type="hidden" name="<?= (require __DIR__.'/../../../config.php')['security']['csrf_key'] ?>" value="<?= Auth::csrfToken() ?>">
      <button type="submit" class="btn">Logout</button>
    </form>
  </nav>
</header>