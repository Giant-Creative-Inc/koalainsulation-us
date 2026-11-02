(() => {
  const root = document.querySelector('[data-location-sidebar]');
  if (!root) return;
  const panel = root.querySelector('.koala-location-sidebar__panel');
  const trigger = root.querySelector('[data-sidebar-open]');
  const close = root.querySelector('[data-sidebar-close]');
  const mobile = window.matchMedia('(max-width: 1229px)');
  let previousFocus;
  let pointerStartedOutside = false;
  function setOpen(open) {
    root.classList.toggle('is-open', open);
    document.body.classList.toggle('koala-sidebar-open', open && mobile.matches);
    trigger.setAttribute('aria-expanded', String(open));
    panel.inert = mobile.matches && !open;
    if (mobile.matches) {
      panel.setAttribute('role', 'dialog');
      panel.setAttribute('aria-modal', 'true');
    } else {
      panel.removeAttribute('role');
      panel.removeAttribute('aria-modal');
    }
    if (open && mobile.matches) {
      previousFocus = document.activeElement;
      panel.focus();
    } else if (previousFocus && mobile.matches) {
      previousFocus.focus();
      previousFocus = null;
    }
  }
  // Touch clicks may be retargeted after focus/viewport changes. Only dismiss
  // gestures that started outside, rather than trusting the final click alone.
  document.addEventListener('pointerdown', (event) => {
    pointerStartedOutside = mobile.matches && root.classList.contains('is-open')
      && !event.composedPath().includes(panel);
  }, true);
  document.addEventListener('pointercancel', () => { pointerStartedOutside = false; }, true);
  // Consume the outside click so it dismisses the panel without following a page link.
  document.addEventListener('click', (event) => {
    if (!mobile.matches || !root.classList.contains('is-open') || !pointerStartedOutside || event.composedPath().includes(panel)) return;
    pointerStartedOutside = false;
    if (event.target.closest('[data-sidebar-open], .show-sidebar-form')) return;
    event.preventDefault();
    event.stopPropagation();
    setOpen(false);
  }, true);
  document.addEventListener('click', (event) => {
    const opener = event.target.closest('[data-sidebar-open], .show-sidebar-form');
    if (opener && !opener.closest('[data-interactions*="popup"]')) {
      event.preventDefault();
      setOpen(true);
    }
    if (event.target.closest('[data-sidebar-close]')) setOpen(false);
  });
  panel.addEventListener('keydown', (event) => {
    if (!mobile.matches) return;
    if (event.key === 'Escape') { event.preventDefault(); setOpen(false); }
    if (event.key !== 'Tab') return;
    const controls = [...panel.querySelectorAll('button, input, select, textarea, a[href], [tabindex="0"]')]
      .filter((el) => !el.disabled && el.getClientRects().length);
    const first = controls[0];
    const last = controls[controls.length - 1];
    if (event.shiftKey && (document.activeElement === first || document.activeElement === panel)) {
      event.preventDefault(); last?.focus();
    } else if (!event.shiftKey && (document.activeElement === last || document.activeElement === panel)) {
      event.preventDefault(); first?.focus();
    }
  });
  mobile.addEventListener('change', () => setOpen(false));
  setOpen(false);
})();
