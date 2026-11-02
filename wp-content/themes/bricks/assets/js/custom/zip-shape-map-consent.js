(function () {
  function hasMapsConsent() {
    return typeof window.cmplz_has_consent !== "function" || window.cmplz_has_consent("marketing");
  }

  function openConsentChoices() {
    const manageConsent = document.querySelector(".cmplz-manage-consent");

    if (manageConsent) {
      manageConsent.click();
    }
  }

  document.addEventListener(
    "click",
    function (event) {
      const trigger = event.target.closest("[data-zip-shape-map-load]");

      if (!trigger || hasMapsConsent()) {
        return;
      }

      event.preventDefault();
      event.stopImmediatePropagation();
      openConsentChoices();
    },
    true
  );
})();
