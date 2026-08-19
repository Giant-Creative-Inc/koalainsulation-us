/**
 * popup.js
 *
 * Global Bricks popup helpers extracted from all-pages.js:
 *   1. ?form= query-string opener — opens the Estimate popup (#4865) via
 *      bricksOpenPopup() when the URL carries a ?form= parameter.
 *   2. CallRail re-swap observer — re-runs CallTrk.loadTagsFromDOM() when the
 *      Estimate popup (#4865) is unhidden, so number swapping applies to the
 *      async-populated phone link inside it.
 *
 * The Estimate popup renders on virtually every page (global header trigger),
 * and both blocks self-guard (return early when the popup / ?form= is absent),
 * so this module is enqueued globally like all-pages.js. Pure vanilla JS.
 *
 * Extracted 2026-08-18; logic is byte-identical to the original blocks.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get the form ID from URL (?form=4865)
    const urlParams = new URLSearchParams(window.location.search);
    let formId = urlParams.get('form');

    if (urlParams.has('form') && !formId) {
        formId = '4865'; 
    }


    if (!formId) return;

    setTimeout(() => {
        if (typeof bricksOpenPopup === 'function') {
            bricksOpenPopup(formId);
        } else {
            console.error('Bricks Popup API not found.');
        }
    }, 500);
});

// Re-trigger CallRail swap when the quote popup becomes visible.
// swap.js skips hidden elements on initial scan, and all-pages.js sets the phone
// number async — so we watch for Bricks removing the .hide class from the popup.
(function () {
    var popup = document.querySelector('[data-popup-id="4865"]');
    if (!popup) return;

    var observer = new MutationObserver(function () {
        if (!popup.classList.contains('hide')) {
            setTimeout(function () {
                if (typeof CallTrk !== 'undefined' && typeof CallTrk.loadTagsFromDOM === 'function') {
                    CallTrk.loadTagsFromDOM();
                }
            }, 300);
        }
    });

    observer.observe(popup, { attributes: true, attributeFilter: ['class'] });
})();
