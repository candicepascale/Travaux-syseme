(() => {
  const zoneCompter = document.querySelector("#compter");
  const items = document.querySelectorAll(".article");

  // Si la page n'a pas #compter, on ne fait rien
  if (!zoneCompter) return;

  const balise = document.createElement("h3");
  balise.innerText = "Nombre de catégories: " + items.length;
  zoneCompter.appendChild(balise);
})();
