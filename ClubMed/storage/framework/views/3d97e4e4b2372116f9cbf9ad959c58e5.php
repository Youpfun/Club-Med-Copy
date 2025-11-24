<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/style.css">

        <title>Club Med</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    </head>
    <body class="antialiased">
        <h1>Bienvenue sur le site du Club Med</h1>

	<div style="text-align: center; margin-top: 50px;">
		<a href="<?php echo e(url('/clients')); ?>"
		style="background-color: #007BFF; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-size: 18px; font-family: sans-serif;"> Accéder à la liste des Clients</a></div>
    </body>
</html>
<?php /**PATH /home/s324-clubmed/club-med/ClubMed/resources/views/welcome.blade.php ENDPATH**/ ?>