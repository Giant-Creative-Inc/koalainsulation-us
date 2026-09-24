(function (root, factory) {
  var api = factory();

  if (typeof module === "object" && module.exports) {
    module.exports = api;
  }

  if (root && root.document) {
    root.KoalaCrossBorderLocationSwitch = api;
    api.attach(root, root.koalaCrossBorderLocation || {});
  }
})(typeof window !== "undefined" ? window : null, function () {
  "use strict";

  var destinations = {
    CA: "https://koalainsulation.com/locations/",
    US: "https://koalainsulation.com/ca/locations/",
  };

  function isUsZip(value) {
    return /^\d{5}(?:-\d{4})?$/.test(String(value || "").trim());
  }

  function isCanadianPostalCode(value) {
    return /^[ABCEGHJ-NPRSTVXY]\d[ABCEGHJ-NPRSTV-Z][ -]?\d[ABCEGHJ-NPRSTV-Z]\d$/i.test(
      String(value || "").trim()
    );
  }

  function isForeignCode(value, country) {
    return country === "CA" ? isUsZip(value) : isCanadianPostalCode(value);
  }

  function getSwitchUrl(value, country, accepted) {
    return accepted && isForeignCode(value, country) ? destinations[country] : null;
  }

  function findInputForEvent(event) {
    var target = event.target;

    if (target.matches(".top-zipcode-input, #zipcode-input")) {
      return target;
    }

    var trigger = target.closest(".find-location-btn, #search-zip");
    if (!trigger) {
      return null;
    }

    var container = trigger.closest(".location-container") || document;
    return container.querySelector(".top-zipcode-input, #zipcode-input");
  }

  function attach(browser, config) {
    var country = config.country === "CA" ? "CA" : "US";
    var message = country === "CA"
      ? "That looks like a U.S. ZIP code. Would you like to switch to the American site?"
      : "That looks like a Canadian postal code. Would you like to switch to the Canadian site?";

    function handle(event) {
      if (event.type === "keydown" && event.key !== "Enter") {
        return;
      }

      var input = findInputForEvent(event);
      if (!input || !isForeignCode(input.value, country)) {
        return;
      }

      var switchUrl = getSwitchUrl(input.value, country, browser.confirm(message));
      if (!switchUrl) {
        return;
      }

      event.preventDefault();
      event.stopImmediatePropagation();
      browser.location.assign(switchUrl);
    }

    browser.document.addEventListener("keydown", handle, true);
    browser.document.addEventListener("click", handle, true);
  }

  return {
    attach: attach,
    getSwitchUrl: getSwitchUrl,
    isCanadianPostalCode: isCanadianPostalCode,
    isUsZip: isUsZip,
  };
});
