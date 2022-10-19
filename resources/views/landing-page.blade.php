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

    @vite(['resources/css/landing_page.scss', 'resources/css/palette.css', 'resources/js/landing_page.js'])

    <title>Bitcoiners Network</title>
    <meta name="description" content="Follow Bitcoiners. Unfollow Shitcoiners. Join our network of tens of thousands of Bitcoiners." />
    
    <meta property="og:image" content="{{ asset('/images/screenshot_bitcoiners_network.png') }}">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@utxo_one">
    <meta name="twitter:title" content="Bitcoiners Network">
    <meta name="twitter:description" content="Follow Bitcoiners. Unfollow Shitcoiners. Join our network of tens of thousands of Bitcoiners.">
    <meta name="twitter:image" content="{{ asset('/images/screenshot_bitcoiners_network.png') }}">
</head>

<body>
  <header>
    <img class='logo' src="{{ asset('images/bitcoiners_network_logo.svg') }}" alt="Bitcoiners Network" />
    <a class='sign-up' href="get_started" id="header-sign-up-button">
      <img src="{{ asset('images/twitter_logo_blue.svg') }}" alt="🐥" />
      <div>Sign Up</div>
    </a>
  </header>

  <section class="above-the-fold">

    <div class="background-gradient"></div>

    <div id="bitcoiners-container"> <!-- columns and images will be added here with JS --> </div>

    <h2>Clean Up Your <strong>Twitter Feed.</strong></h2>
    <div class="tagline">
      <p>Follow <span class="bitcoiners">Bitcoiners.</span></p>
      <p>Unfollow <span class="shitcoiners">Shitcoiners.</span></p>
    </div>

    <div class="get-started" id="above-the-fold-button">
      <a href="get_started">
        <img src="{{ asset('images/twitter_logo.svg') }}" alt="🐥" />
        <div>Get Started</div>
      </a>
    </div>
  </section>

  <section class="unleash-bitcoin-twitter">
    <div class="content">
      <div class="text">
        <h3>Unleash the Power of</h3>
        <div class="center"><h2>Bitcoin Twitter.</h2></div>
        <div>
          <p>Shitcoins can easily overwhelm your Twitter feed and ruin your experience 💩</p>
          <p>Bitcoiners Network allows you to <strong>Follow Bitcoiners</strong> and Unfollow Shitcoiners in two easy steps.</p>
        </div>
      </div>
      
      <div class="image">
        <img src="{{ asset('images/screenshot_follow_bitcoiners.png') }}" class="screenshot-follow" alt="Follow Bitcoiners" />
        <div class="bottom-gradient" />
      </div>
    </div>
  </section>

  <section class="signal-from-noise">
    <div class="content">
      <div class="text">
        <h2>Separate the <strong>Signal</strong> ⚡️ from the Noise 💩</h2>
        <p>We’ve built a network of tens of thousands of bitcoiners and... the rest of users in order to let you quickly filter and purify your feed.</p>
      </div>
      <div class="image">
        <!-- somehow the asset (webp) might not be getting searched because of srcset, and defaults to http -->
        <!-- <picture>
          <source srcset="{{ asset('images/screenshot_user_types.webp') }}" type="image/webp">
          <source srcset="{{ asset('images/screenshot_user_types.png') }}" type="image/png">  -->
          <img src="{{ asset('images/screenshot_user_types.png') }}" alt="Following User Types">
        <!-- </picture> -->
      </div>
    </div>
  </section>

  <section class="features">
    <div class="content">
      <h2>Built by bitcoiners, for bitcoiners.</h2>
      <p>We believe in spreading the word about Bitcoin and creating a world where Bitcoin can bring us financial sovereignity.</p>
    </div>

    <div class="features-list">
      <div class="feature-box">
        <img src="{{asset('/images/git.svg')}}" alt="Lightning" class="git" />
        <h3>Open Source</h3>
        <p class="small">Our code is Open Source and available for anyone to <a href="https://github.com/utxo-one/bitcoiners-network/" target="_blank">verify</a> or run on their own.</p>
      </div>

      <div class="feature-box">
        <img src="{{asset('/images/lightning.svg')}}" alt="Lightning" />
        <h3>Lightning Powered</h3>
        <p class="small">We use the Lightning Network for our quick and privacy-oriented microtransactions. You only pay for the value you use.</p>
      </div>

      <div class="feature-box">
        <img src="{{asset('/images/privacy.svg')}}" alt="Lightning" />
        <h3>Privacy Matters.</h3>
        <p class="small">We don’t collect any sensitive data such as your email or any private information. We don't use any kind of third-party trackers.</p>
      </div>

      <div class="feature-box">
        <img src="{{asset('/images/network.svg')}}" alt="Lightning" />
        <h3>Growing Network</h3>
        <p class="small">We are relentlessly sweeping and expanding our network, creating the best experience out of Bitcoin Twitter.</p>
      </div>
    </div>

    <div class="get-started">
      <a href="get_started">
        <img src="{{ asset('images/twitter_logo.svg') }}" alt="🐥" />
        <div>Get Started</div>
      </a>
    </div>

  </section>

  <footer>
    <ul>
      <li>
        <a href="https://twitter.com/utxo_one" target="_blank" rel="noreferrer">
          <img src="{{ asset('images/twitter_logo.svg') }}" alt="🐥" />
        </a>
      </li>
      <li>
        <a href="https://github.com/utxo-one/bitcoiners-network/" target="_blank" rel="noreferrer">
          <img src="{{ asset('images/github.svg') }}" alt="Github" />
        </a>
      </li>
    </ul>
  
    <ul>
      <li><a href="terms">Terms of Service</a></li>
      <li><a href="privacy">Privacy Policy</a></li>
    </ul>
  </footer>

</body>

</html>
