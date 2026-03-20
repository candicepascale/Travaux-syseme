(() => {
  const images = document.querySelectorAll(".conteneur-images img");
  const suivante = document.querySelector(".droite");
  const precedente = document.querySelector(".gauche");
  const cercles = document.querySelectorAll(".cercle");

  // Si la page n'a pas le carrousel, on ne fait rien
  if (!images.length || !suivante || !precedente || !cercles.length) return;

  let index = 0;
  const lastIndex = images.length - 1;

  // Sécuriser l'état initial (au cas où aucune image n'a "actif")
  images.forEach((img, i) => img.classList.toggle("actif", i === index));
  cercles.forEach((c, i) => c.classList.toggle("actif-cercle", i === index));

  function syncCercles() {
    for (let i = 0; i < cercles.length; i++) {
      if (Number(cercles[i].getAttribute("data-clic")) - 1 === index) {
        cercles[i].classList.add("actif-cercle");
      } else {
        cercles[i].classList.remove("actif-cercle");
      }
    }
  }

  function changerSuivante() {
    images[index].classList.remove("actif");
    index = (index < lastIndex) ? index + 1 : 0;
    images[index].classList.add("actif");
    syncCercles();
  }

  function changerPrecedente() {
    images[index].classList.remove("actif");
    index = (index > 0) ? index - 1 : lastIndex;
    images[index].classList.add("actif");
    syncCercles();
  }

  suivante.addEventListener("click", changerSuivante);
  precedente.addEventListener("click", changerPrecedente);

  document.addEventListener("keydown", (event) => {
    if (event.keyCode === 37) changerPrecedente();
    else if (event.keyCode === 39) changerSuivante();
  });

  cercles.forEach((cercle) => {
    cercle.addEventListener("click", function () {
      for (let i = 0; i < cercles.length; i++) cercles[i].classList.remove("actif-cercle");

      this.classList.add("actif-cercle");
      images[index].classList.remove("actif");

      const cible = Number(this.getAttribute("data-clic")) - 1;
      index = Math.max(0, Math.min(cible, lastIndex));

      images[index].classList.add("actif");
    });
  });
})();
