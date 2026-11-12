(() => {
	const frames = document.querySelectorAll('iframe[data-service-autoplay]');
	if (frames.length) {
		// WP Rocket captures the initial event while it loads delayed scripts.
		const events = ['pointerdown', 'touchstart', 'keydown', 'wheel', 'rocket-allScriptsLoaded'];
		const activate = event => {
			if (!event.isTrusted && event.type !== 'rocket-allScriptsLoaded') return;
			events.forEach(type => window.removeEventListener(type, activate, true));
			frames.forEach(frame => {
				const poster = frame.parentElement.querySelector('[data-service-video-poster]');
				frame.addEventListener('load', () => {
					poster?.remove();
					frame.removeAttribute('tabindex');
				}, { once: true });
				frame.src = frame.dataset.serviceAutoplay;
				delete frame.dataset.serviceAutoplay;
			});
		};
		events.forEach(type => window.addEventListener(type, activate, { capture: true, passive: true }));
	}

	document.addEventListener('click', event => {
		const button = event.target.closest('button[data-service-video-play]');
		if (!button) return;

		const frame = document.createElement('iframe');
		frame.className = 'koala-service-process__video';
		frame.title = button.getAttribute('aria-label').replace(/^Play /, '');
		frame.allow = 'autoplay; fullscreen; picture-in-picture';
		frame.allowFullscreen = true;
		frame.src = button.dataset.serviceVideoPlay;
		button.replaceWith(frame);
		frame.focus({ preventScroll: true });
	});
})();
