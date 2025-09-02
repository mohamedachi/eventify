<section class="card">
  <h1>New user</h1>
  <form method="post" action="<?= base_url('users/store') ?>" onsubmit="return validateUser(this)">
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
    <button class="btn">Create</button>
  </form>
</section>