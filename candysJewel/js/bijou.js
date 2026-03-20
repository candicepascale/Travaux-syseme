/*const vignetteCelebi = document.querySelector("#celebi");
const vignetteQulbutoke = document.querySelector("#qulbutoke");
const vignetteTogepi = document.querySelector("#togepi");

// https://developer.mozilla.org/fr/docs/Web/API/Element/mousedown_event
//www.w3schools.com/jsref/tryit.asp?filename=tryjsref_onmousedown_addeventlistener
vignetteCelebi.addEventListener("mousedown", function () {
  afficherDetailPokemon("celebi");
});

vignetteQulbutoke.addEventListener("mousedown", function () {
  afficherDetailPokemon("qulbutoke");
});

vignetteTogepi.addEventListener("mousedown", function () {
  afficherDetailPokemon("togepi");
});*/

const listeVignettes = document.querySelectorAll(".vignette-bijou");
console.log (listeVignettes)
for (let i = 0; i< listeVignettes.length; i++){
  listeVignettes[i].addEventListener("mousedown", function (){
  afficherDetailBijou(this.id);
  
  });
}

function afficherDetailBijou(bijou){
  let detail = document.querySelector("#detail-" + bijou);
 // console.log(detail);
 let zoneAffichage= document.querySelector("#zone-affichage");
zoneAffichage.innerHTML = detail.innerHTML;
}

