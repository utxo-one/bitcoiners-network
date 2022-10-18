// Landing Page JS -- not used in React
document.onreadystatechange = () => {
  if (document.readyState === 'complete') {
    initWelcome();
  }
}

async function initWelcome() {
  console.log("Trust, but verify.");

  const response = await fetch('/frontend/profile-pictures');
  const bitcoiners = await response.json();

  console.log('bitcoiners:', bitcoiners)
}