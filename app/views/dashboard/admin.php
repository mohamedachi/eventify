<section>
  <h1>Admin dashboard</h1>
  <div class="stats">
    <div class="card"><h3>Total events</h3><p class="big"><?= (int)$stats['total_events'] ?></p></div>
    <div class="card"><h3>Total participations</h3><p class="big"><?= (int)$stats['total_participations'] ?></p></div>
    <div class="card"><h3>Potential revenue</h3><p class="big">$<?= number_format((float)$stats['total_potential_revenue'],2) ?></p></div>
  </div>
  <p>Use the navigation to manage users.</p>
</section>