/**
 * Portfolio grid filtering. Each fieldset[data-filter-group] is a taxonomy;
 * tiles carry data-{taxonomy}="slug slug". Filters combine (AND across groups).
 * Desktop rhythm repeats every ten visible tiles, as rows of 3 columns:
 * [2][1] / [1][1][1] / [1][2] / [1][1][1] — so tiles 1 and 7 of each cycle are wide.
 */
const WIDE_AT = [0, 6]; // positions within each cycle of ten

export function initPortfolioFilter() {
	document.querySelectorAll('[data-portfolio-grid]').forEach((root) => {
		const items = [...root.querySelectorAll('.portfolio-grid__item')];
		const groups = [...root.querySelectorAll('[data-filter-group]')];
		const count = root.querySelector('[data-count]');
		const active = {};

		const apply = () => {
			let visible = 0;
			items.forEach((item) => {
				const show = Object.entries(active).every(([tax, slug]) => !slug || (item.dataset[tax] || '').split(' ').includes(slug));
				item.hidden = !show;
				item.classList.remove('is-wide');
				if (show) {
					if (WIDE_AT.includes(visible % 10)) item.classList.add('is-wide');
					visible += 1;
				}
			});
			if (count) count.textContent = (count.dataset.template || '%d').replace('%d', visible);
		};

		groups.forEach((group) => {
			const tax = group.dataset.filterGroup;
			active[tax] = '';
			group.querySelectorAll('[data-filter]').forEach((chip) => {
				chip.addEventListener('click', () => {
					active[tax] = chip.dataset.filter;
					group.querySelectorAll('[data-filter]').forEach((c) => c.setAttribute('aria-pressed', String(c === chip)));
					apply();
				});
			});
		});
		apply();
	});
}
