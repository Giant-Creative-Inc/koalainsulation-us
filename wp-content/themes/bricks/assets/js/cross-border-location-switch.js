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

  function getDialogCopy(country) {
    return country === "CA"
      ? {
          title: "Switch to the American site?",
          message: "It looks like you entered a U.S. ZIP code.",
          visitLabel: "Visit American Site",
          stayLabel: "Stay on Canadian Site",
        }
      : {
          title: "Switch to the Canadian site?",
          message: "It looks like you entered a Canadian postal code.",
          visitLabel: "Visit Canadian Site",
          stayLabel: "Stay on U.S. Site",
        };
  }

  function createDialog(browser, country) {
    var doc = browser.document;
    var copy = getDialogCopy(country);
    var overlay = doc.createElement("div");
    overlay.className = "koala-country-switch";
    overlay.hidden = true;
    overlay.innerHTML =
      '<div class="koala-country-switch__panel" role="dialog" aria-modal="true" aria-labelledby="koala-country-switch-title" aria-describedby="koala-country-switch-message">' +
        '<button class="koala-country-switch__close" type="button" aria-label="Close website switch prompt">&times;</button>' +
        '<h2 id="koala-country-switch-title"></h2>' +
        '<p id="koala-country-switch-message"></p>' +
        '<div class="koala-country-switch__actions">' +
          '<a class="koala-country-switch__visit" href=""></a>' +
          '<button class="koala-country-switch__stay" type="button"></button>' +
        '</div>' +
      '</div>';

    var title = overlay.querySelector("#koala-country-switch-title");
    var message = overlay.querySelector("#koala-country-switch-message");
    var visit = overlay.querySelector(".koala-country-switch__visit");
    var stay = overlay.querySelector(".koala-country-switch__stay");
    var close = overlay.querySelector(".koala-country-switch__close");
    var previousFocus = null;

    title.textContent = copy.title;
    message.textContent = copy.message;
    visit.textContent = copy.visitLabel;
    visit.href = destinations[country];
    stay.textContent = copy.stayLabel;

    function hide() {
      overlay.hidden = true;
      overlay.classList.remove("is-open");
      doc.body.classList.remove("koala-country-switch-open");
      if (previousFocus) {
        previousFocus.focus();
      }
    }

    close.addEventListener("click", hide);
    stay.addEventListener("click", hide);
    overlay.addEventListener("click", function (event) {
      if (event.target === overlay) {
        hide();
      }
    });
    doc.addEventListener("keydown", function (event) {
      if (!overlay.hidden && event.key === "Escape") {
        hide();
      }
    });
    doc.body.appendChild(overlay);

    return {
      show: function (trigger) {
        previousFocus = trigger;
        overlay.hidden = false;
        overlay.classList.add("is-open");
        doc.body.classList.add("koala-country-switch-open");
        close.focus();
      },
    };
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

    var container = trigger.closest(".location-container") || target.ownerDocument;
    return container.querySelector(".top-zipcode-input, #zipcode-input");
  }

  function attach(browser, config) {
    var country = config.country === "CA" ? "CA" : "US";
    var dialog = createDialog(browser, country);

    function handle(event) {
      if (event.type === "keydown" && event.key !== "Enter") {
        return;
      }

      var input = findInputForEvent(event);
      if (!input || !isForeignCode(input.value, country)) {
        return;
      }

      event.preventDefault();
      event.stopImmediatePropagation();
      dialog.show(input);
    }

    browser.document.addEventListener("keydown", handle, true);
    browser.document.addEventListener("click", handle, true);
  }

  return {
    attach: attach,
    getDialogCopy: getDialogCopy,
    getSwitchUrl: getSwitchUrl,
    isCanadianPostalCode: isCanadianPostalCode,
    isUsZip: isUsZip,
  };
});
