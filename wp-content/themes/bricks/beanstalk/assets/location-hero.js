(() => {
  let dialog, player, opener, previousOverflow;
  let outsidePointer = false;

  const createDialog = () => {
    dialog = document.createElement('dialog');
    dialog.className = 'koala-location-video';
    dialog.setAttribute('aria-label', 'Location video');
    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'koala-location-video__close';
    close.dataset.locationVideoClose = '';
    close.setAttribute('aria-label', 'Close video');
    close.textContent = '\u00d7';
    player = document.createElement('div');
    player.className = 'koala-location-video__player';
    dialog.append(close, player);
    document.body.append(dialog);
    close.addEventListener('click', () => dialog.close());
    const outside = event => {
      const rect = dialog.getBoundingClientRect();
      return event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom;
    };
    dialog.addEventListener('pointerdown', event => { outsidePointer = event.target === dialog && outside(event); });
    dialog.addEventListener('click', event => {
      if (outsidePointer && event.target === dialog && outside(event)) dialog.close();
      outsidePointer = false;
    });
    dialog.addEventListener('close', () => {
      player.replaceChildren(); // Removing the iframe stops playback.
      document.body.style.overflow = previousOverflow;
      if (opener?.isConnected) opener.focus({preventScroll:true});
      opener = null;
      outsidePointer = false;
    });
  };

  document.addEventListener('click', event => {
    const button = event.target.closest('[data-location-hero-video], [data-location-about-play]');
    if (!button) return;
    event.preventDefault();
    if (!dialog) createDialog();
    if (dialog.open) return;
    opener = button;
    const title = button.hasAttribute('data-location-about-play') ? 'Who We Are — Koala Insulation' : 'Location video';
    dialog.setAttribute('aria-label', title);
    const frame = document.createElement('iframe');
    frame.src = button.dataset.locationHeroVideo || button.dataset.video;
    frame.title = title;
    frame.allow = 'autoplay; fullscreen; picture-in-picture';
    frame.allowFullscreen = true;
    player.replaceChildren(frame);
    previousOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    dialog.showModal();
    dialog.querySelector('button').focus();
  });
})();
