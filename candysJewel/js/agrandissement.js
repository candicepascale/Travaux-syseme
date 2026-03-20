(() => {
  const boutonLirePlus = document.querySelectorAll(".bouton-lire-plus");
  const texte = document.querySelectorAll(".texte");

  // Si la page n'a pas ces éléments, on ne fait rien
  if (!boutonLirePlus.length || !texte.length) return;

  // On boucle sur le nombre réel d'éléments disponibles
  const n = Math.min(boutonLirePlus.length, texte.length);

  for (let i = 0; i < n; i++) {
    boutonLirePlus[i].addEventListener("click", function () {
      texte[i].classList.toggle("voirplus");

      if (boutonLirePlus[i].innerHTML === '<i class="fa-regular fa-circle-down"></i>') {
        boutonLirePlus[i].innerHTML = '<i class="fa-solid fa-circle-arrow-up"></i>';
      } else {
        boutonLirePlus[i].innerHTML = '<i class="fa-regular fa-circle-down"></i>';
      }
    });
  }
})();
