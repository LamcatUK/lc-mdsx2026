/**
 * Mobile nav toggle. Wires any button with aria-controls pointing at a
 * .navbar-collapse to show/hide it and keep aria-expanded in sync — this is
 * the entire replacement for Bootstrap's Collapse component for this use case.
 *
 * While the menu is open the header gets .menu-open (white bar, dark badge, burger -> X, see
 * src/css/nav.css), page scrolling is locked (and Lenis paused), Escape closes it, and it closes
 * itself if the viewport grows to the desktop layout.
 */
export function initNavToggle() {
	const header = document.getElementById('masthead');
	const desktop = window.matchMedia('(min-width: 992px)');

	document.querySelectorAll('.navbar-toggler[aria-controls]').forEach((toggler) => {
		const target = document.getElementById(toggler.getAttribute('aria-controls'));
		if (!target) return;

		const setOpen = (isOpen) => {
			target.classList.toggle('is-open', isOpen);
			toggler.setAttribute('aria-expanded', String(isOpen));
			header?.classList.toggle('menu-open', isOpen);
			document.documentElement.classList.toggle('menu-lock', isOpen);
			if (window.lenis) isOpen ? window.lenis.stop() : window.lenis.start();
			// The submenu is always expanded in the open mobile menu; keep its ARIA state truthful.
			target.querySelectorAll('.dropdown-toggle').forEach((t) => t.setAttribute('aria-expanded', String(isOpen)));
		};

		toggler.addEventListener('click', () => setOpen(!target.classList.contains('is-open')));

		// Close after choosing a link — expected mobile nav behaviour.
		target.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setOpen(false)));

		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape' && target.classList.contains('is-open')) {
				setOpen(false);
				toggler.focus();
			}
		});
		desktop.addEventListener('change', () => desktop.matches && setOpen(false));
	});
}
