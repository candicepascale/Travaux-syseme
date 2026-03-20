const menu = document.querySelector("#js-menu");
const navBarToggle = document.querySelector("#js-navbar-toggle")

navBarToggle.addEventListener("click", function(){
    menu.classList.toggle("active");
    navBarToggle.classList.toggle("change");
})