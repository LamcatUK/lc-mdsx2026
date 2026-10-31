/**
 * Sticky header: adds .is-scrolled to #masthead once the page has scrolled a little, which
 * inverts its colours (see src/css/nav.css). Runs once on load too, so a page restored
 * mid-scroll starts in the right state.
 *
 * Two thresholds (on at 48px, off below 16px) so it cannot flicker when the scroll position
 * hovers around a single trigger point.
 */
export function initHeaderScroll() {
	const header = document.getElementById('masthead');
	if (!header) return;

	let ticking = false;
	const update = () => {
		const y = window.scrollY;
		if (y > 48) header.classList.add('is-scrolled');
		else if (y < 16) header.classList.remove('is-scrolled');
		ticking = false;
	};
	window.addEventListener(
		'scroll',
		() => {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(update);
			}
		},
		{ passive: true }
	);
	update();
}
