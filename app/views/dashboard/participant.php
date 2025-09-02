<section>
  <h1>My events</h1>
  <?php if (empty($events)): ?>
    <p>You have not joined any events yet.</p>
  <?php endif; ?>
  <ul>
    <?php foreach ($events as $e): ?>
      <li><a href="<?= base_url('events/show?id='.$e['id']) ?>"><?= htmlspecialchars($e['title']) ?></a> — <?= htmlspecialchars($e['event_date']) ?></li>
    <?php endforeach; ?>
  </ul>
</section>