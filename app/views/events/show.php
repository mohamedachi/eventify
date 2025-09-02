<section class="card">
  <h1><?= htmlspecialchars($event['title']) ?></h1>
  <p class="muted">By <?= htmlspecialchars($event['organizer']) ?> — <?= htmlspecialchars($event['event_date']) ?> — <?= htmlspecialchars($event['location']) ?></p>
  <?php if (!empty($event['image'])): ?><div class="thumb"><img src="<?= base_url($event['image']) ?>" alt="<?= htmlspecialchars($event['title']) ?>"></div><?php endif; ?>
  <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
  <p class="price big">$<?= number_format((float)$event['price'],2) ?></p>

  <?php if (Auth::check()): ?>
    <form method="post" action="<?= base_url('participations/toggle') ?>">
      <input type="hidden" name="event_id" value="<?= (int)$event['id'] ?>">
      <input type="hidden" name="<?= (require __DIR__.'/../../../config.php')['security']['csrf_key'] ?>" value="<?= Auth::csrfToken() ?>">
      <button class="btn">Join / Leave</button>
    </form>
  <?php else: ?>
    <p><a class="btn" href="<?= base_url('login') ?>">Sign in to participate</a></p>
  <?php endif; ?>

  <h3>Participants</h3>
  <p class="muted">Capacity: <?= (int)($event['capacity'] ?? 0) ?> — Status: <span class="badge"><?= htmlspecialchars($event['status'] ?? 'published') ?></span></p>
  <?php if (Auth::role()==="organizer" || Auth::role()==="admin"): ?>
    <a class="btn" href="<?= base_url('participations/export?event_id='.$event['id']) ?>">Export CSV</a>
    <form method="post" action="<?= base_url('participations/checkin') ?>" style="display:inline;" class="inline">
      <input type="text" name="code" placeholder="Check-in code">
      <input type="hidden" name="<?= (require __DIR__.'/../../../config.php')['security']['csrf_key'] ?>" value="<?= Auth::csrfToken() ?>">
      <button class="btn small" type="submit">Check-in</button>
    </form>
  <?php endif; ?>

  <ul class="chips">
    <?php foreach ($participants as $p): ?>
      <li><?= htmlspecialchars($p['participant']) ?><?php if (Auth::role()==="organizer"||Auth::role()==="admin"): ?> — <?= htmlspecialchars($p['email'] ?? '') ?> — <?= $p['checked_in']?"Checked":"Not checked" ?> <?php if (!empty($p['checkin_code'])): ?>(Code: <?= htmlspecialchars($p['checkin_code']) ?>)<?php endif; ?><?php endif; ?></li>
    <?php endforeach; ?>
  </ul>
</section>