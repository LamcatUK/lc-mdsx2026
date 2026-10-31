/** Staggered entrance reveals: adds .is-in to .reveal elements as they scroll into view. */
export function initReveal() {
	const els = document.querySelectorAll('.reveal');
	if (!els.length) return;
	if (!('IntersectionObserver' in window)) {
		els.forEach((el) => el.classList.add('is-in'));
		return;
	}
	const io = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-in');
					io.unobserve(entry.target);
				}
			});
		},
		{ rootMargin: '0px 0px -8% 0px' }
	);
	els.forEach((el) => io.observe(el));
}
