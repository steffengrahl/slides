<!doctype html>
<html>
<head>
    <title><?= $page['title'] ?? '' ?></title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/base_struct.css">
    <style>
    body {
        font-size: 32px;
    }
    .slide .slide-number {
        font-size: 1rem;
    }
    ul, ol, dl {
        padding-left: 1rem;
    }
    ol {
        list-style-type: decimal;
    }
    li {
        margin-bottom: .5em;
    }
    img {
        max-width: 100%;
        height: auto;
    }
    </style>
</head>
<body>
    <?php include $template ?>
</body>
</html>