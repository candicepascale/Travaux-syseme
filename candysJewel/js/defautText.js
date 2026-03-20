(() => {
  // Champ email
  const email = document.querySelector("#email");
  if (email) {
    email.value = "exemple@gmail.com";

    email.addEventListener("focus", function () {
      if (email.value === "exemple@gmail.com") email.value = "";
    });

    email.addEventListener("blur", function () {
      if (email.value === "") email.value = "exemple@gmail.com";
    });
  }

  // Champ password
  const password = document.querySelector("#password");
  if (password) {
    password.value = "Entrer un mot de passe";

    password.addEventListener("focus", function () {
      if (password.value === "Entrer un mot de passe") password.value = "";
    });

    password.addEventListener("blur", function () {
      if (password.value === "") password.value = "Entrer un mot de passe";
    });
  }
})();
