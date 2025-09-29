<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= $this->renderSection("title") ?></title>
</head>

<body>
    <?php if (session()->has("message")): ?>

        <p><?= session("message") ?></p>

    <?php endif; ?>
    
    <?= $this->renderSection("content") ?>

</body>
</html>
