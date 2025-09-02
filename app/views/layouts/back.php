<?php $u = Auth::user(); $config = require __DIR__.'/../../../config.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Back Office - <?= htmlspecialchars($config['app']['name'] ?? 'Event Platform') ?></title>
  <link rel="stylesheet" href="<?= base_url('public/css/styleback.css') ?>">
  <script defer src="<?= base_url('public/js/app.js') ?>"></script>
</head>
<body class="back">
  <?php View::partial('navbar-back', ['user'=>$u]); ?>
  <main class="container">
    <?= $content ?>
  </main>
  <?php View::partial('footer'); ?>
</body>
</html>