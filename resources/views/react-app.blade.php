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

    <title>Bitcoiners Network</title>
    <meta name="description" content="Follow Bitcoiners. Unfollow Shitcoiners. Join our network of tens of thousands of Bitcoiners." />
    
    <meta property="og:image" content="{{ asset('/images/screenshot_bitcoiners_network.png') }}">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@utxo_one">
    <meta name="twitter:title" content="Bitcoiners Network">
    <meta name="twitter:description" content="Follow Bitcoiners. Unfollow Shitcoiners. Join our network of tens of thousands of Bitcoiners.">
    <meta name="twitter:image" content="{{ asset('/images/screenshot_bitcoiners_network.png') }}">

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/css/palette.css', 'resources/js/app.js', 'resources/js/app.jsx'])
</head>

<body>
    <div id="root"></div>
</body>

</html>
