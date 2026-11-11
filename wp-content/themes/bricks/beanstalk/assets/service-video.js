(() => {
	const frames = document.querySelectorAll('iframe[data-service-autoplay]');
	if (frames.length) {
		const events = ['pointerdown', 'touchstart', 'keydown', 'wheel'];
		const activate = event => {
			if (!event.isTrusted) return;
			events.forEach(type => window.removeEventListener(type, activate, true));
			frames.forEach(frame => {
				frame.src = frame.dataset.serviceAutoplay;
				frame.removeAttribute('tabindex');
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
		frame.title = 'Insulation process video';
		frame.allow = 'autoplay; fullscreen; picture-in-picture';
		frame.allowFullscreen = true;
		frame.src = button.dataset.serviceVideoPlay;
		button.replaceWith(frame);
		frame.focus({ preventScroll: true });
	});
})();
