(() => {
	const popup = document.querySelector('#get-estimate-popup');
	if (!popup) return;

	const input = popup.querySelector('.top-zipcode-input');
	const closeButton = popup.querySelector('#get-estimate-close-btn');
	const heading = popup.querySelector('.heading-style-h2');
	if (!input || !closeButton || !heading) return;

	popup.setAttribute('role', 'dialog');
	popup.setAttribute('aria-modal', 'true');
	popup.setAttribute('aria-labelledby', heading.id);
	closeButton.setAttribute('role', 'button');
	closeButton.setAttribute('aria-label', 'Close location finder');
	closeButton.tabIndex = 0;

	let opener;
	let oldOverflow;
	document.addEventListener('click', event => {
		const trigger = event.target.closest('[data-national-location-trigger]');
		if (!trigger) return;

		opener = trigger;
		oldOverflow = document.documentElement.style.overflow;
		document.documentElement.style.overflow = 'hidden';
		popup.style.display = 'flex';
		input.focus();
	});

	const close = () => { popup.style.display = 'none'; };
	closeButton.addEventListener('keydown', event => {
		if (event.key === 'Enter' || event.key === ' ') {
			event.preventDefault();
			close();
		}
	});
	popup.addEventListener('click', event => {
		if (event.target === popup) close();
	});
	popup.addEventListener('keydown', event => {
		if (event.key === 'Escape') {
			event.preventDefault();
			close();
		}
		if (event.key !== 'Tab') return;

		const controls = [...popup.querySelectorAll('input, button, a[href], [tabindex="0"]')]
			.filter(element => element.getClientRects().length && !element.disabled);
		const first = controls[0];
		const last = controls.at(-1);
		if (event.shiftKey && document.activeElement === first) {
			event.preventDefault();
			last?.focus();
		} else if (!event.shiftKey && document.activeElement === last) {
			event.preventDefault();
			first?.focus();
		}
	});
	new MutationObserver(() => {
		if (!opener || getComputedStyle(popup).display !== 'none') return;
		document.documentElement.style.overflow = oldOverflow || '';
		opener.focus({ preventScroll: true });
		opener = null;
	}).observe(popup, { attributes: true, attributeFilter: ['style'] });

	// The shared lookup reveals this existing result panel after a match.
	const result = document.querySelector('#location-popup');
	if (result) {
		new MutationObserver(() => {
			if (opener && getComputedStyle(result).display !== 'none') close();
		}).observe(result, { attributes: true, attributeFilter: ['style'] });
	}
})();
