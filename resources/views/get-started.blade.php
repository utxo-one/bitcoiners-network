<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="/favicon.png">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:100,400,600,700|montserrat:100,400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/get_started.scss', 'resources/css/palette.css'])

    <title>Bitcoiners Network</title>
</head>

<body>
  <img class='logo' src="{{ asset('images/bitcoiners_network_logo.svg') }}" alt="Bitcoiners Network" />

  <h2>Let's get ready <strong>to start.</strong></h2>
  <h3>Before you sign in with Twitter...</h3>

  <p>Our app requires <strong>read and write</strong> permissions from Twitter to function.</p>
  <p>To verify and run it without trust, feel free to <a href="https://github.com/utxo-one/bitcoiners-network" target="_blank" rel="noreferrer">Download the Code.</a></p>

  <div class="get-started">
    <a href="/auth/twitter">
      <img src="{{ asset('images/twitter_logo.svg') }}" alt="🐥" />
      <div>Get Started</div>
    </a>
  </div>

  <div class='verify'>We do not post on your behalf, collect your email or any private information. <a href="https://github.com/utxo-one/bitcoiners-network/blob/master/app/Services/UserService.php#L158" target="_blank" rel="noreferrer">Verify source.</a></div>
</body>

</html>
