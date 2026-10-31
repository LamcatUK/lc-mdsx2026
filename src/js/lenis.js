/**
 * Lenis smooth scroll (vendored in js/vendor/lenis.min.js, exposed as window.Lenis).
 *
 * Skipped when the library is missing or the visitor prefers reduced motion.
 * - Anchor links (#related, #contact-form …) scroll smoothly. Lenis honours the page's scroll-padding-top
 *   (src/css/nav.css), so they stop below the sticky header with no extra offset here.
 * - Scrollable overlays (the mobile menu, native <dialog> modals) carry data-lenis-prevent so
 *   they keep their own native scrolling.
 * - The instance is available as window.lenis for other scripts (e.g. lenis.stop() in a modal).
 */
export function initLenis() {
	if (!window.Lenis) return;
	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

	document.querySelectorAll('dialog').forEach((dialog) => dialog.setAttribute('data-lenis-prevent', ''));

	const lenis = new window.Lenis({ anchors: true });
	window.lenis = lenis;

	const raf = (time) => {
		lenis.raf(time);
		window.requestAnimationFrame(raf);
	};
	window.requestAnimationFrame(raf);
}
