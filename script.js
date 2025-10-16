function loadApp() {
  document.getElementById("splash").style.display = "none";
  document.querySelector(".app-loaded").style.display = "block";
  document.getElementById("main-content").style.display = "block";
}

// Simula o carregamento do app (3 segundos)
setTimeout(loadApp, 3000);