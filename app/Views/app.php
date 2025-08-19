<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>CI4-Inertia</title>

    <!-- ViteJs Helper -->
    <?= vite('resources/js/app.js') ?>
    <?= \Inertia\Directive::inertiaHead($page) ?>
</head>
<body>
	<?= \Inertia\Directive::inertia($page) ?>
</body>
</html>
