(() => {
	'use strict';

	const header = document.querySelector('[data-beanstalk-header]');
	if (!header) return;
	const measureHeader = () => {
		document.documentElement.style.setProperty('--beanstalk-header-height', `${header.offsetHeight}px`);
	};
	measureHeader();
	if ('ResizeObserver' in window) {
		new ResizeObserver(measureHeader).observe(header);
	}

	const menu = header.querySelector('#beanstalk-navigation');
	const toggle = header.querySelector('.beanstalk-header__toggle');
	const close = header.querySelector('.beanstalk-header__close');
	const dropdownButtons = header.querySelectorAll('.beanstalk-header__menu-row button');

	const setMenu = (open) => {
		menu.classList.toggle('is-open', open);
		toggle.setAttribute('aria-expanded', String(open));
		toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
		document.body.classList.toggle('beanstalk-menu-open', open);
		if (open) close.focus();
	};

	toggle.addEventListener('click', () => setMenu(!menu.classList.contains('is-open')));
	close.addEventListener('click', () => {
		setMenu(false);
		toggle.focus();
	});

	dropdownButtons.forEach((button) => {
		button.addEventListener('click', () => {
			const item = button.closest('.beanstalk-header__dropdown');
			const open = !item.classList.contains('is-open');
			item.classList.toggle('is-open', open);
			button.setAttribute('aria-expanded', String(open));
		});
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && menu.classList.contains('is-open')) {
			setMenu(false);
			toggle.focus();
		}
	});

	window.addEventListener('resize', () => {
		measureHeader();
		if (window.innerWidth > 991 && menu.classList.contains('is-open')) setMenu(false);
	});
})();
