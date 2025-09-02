<section class="card">
  <h1>Create event</h1>
  <form method="post" action="<?= base_url('events/store') ?>" onsubmit="return validateEvent(this)" enctype="multipart/form-data">
    <label>Title <input type="text" name="title" required></label>
    <label>Description <textarea name="description" required></textarea></label>
    <label>Location <input type="text" name="location" required></label>
    <label>Date <input type="datetime-local" name="event_date" required></label>
    <label>Price <input type="number" name="price" min="0" step="0.01" required></label>
    <label>Capacity <input type="number" name="capacity" min="0" value="0"></label>
    <label>Image <input type="file" name="image" accept="image/*"></label>
    <label>Status <select name="status"><option value="draft">Draft</option><option value="published" selected>Published</option><option value="pending">Pending</option><option value="archived">Archived</option></select></label>
    <input type="hidden" name="<?= (require __DIR__.'/../../../config.php')['security']['csrf_key'] ?>" value="<?= Auth::csrfToken() ?>">
    <button type="submit" class="btn">Save</button>
  </form>
</section>