/**
 * Cursor effect over work tiles: a Crusta circle with an arrow follows the pointer while it is over
 * a tile (.work-tile), in place of the native cursor. Fine pointers only (never on touch), and
 * skipped for reduced motion. The circle trails the pointer slightly; styles in src/css/brand.css.
 */
export function initWorkCursor() {
	if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
	if (!document.querySelector('.work-tile')) return;

	const cursor = document.createElement('div');
	cursor.className = 'work-cursor';
	cursor.setAttribute('aria-hidden', 'true');
	cursor.innerHTML =
		'<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="square"><path d="M6 18 18 6M8 6h10v10"/></svg>';
	document.body.appendChild(cursor);

	let x = 0;
	let y = 0;
	let cx = 0;
	let cy = 0;
	let running = false;

	const frame = () => {
		cx += (x - cx) * 0.22;
		cy += (y - cy) * 0.22;
		cursor.style.transform = `translate3d(${cx}px, ${cy}px, 0) translate(-50%, -50%)`;
		if (Math.abs(x - cx) > 0.1 || Math.abs(y - cy) > 0.1) {
			requestAnimationFrame(frame);
		} else {
			running = false;
		}
	};
	const run = () => {
		if (!running) {
			running = true;
			requestAnimationFrame(frame);
		}
	};

	const setActive = (on) => {
		cursor.classList.toggle('is-active', on);
		document.documentElement.classList.toggle('has-work-cursor', on);
	};
	const overTile = (el) => !!(el && el.closest && el.closest('.work-tile'));

	document.addEventListener(
		'pointermove',
		(event) => {
			if (event.pointerType !== 'mouse') return;
			const wasActive = cursor.classList.contains('is-active');
			x = event.clientX;
			y = event.clientY;
			const on = overTile(event.target);
			if (on && !wasActive) {
				cx = x;
				cy = y;
			}
			setActive(on);
			if (on) run();
		},
		{ passive: true }
	);
	// the page can scroll under a still pointer, so re-check what is under it
	window.addEventListener(
		'scroll',
		() => {
			if (cursor.classList.contains('is-active') || x || y) setActive(overTile(document.elementFromPoint(x, y)));
		},
		{ passive: true }
	);
	document.addEventListener('pointerleave', () => setActive(false));
}
