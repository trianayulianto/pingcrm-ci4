<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>CI4-Inertia</title>

    <!-- Styles -->
    <link rel="stylesheet" href="<?php echo base_url('css/app.css') ?>">

    <!-- Scripts -->
    <script src="<?php echo base_url('js/app.js') ?>" defer></script>
</head>
<body>
	<?= inertia()->app($page) ?>
</body>
</html>
