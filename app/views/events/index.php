<section>
  <div class="hero">
    <h1>Discover upcoming events</h1>
    <p>Concerts, meetups, and more.</p>
    <div class="controls"><input id="searchEvents" type="text" placeholder="Search events..." onkeyup="filterEvents()" class="search"><select id="statusFilter" onchange="filterEvents()"><option value="">All</option><option value="published">Published</option><option value="pending">Pending</option><option value="draft">Draft</option></select></div>
  </div>
  <div class="grid">
    <?php foreach ($events as $e): ?>
      <article class="card" data-status="<?= htmlspecialchars($e['status'] ?? 'published') ?>">
        <?php if (!empty($e['image'])): ?><div class="thumb"><img src="<?= base_url($e['image']) ?>" alt="<?= htmlspecialchars($e['title']) ?>"></div><?php endif; ?>
        <h3><?= htmlspecialchars($e['title']) ?></h3>
        <p class="muted">@ <?= htmlspecialchars($e['location']) ?> • <?= htmlspecialchars($e['event_date']) ?></p>
        <p><?= nl2br(htmlspecialchars(substr($e['description'],0,120))) ?>...</p>
        <div class="row">
          <a class="btn" href="<?= base_url('events/show?id='.$e['id']) ?>">Details</a>
          <span class="price">$<?= number_format((float)$e['price'],2) ?></span>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>