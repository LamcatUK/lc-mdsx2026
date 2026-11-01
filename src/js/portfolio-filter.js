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
		const empty = root.querySelector('[data-empty]');
		const toggle = root.querySelector('[data-filters-toggle]');
		const panel = root.querySelector('#portfolio-filter-panel');
		const badge = root.querySelector('[data-filter-badge]');
		const done = root.querySelector('[data-filters-done]');
		const resets = root.querySelectorAll('[data-filter-reset]');
		const active = {};

		// Small screens: the chips sit in a panel that drops down from a slim bar.
		const setOpen = (open) => {
			if (!toggle || !panel) return;
			panel.classList.toggle('is-open', open);
			toggle.setAttribute('aria-expanded', String(open));
		};
		toggle?.addEventListener('click', () => setOpen(!panel.classList.contains('is-open')));
		done?.addEventListener('click', () => {
			setOpen(false);
			toggle?.focus();
		});
		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape' && panel?.classList.contains('is-open')) {
				setOpen(false);
				toggle.focus();
			}
		});
		document.addEventListener('click', (event) => {
			if (panel?.classList.contains('is-open') && !event.target.closest('.portfolio-grid__filter-bar')) setOpen(false);
		});

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
			if (empty) empty.hidden = visible > 0;
			const chosen = Object.values(active).filter(Boolean).length;
			if (badge) {
				badge.hidden = chosen === 0;
				badge.textContent = chosen;
			}
			resets.forEach((reset) => {
				if (reset.closest('.portfolio-grid__panel-actions')) reset.hidden = chosen === 0;
			});
			if (done) done.firstChild.textContent = (done.dataset.template || '%d').replace('%d', visible);
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
		resets.forEach((reset) =>
			reset.addEventListener('click', () => {
				groups.forEach((group) => {
					active[group.dataset.filterGroup] = '';
					group.querySelectorAll('[data-filter]').forEach((c) => c.setAttribute('aria-pressed', String(c.dataset.filter === '')));
				});
				apply();
			})
		);
		apply();
	});
}
