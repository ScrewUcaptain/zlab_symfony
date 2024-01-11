const trendContainer = document.querySelector(".trends");
const introContainer = document.querySelector(".intro");

function displayTrends() {
  trendContainer.style.display = "block";
  if (window.innerWidth < 768) {
    introContainer.style.display = "none";
    return;
  }
  introContainer.style.animation = "introUp-desktop 1s forwards";
  trendContainer.style.animation = "trendsUp-desktop 1s forwards";
}

function toggleBurgerMenu() {
  if (document.querySelector(".burger-links").style.display == "flex") {
    document.querySelector(".burger-links").style.display = "none";
  } else {
    document.querySelector(".burger-links").style.display = "flex";
  }
}