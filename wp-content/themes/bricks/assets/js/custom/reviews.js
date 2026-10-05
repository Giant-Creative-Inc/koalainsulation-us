/**
 * reviews.js
 *
 * Review-count integration extracted from all-pages.js.
 * Polls the Google/NiceJob review widget (#main-page-widget / #local-page-widget,
 * populated client-side by the widget-google-reviews plugin) for its
 * `.nj-trust__total` value and mirrors it into the `#review-count` target.
 *
 * The block self-terminates immediately when `#review-count` is absent, so it is
 * enqueued only where that target renders: single locations and landing pages.
 *
 * Pure vanilla JS (no jQuery dependency). Extracted 2026-08-18; logic is
 * byte-identical to the original block in all-pages.js.
 */

document.addEventListener("DOMContentLoaded", function () {
  const interval = setInterval(function () {
    // 1. Get references to all necessary elements first.
    const localWidget = document.getElementById('local-page-widget');
    const mainWidget = document.getElementById('main-page-widget');
    const targetDiv = document.getElementById('review-count');

    // If the target div doesn't exist on the page, stop trying.
    if (!targetDiv) {
      clearInterval(interval);
      return;
    }

    // 2. Determine if the local widget is currently visible.
    // We use getComputedStyle because it checks the actual rendered style,
    // not just inline styles.
    const isLocalWidgetHidden = !localWidget || window.getComputedStyle(localWidget).display === 'none';

    // 3. Define a variable to hold the element we find.
    let njReviewCountEl = null;

    // 4. Conditionally search for the review count element in the correct parent widget.
    if (isLocalWidgetHidden) {
      // If the local widget is hidden, search inside the main (national) widget.
      if (mainWidget) {
        njReviewCountEl = mainWidget.querySelector('.nj-trust__total');
      }
    } else {
      // Otherwise, the local widget is visible, so search inside it.
      njReviewCountEl = localWidget.querySelector('.nj-trust__total');
    }

    // 5. If we found the review count element AND the target div exists, update and stop.
    if (njReviewCountEl && targetDiv) {
      targetDiv.textContent = njReviewCountEl.textContent;
      clearInterval(interval); // Stop the interval once the job is done.
    }
  }, 200); // Check every 200 milliseconds.
});
