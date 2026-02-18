<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Kromerce - Plataforma e-commerce para MiPYMES. Vende productos y servicios con pagos internacionales y locales.">
    <title>Kromerce - E-commerce para MiPYMES</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logos/kromerce-business-icon-logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logos/kromerce-business-icon-logo.png') }}">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-foreground">
    <div id="app"></div>
</body>
</html>
