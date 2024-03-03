<!doctype html>
<html>
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
<?php include $template ?>
    </main>
</body>
</html>