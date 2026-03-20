//console.log("js ok")
const accordeonItem = document.querySelectorAll(".accordeonItem");
const accordeonHeader = document.querySelectorAll(".accordeonItemHeader");
//console.log(accordeonHeader);

for (i = 0; i < accordeonHeader.length; i++) {
  accordeonHeader[i].addEventListener("click", basculerItem);
}

function basculerItem() {
  let itemClass = this.parentNode.className;
  for (i = 0; i < accordeonItem.length; i++) {
    accordeonItem[i].className = "accordeonItem fermer";
    if (itemClass === "accordeonItem fermer") {
      this.parentNode.className = "accordeonItem ouvert";
      console.log(itemClass);
    }
  }
}
