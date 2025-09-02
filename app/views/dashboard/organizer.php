<section>
  <h1>Organizer dashboard</h1>
  <a class="btn" href="<?= base_url('events/create') ?>">Create event</a>
  <div class="grid">
    <?php foreach ($events as $e): ?>
      <article class="card">
        <h3><?= htmlspecialchars($e['title']) ?></h3>
        <p class="muted"><?= htmlspecialchars($e['event_date']) ?> — <?= htmlspecialchars($e['location']) ?></p>
        <a class="btn" href="<?= base_url('events/edit?id='.$e['id']) ?>">Edit</a>
      </article>
    <?php endforeach; ?>
  </div>
</section>