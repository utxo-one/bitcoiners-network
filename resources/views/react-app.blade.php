<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="/favicon.png">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:100,400,600,700" rel="stylesheet" />

    <title>Bitcoiners Network</title>

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/css/palette.css', 'resources/js/app.js', 'resources/js/app.jsx'])
</head>

<body>
    <div id="root"></div>
</body>

</html>
