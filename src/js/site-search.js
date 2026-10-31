/**
 * Site search (/search/). Typing fetches matches from lc-mdsx2026/v1/search (inc/search.php) and
 * swaps the results in, grouped by type with the matched words wrapped in <mark>; chips narrow the
 * list to one type, and the address is kept in step (?q=…) so a search can be shared. Without JS
 * the form submits to /search/?q=… and the block renders the same results on the server.
 */
export function initSiteSearch() {
	const root = document.querySelector('[data-site-search]');
	if (!root) return;

	const input = root.querySelector('[data-search-input]');
	const form = root.querySelector('form');
	const filters = root.querySelector('[data-search-filters]');
	const results = root.querySelector('[data-search-results]');
	const endpoint = root.dataset.endpoint;
	if (!input || !results || !endpoint) return;

	const LABELS = { portfolio: 'Projects', page: 'Pages', team: 'People', post: 'News' };
	const ORDER = ['portfolio', 'page', 'team', 'post'];
	let items = [];
	let type = '';
	let words = [];
	let timer = null;
	let pending = null;

	const escapeRegExp = (text) => text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

	const highlight = (el, text) => {
		el.textContent = '';
		if (!words.length) {
			el.textContent = text;
			return;
		}
		text.split(new RegExp('(' + words.map(escapeRegExp).join('|') + ')', 'i')).forEach((part, i) => {
			if (i % 2) {
				const mark = document.createElement('mark');
				mark.textContent = part;
				el.appendChild(mark);
			} else if (part) {
				el.appendChild(document.createTextNode(part));
			}
		});
	};

	const el = (tag, className, text) => {
		const node = document.createElement(tag);
		if (className) node.className = className;
		if (text) node.textContent = text;
		return node;
	};

	const renderFilters = () => {
		filters.textContent = '';
		if (!items.length) {
			filters.hidden = true;
			return;
		}
		const counts = {};
		items.forEach((item) => (counts[item.type] = (counts[item.type] || 0) + 1));
		const chip = (value, label) => {
			const button = el('button', 'chip', label);
			button.type = 'button';
			button.dataset.type = value;
			button.setAttribute('aria-pressed', String(value === type));
			button.addEventListener('click', () => {
				type = value;
				render();
			});
			filters.appendChild(button);
		};
		chip('', `All (${items.length})`);
		ORDER.filter((t) => counts[t]).forEach((t) => chip(t, `${LABELS[t]} (${counts[t]})`));
		filters.hidden = false;
	};

	const renderItem = (item) => {
		const li = el('li', 'site-search__item' + (item.image ? ' has-thumb' : ''));

		if (item.image) {
			const thumb = el('a', 'site-search__thumb pg pg--half');
			thumb.href = item.url;
			thumb.tabIndex = -1;
			thumb.setAttribute('aria-hidden', 'true');
			const wrap = document.createElement('div');
			const img = document.createElement('img');
			img.src = item.image;
			img.alt = '';
			img.loading = 'lazy';
			wrap.appendChild(img);
			thumb.appendChild(wrap);
			li.appendChild(thumb);
		}

		const text = el('div', 'site-search__text');
		const title = el('h3');
		const link = document.createElement('a');
		link.href = item.url;
		highlight(link, item.title);
		title.appendChild(link);
		const snippet = el('p');
		highlight(snippet, item.snippet);
		text.append(title, snippet);
		li.appendChild(text);

		return li;
	};

	const render = () => {
		renderFilters();
		results.textContent = '';

		const shown = type ? items.filter((item) => item.type === type) : items;
		const status = el('p', 'site-search__status');
		status.setAttribute('role', 'status');
		status.textContent = shown.length ? `${shown.length} ${shown.length === 1 ? 'result' : 'results'}` : 'No results. Try different or fewer words.';
		results.appendChild(status);

		ORDER.forEach((t) => {
			const group = shown.filter((item) => item.type === t);
			if (!group.length) return;
			const section = el('section', 'site-search__group');
			section.dataset.type = t;
			section.appendChild(el('h2', '', LABELS[t]));
			const list = el('ul', 'site-search__list');
			group.forEach((item) => list.appendChild(renderItem(item)));
			section.appendChild(list);
			results.appendChild(section);
		});
	};

	const setUrl = (query) => {
		const url = new URL(window.location.href);
		if (query) url.searchParams.set('q', query);
		else url.searchParams.delete('q');
		window.history.replaceState(null, '', url);
	};

	const run = () => {
		const query = input.value.trim();
		setUrl(query);
		pending?.abort();
		words = query.split(/\s+/).filter((word) => word.length > 1);

		if (words.length === 0) {
			items = [];
			results.textContent = '';
			filters.hidden = true;
			results.removeAttribute('aria-busy');
			return;
		}

		pending = new AbortController();
		results.setAttribute('aria-busy', 'true');
		fetch(`${endpoint}?q=${encodeURIComponent(query)}`, { signal: pending.signal })
			.then((response) => (response.ok ? response.json() : null))
			.then((data) => {
				if (!data) return;
				items = data.results || [];
				type = '';
				render();
				results.removeAttribute('aria-busy');
			})
			.catch((error) => {
				if (error.name !== 'AbortError') results.removeAttribute('aria-busy');
			});
	};

	input.addEventListener('input', () => {
		clearTimeout(timer);
		timer = setTimeout(run, 220);
	});
	form.addEventListener('submit', (event) => {
		event.preventDefault();
		clearTimeout(timer);
		run();
	});
	// Back/forward or a fresh load with ?q=: the server already rendered the results, so only wire the chips up.
	if (input.value.trim()) {
		run();
	}
}
