(() => {
  const labelArrows = feed => {
    feed.querySelectorAll('button.grw-prev:not([aria-label]), button.grw-next:not([aria-label])').forEach(button => {
      button.setAttribute('aria-label', button.matches('.grw-prev') ? 'Previous reviews' : 'Next reviews');
    });
  };
  document.querySelectorAll('.koala-service-reviews__feed').forEach(feed => {
    labelArrows(feed);
    new MutationObserver(() => labelArrows(feed)).observe(feed, { childList: true, subtree: true });
  });

  document.querySelectorAll('.koala-service-reviews__gallery').forEach(gallery => {
    const track = gallery.querySelector('.koala-service-reviews__track');
    const controls = gallery.querySelector('.koala-service-reviews__controls');
    const buttons = [...controls.querySelectorAll('button')];
    const update = () => {
      controls.hidden = track.scrollWidth <= track.clientWidth + 2;
      buttons[0].disabled = track.scrollLeft <= 2;
      buttons[1].disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 2;
    };
    buttons.forEach(button => button.addEventListener('click', () => {
      track.scrollBy({
        left: Number(button.dataset.galleryDirection) * track.clientWidth,
        behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth'
      });
    }));
    track.addEventListener('scroll', update, { passive: true });
    new ResizeObserver(update).observe(track);
    update();
  });
})();
