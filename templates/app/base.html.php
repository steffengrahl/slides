<!doctype html>
<html lang="en">
<head>
  <title><?= $page['title'] ?? '' ?></title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="css/styles.css?cb<?= time() ?>">
</head>
<body>
  <header class="page-header">
    <h1 class="page-title">
      <a href="index.php">Your slides</a>
    </h1>
  </header>
  <main>
<?php include \App\Configuration\DIR_TEMPLATES . $template ?>
  </main>
  <script src="https://kit.fontawesome.com/1fd9e7b2d4.js" crossorigin="anonymous"></script>
</body>
</html>