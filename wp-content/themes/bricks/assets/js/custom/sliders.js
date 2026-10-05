/**
 * sliders.js
 *
 * Swiper slider initialisers extracted from all-pages.js.
 * Loaded conditionally (with the swiper-bundle CDN library) only on pages/
 * templates that render custom slider markup: front page (services slider),
 * single locations (partner slider), single location-services / why-koala /
 * why-reinsulate (photo & before/after sliders).
 *
 * Depends on: jQuery, Swiper (swiper-bundle).
 *
 * Wrapped in an IIFE that binds $ = jQuery locally. The original code relied on
 * a global `$ = jQuery` set at the top of all-pages.js; because this module is
 * enqueued to load *before* all-pages.js, that global is not yet defined when
 * this runs, so we bind $ here instead (and avoid leaking a new global).
 *
 * Extracted 2026-08-18. Slider logic is byte-identical to the original blocks
 * in all-pages.js; see all-pages.js.bak-20260818-slider-extract for the source.
 */

(function ($) {

// swiper slider photos
function numberWithZero(num) {
  if (num < 10) {
    return "0" + num;
  } else {
    return num;
  }
}

$(".slider-photo-wrapper").each(function (index) {
  let totalSlides = numberWithZero(
    $(this).find(".swiper-slide.is-slider-main-slide").length
  );
  $(".swiper-number-total").text(totalSlides);
  let loopMode = false;
  if ($(this).attr("loop-mode") === "true") {
    loopMode = true;
  }
  let sliderDuration = 300;
  if ($(this).attr("slider-duration") !== undefined) {
    sliderDuration = +$(this).attr("slider-duration");
  }
  const swiper = new Swiper($(this).find(".swiper")[0], {
    speed: 1000,
    loop: true,
    autoHeight: false,
    centeredSlides: loopMode,
    followFinger: true,
    freeMode: false,
    slideToClickedSlide: false,
    parallax: true,
    slidesPerView: 1,
    spaceBetween: "8%",
    rewind: false,
    mousewheel: {
      forceToAxis: true,
    },
    keyboard: {
      enabled: true,
      onlyInViewport: true,
    },
    breakpoints: {
      // mobile landscape
      480: {
        slidesPerView: 1,
        spaceBetween: 24,
      },
      // tablet
      768: {
        slidesPerView: 2,
        spaceBetween: 24,
      },
      // desktop
      992: {
        slidesPerView: 2,
        spaceBetween: 24,
      },
    },
    pagination: {
      el: $(this).find(".swiper-bullet-wrapper")[0],
      bulletActiveClass: "is-active",
      bulletClass: "swiper-bullet",
      bulletElement: "button",
      clickable: true,
    },
    navigation: {
      nextEl: $(this).find(".swiper-next")[0],
      prevEl: $(this).find(".swiper-prev")[0],
      disabledClass: "is-disabled",
    },
    scrollbar: {
      el: $(this).find(".swiper-drag-wrapper")[0],
      draggable: true,
      dragClass: "swiper-drag",
      snapOnRelease: true,
    },
    slideActiveClass: "is-active",
    slideDuplicateActiveClass: "is-active",
  });

  swiper.on("slideChange", function (e) {
    //console.log(swiper.realIndex);
    let slidenumber = numberWithZero(e.realIndex + 1);
    $(".swiper-number-current").text(slidenumber);
  });
});

// swiper slider why reinsulate before after photos

$(".slider-before-after-wrapper").each(function (index) {
  let totalSlides = numberWithZero(
    $(this).find(".swiper-slide.is-slider-main-slide").length
  );
  $(".swiper-number-total").text(totalSlides);
  let loopMode = false;
  if ($(this).attr("loop-mode") === "true") {
    loopMode = true;
  }
  let sliderDuration = 300;
  if ($(this).attr("slider-duration") !== undefined) {
    sliderDuration = +$(this).attr("slider-duration");
  }
  const swiper = new Swiper($(this).find(".swiper")[0], {
    speed: 1000,
    loop: true,
    autoHeight: false,
    centeredSlides: loopMode,
    followFinger: true,
    freeMode: false,
    slideToClickedSlide: false,
    parallax: true,
    slidesPerView: 1,
    spaceBetween: "8%",
    rewind: false,
    mousewheel: {
      forceToAxis: true,
    },
    keyboard: {
      enabled: true,
      onlyInViewport: true,
    },
    breakpoints: {
      // mobile landscape
      480: {
        slidesPerView: 1,
        spaceBetween: 80,
      },
      // tablet
      768: {
        slidesPerView: 1,
        spaceBetween: 80,
      },
      // desktop
      992: {
        slidesPerView: 1,
        spaceBetween: 80,
      },
    },
    pagination: {
      el: $(this).find(".swiper-bullet-wrapper")[0],
      bulletActiveClass: "is-active",
      bulletClass: "swiper-bullet",
      bulletElement: "button",
      clickable: true,
    },
    navigation: {
      nextEl: $(this).find(".swiper-next")[0],
      prevEl: $(this).find(".swiper-prev")[0],
      disabledClass: "is-disabled",
    },
    scrollbar: {
      el: $(this).find(".swiper-drag-wrapper")[0],
      draggable: true,
      dragClass: "swiper-drag",
      snapOnRelease: true,
    },
    slideActiveClass: "is-active",
    slideDuplicateActiveClass: "is-active",
  });

  swiper.on("slideChange", function (e) {
    //console.log(swiper.realIndex);
    let slidenumber = numberWithZero(e.realIndex + 1);
    $(".swiper-number-current").text(slidenumber);
  });
});

// swiper slider services

$(".services-slider-main-component").each(function (index) {
  let totalSlides = numberWithZero(
    $(this).find(".service-card-home.swiper-slide").length
  );
  $(".swiper-number-total").text(totalSlides);
  let loopMode = false;
  if ($(this).attr("loop-mode") === "true") {
    loopMode = true;
  }
  let sliderDuration = 300;
  if ($(this).attr("slider-duration") !== undefined) {
    sliderDuration = +$(this).attr("slider-duration");
  }
  const swiper = new Swiper($(this).find(".swiper")[0], {
    speed: 1000,
    loop: true,
    autoHeight: false,
    centeredSlides: false,
    followFinger: true,
    freeMode: false,
    slideToClickedSlide: false,
    parallax: true,
    slidesPerView: 1,
    spaceBetween: "8%",
    rewind: false,
    mousewheel: {
      forceToAxis: true,
    },
    keyboard: {
      enabled: true,
      onlyInViewport: true,
    },
    breakpoints: {
      // mobile landscape
      480: {
        slidesPerView: 1,
        spaceBetween: 24,
        slidesPerGroup: 1,
      },
      // tablet
      768: {
        slidesPerView: 2,
        spaceBetween: 24,
        slidesPerGroup: 2,
      },
      // desktop
      992: {
        slidesPerView: 3,
        slidesPerGroup: 3,
        spaceBetween: 24,
      },
    },
    pagination: {
      el: $(this).find(".swiper-bullet-wrapper")[0],
      bulletActiveClass: "is-active",
      bulletClass: "swiper-bullet",
      bulletElement: "button",
      clickable: true,
    },
    navigation: {
      nextEl: $(this).find(".swiper-next")[0],
      prevEl: $(this).find(".swiper-prev")[0],
      disabledClass: "is-disabled",
    },
    scrollbar: {
      el: $(this).find(".swiper-drag-wrapper")[0],
      draggable: true,
      dragClass: "swiper-drag",
      snapOnRelease: true,
    },
    slideActiveClass: "is-active",
    slideDuplicateActiveClass: "is-active",
  });

  swiper.on("slideChange", function (e) {
    //console.log(swiper.realIndex);
    let slidenumber = numberWithZero(e.realIndex + 1);
    $(".swiper-number-current").text(slidenumber);
  });
});

// swiper slider partners (responsive create/destroy)
document.addEventListener("DOMContentLoaded", function () {
  const swiperSelector = '.swiper-partner-wrapper';
  const swiperEl = document.querySelector(swiperSelector);
  let swiperInstance = null;

  function initOrDestroySwiper() {
    const slideCount = swiperEl.querySelectorAll('.swiper-slide').length;
    const isDesktop = window.innerWidth >= 1024;

    const shouldInit = !isDesktop || slideCount > 5;

    if (shouldInit && !swiperInstance) {
      swiperInstance = new Swiper(swiperSelector, {
        loop: false,
        centeredSlides: true,
        centerInsufficientSlides: true,
        spaceBetween: 20,
        autoplay: {
          delay: 3000,
          disableOnInteraction: false,
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        pagination: false,
        breakpoints: {
          320: {
            slidesPerView: 2,
          },
          480: {
            slidesPerView: 2,
          },
          600: {
            slidesPerView: 2,
          },
          1024: {
            slidesPerView: 'auto',
          }
        }
      });
    } else if (!shouldInit && swiperInstance) {
      swiperInstance.destroy(true, true);
      swiperInstance = null;
    }
  }

  // Run once on load
  if (swiperEl) {
    initOrDestroySwiper();

    // Re-check on window resize
    window.addEventListener('resize', function () {
      initOrDestroySwiper();
    });
  }
});

})(jQuery);
