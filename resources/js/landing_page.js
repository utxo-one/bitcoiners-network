// Landing Page JS -- not used in React
addEventListener('DOMContentLoaded', () => {
  initWelcome();
});

// Shows the "sign up" button on the header after scrolling below the fold:
function handleButtonIntersect() {
  const handleIntersect = ([entry]) => {
    const button = document.getElementById('header-sign-up-button');
    entry.isIntersecting ?  button.classList.remove('visible') : button.classList.add('visible');
  }
  
  const intersector = new IntersectionObserver(handleIntersect, { threshold: [0] });
  intersector.observe(document.getElementById('above-the-fold-button'));
}

const availableImages = []; 

async function handleBitcoinersFaces() {
  // try {
    const response = await fetch('/frontend/profile-pictures');
    const bitcoiners = await response.json();

    const container = document.getElementById('bitcoiners-container');

    const loadImage = async (imageSrc) => {
      try {
        const image = new Image();
        image.src = imageSrc;
        await image.decode();

        availableImages.push(imageSrc);
      }
      catch {}
    }

    // if image loading doesn't work (IE: on the twitter side of the network), do not create images:
    if (availableImages.length === 0) {
      Promise.allSettled(bitcoiners.map(imageSrc => loadImage(imageSrc))).then(() => {
        const CELL_WIDTH = 72;
        const columnCount = Math.ceil(Math.round(window.innerWidth / CELL_WIDTH));
  
        for (let i = 0; i < columnCount; ++i) {
          const column = document.createElement("div");
          column.className = 'column';
    
          // select 10 random images for each colunmn:
          const imageIndex = [];
          for (let j = 0; j < 8; ++j) {
            imageIndex.push(Math.floor(Math.random() * availableImages.length));
          }
    
          for (let j = 0; j < imageIndex.length * 3; ++j) {
            const img = document.createElement("img");
            img.src = availableImages[imageIndex[j % imageIndex.length]];
            img.style.animationDelay = `${1000 + Math.round(Math.random() * 6000)}ms`;
            column.appendChild(img);
          }
    
          container.appendChild(column);
        }
      });
    }

  // }
  // catch {
  //   console.log("Could not load bitcoiners")
  // }
}

async function initWelcome() {
  handleBitcoinersFaces();
  handleButtonIntersect();
}

