// ===== EMAIL =====
const emailInput = document.querySelector("#email");

if (emailInput) {
  emailInput.value = "exemple@gmail.com";

  emailInput.addEventListener("focus", function () {
    if (emailInput.value === "exemple@gmail.com") {
      emailInput.value = "";
    }
  });

  emailInput.addEventListener("blur", function () {
    if (emailInput.value === "") {
      emailInput.value = "exemple@gmail.com";
    }
  });
}

// ===== PASSWORD =====
const passwordInput = document.querySelector("#password");

if (passwordInput) {
  passwordInput.value = "Entrer un mot de passe";

  passwordInput.addEventListener("focus", function () {
    if (passwordInput.value === "Entrer un mot de passe") {
      passwordInput.value = "";
    }
  });

  passwordInput.addEventListener("blur", function () {
    if (passwordInput.value === "") {
      passwordInput.value = "Entrer un mot de passe";
    }
  });
}
