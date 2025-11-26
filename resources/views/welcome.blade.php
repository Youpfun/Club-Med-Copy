<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="style.css" rel="stylesheet" />
        <title>Laravel</title>

    </head>
    <body class="antialiased">
		<h1> Bienvenue sur le site du Club Med</h1>
		<div style="text-align: center; margin-top: 50px;">
    <a href="{{ url('/resorts') }}" 
       style="background-color: #007BFF; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-size: 18px; font-family: sans-serif;">
        📂 Accéder à la liste des Clients
    </a>
</div>
    </body>
</html>
