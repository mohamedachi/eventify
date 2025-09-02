<section class="card">
  <h1>Edit event</h1>
  <form method="post" action="<?= base_url('events/update') ?>" onsubmit="return validateEvent(this)" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= (int)$event['id'] ?>">
    <label>Title <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" required></label>
    <label>Description <textarea name="description" required><?= htmlspecialchars($event['description']) ?></textarea></label>
    <label>Location <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required></label>
    <label>Date <input type="datetime-local" name="event_date" value="<?= str_replace(' ', 'T', htmlspecialchars($event['event_date'])) ?>" required></label>
    <label>Price <input type="number" name="price" min="0" step="0.01" value="<?= htmlspecialchars($event['price']) ?>" required></label>
    <label>Capacity <input type="number" name="capacity" min="0" value="<?= (int)$event['capacity'] ?>"></label>
    <label>Image <input type="file" name="image" accept="image/*"></label>
    <label>Status <select name="status"><option value="draft" <?= $event["status"]==="draft"?"selected":"" ?>>Draft</option><option value="published" <?= $event["status"]==="published"?"selected":"" ?>>Published</option><option value="pending" <?= $event["status"]==="pending"?"selected":"" ?>>Pending</option><option value="archived" <?= $event["status"]==="archived"?"selected":"" ?>>Archived</option></select></label>
    <input type="hidden" name="<?= (require __DIR__.'/../../../config.php')['security']['csrf_key'] ?>" value="<?= Auth::csrfToken() ?>">
    <button type="submit" class="btn">Update</button>
  </form>
  <form method="post" action="<?= base_url('events/delete') ?>" onsubmit="return confirm('Delete event?')">
    <input type="hidden" name="id" value="<?= (int)$event['id'] ?>">
    <input type="hidden" name="<?= (require __DIR__.'/../../../config.php')['security']['csrf_key'] ?>" value="<?= Auth::csrfToken() ?>">
    <button type="submit" class="btn danger">Delete</button>
  </form>
</section>