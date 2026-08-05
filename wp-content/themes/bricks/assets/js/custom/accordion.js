jQuery(document).ready(function ($) {
  // Initially hide all accordion content except the first
  $(".acc-container4 .acc-content4").hide();
  $(".acc-container4 .acc4:first-child .acc-content4").show();
  $(".acc-container4 .acc4:first-child .acc-head4").addClass("active");
  $(".acc-container4 .acc4:first-child .accordian-icon-wrapper .acc-icon-min").css("display", "block");
  $(".acc-container4 .acc4:first-child .accordian-icon-wrapper .acc-icon-plus").css("display", "none");

  // Add click event for accordion headers
  $(".acc-head4").on("click", function () {
    const $accordionHead = $(this);
    const $accordionContent = $accordionHead.next(".acc-content4");
    const $iconPlus = $accordionHead.find(".acc-icon-plus");
    const $iconMin = $accordionHead.find(".acc-icon-min");

    if ($accordionHead.hasClass("active")) {
      $accordionContent.slideUp();
      $iconPlus.css("display", "block");
      $iconMin.css("display", "none");
      $accordionHead.removeClass("active");
    } else {
      $(".acc-content4").slideUp();
      $(".acc-head4").removeClass("active");
      $(".acc-head4 .acc-icon-plus").css("display", "block");
      $(".acc-head4 .acc-icon-min").css("display", "none");

      $accordionContent.slideDown();
      $iconPlus.css("display", "none");
      $iconMin.css("display", "block");
      $accordionHead.addClass("active");
    }
  });
});