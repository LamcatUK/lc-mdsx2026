/**
 * Sticky header: adds .is-scrolled to #masthead once the page has scrolled a little, which
 * inverts its colours (see src/css/nav.css). Runs once on load too, so a page restored
 * mid-scroll starts in the right state.
 */
export function initHeaderScroll() {
	const header = document.getElementById('masthead');
	if (!header) return;

	let ticking = false;
	const update = () => {
		header.classList.toggle('is-scrolled', window.scrollY > 40);
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
