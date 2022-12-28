<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>CI4-Inertia</title>

    <!-- ViteJs Helper -->
    <?= vite('resources/js/app.js') ?>
</head>
<body>
	<?= inertia()->app($page) ?>
</body>
</html>
