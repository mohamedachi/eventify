<section>
  <h1>Users</h1>
  <a class="btn" href="<?= base_url('users/create') ?>">New user</a>
  <table class="table">
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Blocked</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= (int)$u['id'] ?></td>
        <td><?= htmlspecialchars($u['name']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><span class="badge"><?= htmlspecialchars($u['role']) ?></span></td>
        <td><?= $u['blocked'] ? 'Yes' : 'No' ?></td>
        <td>
          <a class="btn small" href="<?= base_url('users/edit?id='.$u['id']) ?>">Edit</a>
          <form class="inline" method="post" action="<?= base_url('users/delete') ?>" onsubmit="return confirm('Delete user?')">
            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
            <input type="hidden" name="<?= (require __DIR__.'/../../../config.php')['security']['csrf_key'] ?>" value="<?= Auth::csrfToken() ?>">
            <button class="btn small danger">Delete</button>
          </form>
          <form class="inline" method="post" action="<?= base_url('users/block') ?>" onsubmit="return confirm('Toggle block for this user?')">
            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
            <input type="hidden" name="<?= (require __DIR__.'/../../../config.php')['security']['csrf_key'] ?>" value="<?= Auth::csrfToken() ?>">
            <button class="btn small" type="submit"><?= $u['blocked'] ? 'Unblock' : 'Block' ?></button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>