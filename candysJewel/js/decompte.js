(() => {
  function compterRebours() {
    const decompte = document.querySelector("#decompte");

    // Si la page n'a pas #decompte, on ne fait rien
    if (!decompte) return;

    const dateActuelle = new Date().getTime();
    const dateEvenement = new Date("Jan 15 00:00 2024").getTime();
    const totalSecondes = (dateEvenement - dateActuelle) / 1000;

    if (totalSecondes <= 0) {
      decompte.innerText = "Compte a rebours terminé !";
      return; // on arrête ici
    }

    const jours = Math.floor(totalSecondes / (60 * 60 * 24));
    const heures = Math.floor((totalSecondes - (jours * 60 * 60 * 24)) / (60 * 60));
    const minutes = Math.floor((totalSecondes - (jours * 60 * 60 * 24 + heures * 60 * 60)) / 60);
    const secondes = Math.floor(totalSecondes - (jours * 60 * 60 * 24 + heures * 60 * 60 + minutes * 60));

    decompte.innerText = jours + "j " + heures + "h " + minutes + "m et " + secondes + "s";

    setTimeout(compterRebours, 1000);
  }

  compterRebours();
})();
