(function () {
  "use strict";

  var started = false;
  var events = ["pointerdown", "touchstart", "keydown", "wheel", "scroll"];

  function addScript(src, onLoad) {
    var script = document.createElement("script");
    script.src = src;
    script.async = true;
    script.addEventListener("load", onLoad, { once: true });
    document.head.appendChild(script);
  }

  function loadSliderInitializers() {
    if (window.jQuery) {
      addScript(window.koalaSliderLoader.slidersUrl, function () {});
      return;
    }

    var attempts = 0;
    var waitForJQuery = window.setInterval(function () {
      attempts += 1;

      if (window.jQuery) {
        window.clearInterval(waitForJQuery);
        addScript(window.koalaSliderLoader.slidersUrl, function () {});
      } else if (attempts >= 100) {
        window.clearInterval(waitForJQuery);
      }
    }, 50);
  }

  function loadSliders() {
    if (started || !window.koalaSliderLoader) {
      return;
    }

    started = true;
    events.forEach(function (eventName) {
      window.removeEventListener(eventName, loadSliders);
    });

    if (window.Swiper) {
      loadSliderInitializers();
      return;
    }

    addScript(window.koalaSliderLoader.swiperUrl, loadSliderInitializers);
  }

  events.forEach(function (eventName) {
    window.addEventListener(eventName, loadSliders, {
      once: true,
      passive: true,
    });
  });
})();
