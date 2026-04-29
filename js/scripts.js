/**
 * L'annonceur — scripts.js (ALLÉGÉ)
 * Ce fichier gère uniquement l'interactivité essentielle du site :
 * - Gestion du thème (Clair / Sombre)
 * - Gestion de la recherche au header
 * - Gestion de l'authentification (Connexion / Inscription)
 * - Gestion de l'affichage dynamique des éléments (CTA, etc.)
 *
 * NOTE: Les header et footer sont maintenant STATIQUES dans le HTML
 */

// --- VARIABLES GLOBALES ---
var inPages = window.location.pathname.includes("/pages/");
var base = inPages ? "../" : "";

/**
 * GESTION DU LIEN PROFIL CONDITIONNEL
 * Redirige vers le dashboard si l'utilisateur est connecté, sinon vers login
 */
function initProfileLink() {
  var profileLink = document.getElementById("profile-link-btn");
  if (!profileLink) return;

  var isLoggedIn = localStorage.getItem("isLoggedIn") === "true";

  if (isLoggedIn) {
    profileLink.href = base + "pages/dashboard.html";
  } else {
    profileLink.href = base + "pages/login.html";
  }

  // Empêcher le comportement par défaut et rediriger avec la logique
  profileLink.addEventListener("click", function (e) {
    e.preventDefault();
    window.location.href = this.href;
  });
}

/**
 * GESTION DE LA FAQ (ACCORDÉON)
 * Affiche/masque les réponses et change l'icône +/-
 */
function initFAQ() {
  var faqQuestions = document.querySelectorAll(".faq-question");
  if (faqQuestions.length === 0) return;

  faqQuestions.forEach(function (question) {
    question.addEventListener("click", function () {
      var faqItem = this.parentElement;
      var faqAnswer = faqItem.querySelector(".faq-answer");
      var faqIcon = this.querySelector(".faq-icon");

      // Toggle la classe active
      faqItem.classList.toggle("active");

      // Toggle l'affichage de la réponse
      if (faqAnswer.style.display === "block") {
        faqAnswer.style.display = "none";
        if (faqIcon) faqIcon.textContent = "+";
      } else {
        faqAnswer.style.display = "block";
        if (faqIcon) faqIcon.textContent = "-"; // Tiret pour fermer
      }
    });
  });
}
/**
 * GESTION DU THÈME (CLAIR / SOMBRE)
 * Permet à l'utilisateur de basculer entre les deux thèmes.
 * L'état est sauvegardé dans le localStorage du navigateur.
 */
function initTheme() {
  var btn = document.getElementById("theme-toggle");
  if (!btn) return;

  // Récupération du thème sauvegardé (ou thème clair par défaut)
  var savedTheme = localStorage.getItem("theme") || "theme-clair";
  document.body.className = savedTheme;
  updateThemeIcon(savedTheme);

  // Événement au clic sur le bouton de bascule
  btn.addEventListener("click", function () {
    var nextTheme = document.body.classList.contains("theme-sombre")
      ? "theme-clair"
      : "theme-sombre";
    document.body.className = nextTheme;
    localStorage.setItem("theme", nextTheme);
    updateThemeIcon(nextTheme);
  });
}

/**
 * Mise à jour de l'icône du bouton de thème
 * @param {string} theme - Le thème actuel ("theme-clair" ou "theme-sombre")
 */
function updateThemeIcon(theme) {
  var icon = document.querySelector(".theme-icon");
  if (icon) icon.textContent = theme === "theme-sombre" ? "🌙" : "🌞";
}

/**
 * GESTION DE LA RECHERCHE AU HEADER
 * Redirige vers la page produits avec le mot-clé de recherche
 */
function initSearch() {
  var searchBtn = document.getElementById("header-search-btn");
  var searchInput = document.getElementById("header-search-input");

  if (searchBtn && searchInput) {
    searchBtn.addEventListener("click", function () {
      var query = searchInput.value.trim();
      if (query)
        window.location.href =
          base + "pages/produits.html?q=" + encodeURIComponent(query);
    });

    searchInput.addEventListener("keydown", function (e) {
      if (e.key === "Enter") searchBtn.click();
    });
  }
}

/**
 * GESTION DES FORMULAIRES D'AUTHENTIFICATION
 * Gère l'envoi des données de connexion et d'inscription vers l'API PHP.
 * @param {string} formId - ID du formulaire HTML
 * @param {string} apiUrl - URL de l'API PHP à appeler
 * @param {string} redirectUrl - URL de redirection en cas de succès
 */
function initAuthForm(formId, apiUrl, redirectUrl) {
  var form = document.getElementById(formId);
  if (!form) return;

  var msgEl = document.getElementById(formId + "-message");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    if (msgEl) {
      msgEl.textContent = "Traitement en cours...";
      msgEl.className = "form-message info";
    }

    var formData = new FormData(form);
    fetch(apiUrl, { method: "POST", body: formData })
      .then(function (res) {
        if (!res.ok)
          throw new Error("Erreur serveur (HTTP " + res.status + ")");
        return res.json();
      })
      .then(function (json) {
        if (json.succes) {
          if (msgEl) {
            msgEl.textContent = "Succès ! Redirection...";
            msgEl.className = "form-message success";
          }
          localStorage.setItem("isLoggedIn", "true");
          setTimeout(function () {
            window.location.href = redirectUrl;
          }, 1000);
        } else {
          if (msgEl) {
            msgEl.textContent = json.erreur || "Une erreur est survenue";
            msgEl.className = "form-message error";
          }
        }
      })
      .catch(function (err) {
        if (msgEl) {
          msgEl.textContent = "Erreur réseau : " + err.message;
          msgEl.className = "form-message error";
        }
      });
  });
}

/**
 * GESTION DE LA SECTION CTA (Call To Action)
 * Masque la section "Prêt à commencer ?" si l'utilisateur est déjà connecté.
 */
function toggleCtaSection() {
  var cta = document.getElementById("cta-section");
  if (!cta) return;
  cta.style.display =
    localStorage.getItem("isLoggedIn") === "true" ? "none" : "block";
}

/**
 * INITIALISATION GLOBALE AU CHARGEMENT DE LA PAGE
 */
document.addEventListener("DOMContentLoaded", function () {
  initTheme(); // Activation du thème
  initSearch(); // Activation de la recherche
  initProfileLink(); // Gestion du lien profil conditionnel
  toggleCtaSection(); // Gestion de l'affichage du CTA
  initFAQ(); // Gestion de la FAQ de la page à propos

  // Initialisation des formulaires si présents sur la page
  initAuthForm(
    "form-login",
    base + "php/api/login.php",
    base + "pages/dashboard.html",
  );
  initAuthForm(
    "form-register",
    base + "php/api/register.php",
    base + "pages/dashboard.html",
  );
});

// Rafraîchir l'affichage du CTA lors de la navigation (bouton retour du navigateur)
window.addEventListener("pageshow", toggleCtaSection);
