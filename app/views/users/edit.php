<section class="card">
  <h1>Edit user</h1>
  <form method="post" action="<?= base_url('users/update') ?>" onsubmit="return validateUser(this)">
    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
    <label>Name <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required></label>
    <label>Email <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></label>
    <label>Password <input type="password" name="password" minlength="6" placeholder="Leave blank to keep current"></label>
    <label>Role
      <select name="role" required>
        <option <?= $user['role']==='participant'?'selected':'' ?> value="participant">Participant</option>
        <option <?= $user['role']==='organizer'?'selected':'' ?> value="organizer">Organizer</option>
        <option <?= $user['role']==='admin'?'selected':'' ?> value="admin">Admin</option>
      </select>
    </label>
    <input type="hidden" name="<?= (require __DIR__.'/../../../config.php')['security']['csrf_key'] ?>" value="<?= Auth::csrfToken() ?>">
    <button class="btn">Update</button>
  </form>
</section>