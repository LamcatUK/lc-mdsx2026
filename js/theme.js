/*!
 * lc-mdsx2026 v1.0.0 (https://github.com/LamcatUK/lc-mdsx2026)
 * Copyright 2026 LamcatUK
 * Licensed under GPL-3.0
 */
(function () {
	'use strict';

	/**
	 * Mobile nav toggle. Wires any button with aria-controls pointing at a
	 * .navbar-collapse to show/hide it and keep aria-expanded in sync — this is
	 * the entire replacement for Bootstrap's Collapse component for this use case.
	 *
	 * While the menu is open the header gets .menu-open (white bar, dark badge, burger -> X, see
	 * src/css/nav.css), page scrolling is locked (and Lenis paused), Escape closes it, and it closes
	 * itself if the viewport grows to the desktop layout.
	 */
	function initNavToggle() {
	  const header = document.getElementById('masthead');
	  const desktop = window.matchMedia('(min-width: 992px)');
	  document.querySelectorAll('.navbar-toggler[aria-controls]').forEach(toggler => {
	    const target = document.getElementById(toggler.getAttribute('aria-controls'));
	    if (!target) return;
	    const setOpen = isOpen => {
	      target.classList.toggle('is-open', isOpen);
	      toggler.setAttribute('aria-expanded', String(isOpen));
	      header?.classList.toggle('menu-open', isOpen);
	      document.documentElement.classList.toggle('menu-lock', isOpen);
	      // the page behind the full-screen menu must not take focus or be read out while it is open
	      document.querySelectorAll('#main, #footer').forEach(el => el.toggleAttribute('inert', isOpen));
	      if (toggler.dataset.labelOpen) toggler.setAttribute('aria-label', isOpen ? toggler.dataset.labelClose : toggler.dataset.labelOpen);
	      if (isOpen && !desktop.matches) target.querySelector('a, button')?.focus({
	        preventScroll: true
	      });
	      if (window.lenis) isOpen ? window.lenis.stop() : window.lenis.start();
	      // Submenus start collapsed each time the mobile menu opens, and fold away when it closes.
	      target.querySelectorAll('.dropdown-toggle').forEach(t => t.setAttribute('aria-expanded', 'false'));
	      target.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('is-open'));
	    };
	    toggler.addEventListener('click', () => setOpen(!target.classList.contains('is-open')));

	    // Close after choosing a link — expected mobile nav behaviour.
	    target.querySelectorAll('a').forEach(link => link.addEventListener('click', () => setOpen(false)));
	    document.addEventListener('keydown', event => {
	      if (event.key === 'Escape' && target.classList.contains('is-open')) {
	        setOpen(false);
	        toggler.focus();
	      }
	    });
	    desktop.addEventListener('change', () => desktop.matches && setOpen(false));
	  });
	}

	/**
	 * Click-to-open nav dropdowns. Each dropdown-toggle button shows/hides its
	 * linked .dropdown-menu and keeps aria-expanded in sync. Clicking elsewhere,
	 * or pressing Escape, closes whatever is open — this is the entire
	 * replacement for hover-based submenus.
	 */
	function initNavDropdowns() {
	  const toggles = document.querySelectorAll('.dropdown-toggle[aria-controls]');
	  function close(toggle) {
	    const menu = document.getElementById(toggle.getAttribute('aria-controls'));
	    if (!menu) return;
	    menu.classList.remove('is-open');
	    toggle.setAttribute('aria-expanded', 'false');
	  }
	  function closeAllExcept(except) {
	    toggles.forEach(toggle => {
	      if (toggle !== except) close(toggle);
	    });
	  }
	  toggles.forEach(toggle => {
	    const menu = document.getElementById(toggle.getAttribute('aria-controls'));
	    if (!menu) return;
	    toggle.addEventListener('click', event => {
	      event.stopPropagation();
	      const isOpen = menu.classList.toggle('is-open');
	      toggle.setAttribute('aria-expanded', String(isOpen));
	      closeAllExcept(toggle);
	    });
	  });
	  document.addEventListener('click', event => {
	    if (event.target.closest('.dropdown-menu')) return;
	    closeAllExcept();
	  });
	  document.addEventListener('keydown', event => {
	    if (event.key !== 'Escape') return;
	    const openToggle = Array.from(toggles).find(toggle => toggle.getAttribute('aria-expanded') === 'true');
	    closeAllExcept();
	    if (openToggle) openToggle.focus();
	  });
	}

	/**
	 * Native <dialog> wiring — replaces Bootstrap's Modal component entirely.
	 * showModal()/close() do the heavy lifting (focus trap, Escape-to-close,
	 * ::backdrop); this just connects trigger/close buttons to a target dialog.
	 *
	 * Markup:
	 *   <button data-dialog-target="my-dialog">Open</button>
	 *   <dialog id="my-dialog">
	 *     <button data-dialog-close>Close</button>
	 *     ...
	 *   </dialog>
	 */
	function initDialogs() {
	  document.querySelectorAll('[data-dialog-target]').forEach(trigger => {
	    const dialog = document.getElementById(trigger.getAttribute('data-dialog-target'));
	    if (!(dialog instanceof HTMLDialogElement)) return;
	    trigger.addEventListener('click', () => dialog.showModal());
	    dialog.querySelectorAll('[data-dialog-close]').forEach(closeBtn => {
	      closeBtn.addEventListener('click', () => dialog.close());
	    });

	    // Click on the backdrop (the dialog element itself, outside its content) closes it.
	    dialog.addEventListener('click', event => {
	      if (event.target === dialog) dialog.close();
	    });
	  });
	}

	/**
	 * Hero reel: crossfades .hero-reel__slide figures and keeps the "now showing"
	 * card (title, meta, link, progress bar) in step with the current slide.
	 * Timing (hold + fade = 5.4s) matches --reel-ms in hero-reel.css, which drives
	 * the progress bar and zoom animations. Respects prefers-reduced-motion and
	 * offers a pause control (WCAG 2.2.2).
	 */
	const SLIDE_MS = 5400;
	function initHeroReel() {
	  document.querySelectorAll('.hero-reel').forEach(hero => {
	    const slides = hero.querySelectorAll('.hero-reel__slide');
	    if (slides.length < 2) return;
	    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
	    const button = hero.querySelector('[data-reel-pause]');
	    const card = hero.querySelector('[data-reel-card]');
	    const cardTitle = card?.querySelector('[data-card-title]');
	    const cardMeta = card?.querySelector('[data-card-meta]');
	    const cardBar = card?.querySelector('[data-card-bar]');
	    let index = 0;
	    let paused = reduced.matches;
	    let timer = null;
	    const restartBar = () => {
	      if (!cardBar) return;
	      cardBar.style.animation = 'none';
	      void cardBar.offsetWidth; // reflow so the animation restarts
	      cardBar.style.animation = '';
	    };
	    const show = next => {
	      slides[index].classList.remove('is-active');
	      index = next % slides.length;
	      const slide = slides[index];
	      slide.classList.add('is-active');
	      if (card) {
	        cardTitle.textContent = slide.dataset.title;
	        cardTitle.href = slide.dataset.url;
	        cardMeta.textContent = slide.dataset.meta;
	        restartBar();
	      }
	    };
	    const stop = () => window.clearInterval(timer);
	    const start = () => {
	      stop();
	      timer = window.setInterval(() => show(index + 1), SLIDE_MS);
	    };
	    const sync = () => {
	      hero.classList.toggle('is-paused', paused);
	      if (button) button.setAttribute('aria-pressed', String(paused));
	      if (paused) stop();else {
	        restartBar();
	        start();
	      }
	    };
	    button?.addEventListener('click', () => {
	      paused = !paused;
	      sync();
	    });
	    document.addEventListener('visibilitychange', () => {
	      if (document.hidden) stop();else if (!paused) {
	        restartBar();
	        start();
	      }
	    });
	    if ('IntersectionObserver' in window) {
	      // no point cycling slides nobody can see
	      new IntersectionObserver(([entry]) => {
	        if (!entry.isIntersecting) stop();else if (!paused && !document.hidden) {
	          restartBar();
	          start();
	        }
	      }).observe(hero);
	    }
	    sync();
	  });
	}

	/**
	 * Portfolio grid filtering. Each fieldset[data-filter-group] is a taxonomy;
	 * tiles carry data-{taxonomy}="slug slug". Filters combine (AND across groups).
	 * Desktop rhythm repeats every ten visible tiles, as rows of 3 columns:
	 * [2][1] / [1][1][1] / [1][2] / [1][1][1] — so tiles 1 and 7 of each cycle are wide.
	 */
	const WIDE_AT = [0, 6]; // positions within each cycle of ten

	function initPortfolioFilter() {
	  document.querySelectorAll('[data-portfolio-grid]').forEach(root => {
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
	    const setOpen = open => {
	      if (!toggle || !panel) return;
	      panel.classList.toggle('is-open', open);
	      toggle.setAttribute('aria-expanded', String(open));
	    };
	    toggle?.addEventListener('click', () => setOpen(!panel.classList.contains('is-open')));
	    done?.addEventListener('click', () => {
	      setOpen(false);
	      toggle?.focus();
	    });
	    document.addEventListener('keydown', event => {
	      if (event.key === 'Escape' && panel?.classList.contains('is-open')) {
	        setOpen(false);
	        toggle.focus();
	      }
	    });
	    document.addEventListener('click', event => {
	      if (panel?.classList.contains('is-open') && !event.target.closest('.portfolio-grid__filter-bar')) setOpen(false);
	    });
	    const apply = () => {
	      let visible = 0;
	      items.forEach(item => {
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
	      resets.forEach(reset => {
	        if (reset.closest('.portfolio-grid__panel-actions')) reset.hidden = chosen === 0;
	      });
	      if (done) done.firstChild.textContent = (done.dataset.template || '%d').replace('%d', visible);
	      if (count) count.textContent = (count.dataset.template || '%d').replace('%d', visible);
	    };
	    groups.forEach(group => {
	      const tax = group.dataset.filterGroup;
	      active[tax] = '';
	      group.querySelectorAll('[data-filter]').forEach(chip => {
	        chip.addEventListener('click', () => {
	          active[tax] = chip.dataset.filter;
	          group.querySelectorAll('[data-filter]').forEach(c => c.setAttribute('aria-pressed', String(c === chip)));
	          apply();
	        });
	      });
	    });
	    resets.forEach(reset => reset.addEventListener('click', () => {
	      groups.forEach(group => {
	        active[group.dataset.filterGroup] = '';
	        group.querySelectorAll('[data-filter]').forEach(c => c.setAttribute('aria-pressed', String(c.dataset.filter === '')));
	      });
	      apply();
	    }));
	    apply();
	  });
	}

	/** Staggered entrance reveals: adds .is-in to .reveal elements as they scroll into view. */
	function initReveal() {
	  const els = document.querySelectorAll('.reveal');
	  if (!els.length) return;
	  if (!('IntersectionObserver' in window)) {
	    els.forEach(el => el.classList.add('is-in'));
	    return;
	  }
	  const io = new IntersectionObserver(entries => {
	    entries.forEach(entry => {
	      if (entry.isIntersecting) {
	        entry.target.classList.add('is-in');
	        io.unobserve(entry.target);
	      }
	    });
	  }, {
	    rootMargin: '0px 0px -8% 0px'
	  });
	  els.forEach(el => io.observe(el));
	}

	/**
	 * Conversion tracking for Contact Form 7. After a successful send CF7 fires
	 * `wpcf7mailsent` on the form's wrapper; push a GTM/GA4 `generate_lead` event with
	 * the chosen topic (the visible label, not the piped recipient addresses), and follows the
	 * form's thank-you page when it has one.
	 */
	function initContactForm() {
	  document.addEventListener('wpcf7mailsent', event => {
	    const form = event.target.querySelector('form') || event.target;
	    const select = form.querySelector('select[name="topic"]');
	    const topic = select ? select.options[select.selectedIndex]?.text || '' : '';
	    // the contact form carries a topic; the project gate and access request forms identify themselves by container
	    const kind = select ? 'contact' : form.closest('[data-gate-form]') ? 'project-gate' : 'project-access-request';
	    window.dataLayer = window.dataLayer || [];
	    window.dataLayer.push({
	      event: 'generate_lead',
	      form_id: kind,
	      enquiry_topic: topic
	    });

	    // A form with `thank_you_page: /path/` in its Additional Settings sends the visitor there (inc/cf7.php);
	    // the short wait lets the tracking event above go out first.
	    const redirect = event.detail?.apiResponse?.redirect_url;
	    if (redirect) {
	      window.setTimeout(() => {
	        window.location.href = redirect;
	      }, 400);
	    }
	  });
	}

	/**
	 * Sticky header: adds .is-scrolled to #masthead once the page has scrolled a little, which
	 * inverts its colours (see src/css/nav.css). Runs once on load too, so a page restored
	 * mid-scroll starts in the right state.
	 *
	 * Two thresholds (on at 48px, off below 16px) so it cannot flicker when the scroll position
	 * hovers around a single trigger point.
	 */
	function initHeaderScroll() {
	  const header = document.getElementById('masthead');
	  if (!header) return;
	  let ticking = false;
	  const update = () => {
	    const y = window.scrollY;
	    if (y > 48) header.classList.add('is-scrolled');else if (y < 16) header.classList.remove('is-scrolled');
	    ticking = false;
	  };
	  window.addEventListener('scroll', () => {
	    if (!ticking) {
	      ticking = true;
	      window.requestAnimationFrame(update);
	    }
	  }, {
	    passive: true
	  });
	  update();
	}

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
	function initLenis() {
	  if (!window.Lenis) return;
	  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
	  document.querySelectorAll('dialog').forEach(dialog => dialog.setAttribute('data-lenis-prevent', ''));
	  const lenis = new window.Lenis({
	    anchors: true
	  });
	  window.lenis = lenis;
	  const raf = time => {
	    lenis.raf(time);
	    window.requestAnimationFrame(raf);
	  };
	  window.requestAnimationFrame(raf);
	}

	/**
	 * Site search (/search/). Typing fetches matches from lc-mdsx2026/v1/search (inc/search.php) and
	 * swaps the results in, grouped by type with the matched words wrapped in <mark>; chips narrow the
	 * list to one type, and the address is kept in step (?q=…) so a search can be shared. Without JS
	 * the form submits to /search/?q=… and the block renders the same results on the server.
	 */
	function initSiteSearch() {
	  const root = document.querySelector('[data-site-search]');
	  if (!root) return;
	  const input = root.querySelector('[data-search-input]');
	  const form = root.querySelector('form');
	  const filters = root.querySelector('[data-search-filters]');
	  const results = root.querySelector('[data-search-results]');
	  const endpoint = root.dataset.endpoint;
	  if (!input || !results || !endpoint) return;
	  const LABELS = {
	    portfolio: 'Projects',
	    page: 'Pages',
	    team: 'People',
	    post: 'News'
	  };
	  const ORDER = ['portfolio', 'page', 'team', 'post'];
	  let items = [];
	  let type = '';
	  let words = [];
	  let timer = null;
	  let pending = null;
	  const escapeRegExp = text => text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
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
	    items.forEach(item => counts[item.type] = (counts[item.type] || 0) + 1);
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
	    ORDER.filter(t => counts[t]).forEach(t => chip(t, `${LABELS[t]} (${counts[t]})`));
	    filters.hidden = false;
	  };
	  const renderItem = item => {
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
	    const shown = type ? items.filter(item => item.type === type) : items;
	    const status = el('p', 'site-search__status');
	    status.setAttribute('role', 'status');
	    status.textContent = shown.length ? `${shown.length} ${shown.length === 1 ? 'result' : 'results'}` : 'No results. Try different or fewer words.';
	    results.appendChild(status);
	    ORDER.forEach(t => {
	      const group = shown.filter(item => item.type === t);
	      if (!group.length) return;
	      const section = el('section', 'site-search__group');
	      section.dataset.type = t;
	      section.appendChild(el('h2', '', LABELS[t]));
	      const list = el('ul', 'site-search__list');
	      group.forEach(item => list.appendChild(renderItem(item)));
	      section.appendChild(list);
	      results.appendChild(section);
	    });
	  };
	  const setUrl = query => {
	    const url = new URL(window.location.href);
	    if (query) url.searchParams.set('q', query);else url.searchParams.delete('q');
	    window.history.replaceState(null, '', url);
	  };
	  const run = () => {
	    const query = input.value.trim();
	    setUrl(query);
	    pending?.abort();
	    words = query.split(/\s+/).filter(word => word.length > 1);
	    if (words.length === 0) {
	      items = [];
	      results.textContent = '';
	      filters.hidden = true;
	      results.removeAttribute('aria-busy');
	      return;
	    }
	    pending = new AbortController();
	    results.setAttribute('aria-busy', 'true');
	    fetch(`${endpoint}?q=${encodeURIComponent(query)}`, {
	      signal: pending.signal
	    }).then(response => response.ok ? response.json() : null).then(data => {
	      if (!data) return;
	      items = data.results || [];
	      type = '';
	      render();
	      results.removeAttribute('aria-busy');
	    }).catch(error => {
	      if (error.name !== 'AbortError') results.removeAttribute('aria-busy');
	    });
	  };
	  input.addEventListener('input', () => {
	    clearTimeout(timer);
	    timer = setTimeout(run, 220);
	  });
	  form.addEventListener('submit', event => {
	    event.preventDefault();
	    clearTimeout(timer);
	    run();
	  });
	  // Back/forward or a fresh load with ?q=: the server already rendered the results, so only wire the chips up.
	  if (input.value.trim()) {
	    run();
	  }
	}

	/**
	 * Lead-capture gate on portfolio projects: the modal opens by itself once (the page behind it only
	 * ever holds the public teaser, the locked content is not in the page), and a successful Contact
	 * Form 7 send reloads the page so the server, now holding the signed cookie, returns the full
	 * case study. dialog.js wires the open and close buttons.
	 */
	function initProjectGate() {
	  const dialog = document.querySelector('dialog[data-gate-auto]');
	  if (!dialog) return;
	  const lenisStop = () => window.lenis?.stop();
	  const lenisStart = () => window.lenis?.start();
	  dialog.addEventListener('close', lenisStart);
	  dialog.addEventListener('cancel', lenisStart);
	  document.querySelectorAll('[data-dialog-target="' + dialog.id + '"]').forEach(button => button.addEventListener('click', lenisStop));

	  // Give the page a moment to settle before the modal appears.
	  window.setTimeout(() => {
	    if (!dialog.open) {
	      dialog.showModal();
	      dialog.querySelector('input[type="email"]')?.focus(); // rather than the close button
	      lenisStop();
	    }
	  }, 700);

	  // A sent email unlocks; so does a failed delivery (the server grants access either way).
	  ['wpcf7mailsent', 'wpcf7mailfailed'].forEach(name => dialog.addEventListener(name, () => {
	    window.setTimeout(() => {
	      window.location.hash = 'project-details';
	      window.location.reload();
	    }, 900);
	  }));
	}

	/**
	 * Project page motion helpers.
	 *
	 * - isStatic(): true for prefers-reduced-motion or a `?static` query (used for design capture).
	 *   Switches off the hero push-in, the pinned sketchbook strip (falls back to native scroll), the compare
	 *   sweep, parallax and autoplaying loops; `html.is-static` mirrors it for CSS.
	 * - Reel arrows: scroll the "on the floor" row by 60% of its width.
	 * - Cinematic image: light scrubbed parallax (the image is 120% tall inside a clipped box).
	 */
	const isStatic = () => /[?&]static/.test(window.location.search) || window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	function initProjectMotion() {
	  if (isStatic()) document.documentElement.classList.add('is-static');
	  document.querySelectorAll('[data-reel-row]').forEach(row => {
	    const root = row.closest('.floor');
	    root?.querySelectorAll('[data-reel]').forEach(button => {
	      button.addEventListener('click', () => {
	        row.scrollBy({
	          left: Number(button.dataset.reel) * row.clientWidth * 0.6,
	          behavior: isStatic() ? 'auto' : 'smooth'
	        });
	      });
	    });
	  });
	  if (isStatic()) return;
	  const cines = [...document.querySelectorAll('[data-cine]')];
	  if (!cines.length) return;
	  let ticking = false;
	  const update = () => {
	    const vh = window.innerHeight;
	    cines.forEach(cine => {
	      const rect = cine.getBoundingClientRect();
	      if (rect.bottom < 0 || rect.top > vh) return;
	      const progress = (vh - rect.top) / (vh + rect.height);
	      const img = cine.querySelector('[data-cine-img]');
	      if (img) img.style.transform = `translate3d(0, ${((progress - 0.5) * 10).toFixed(2)}%, 0)`;
	    });
	    ticking = false;
	  };
	  window.addEventListener('scroll', () => {
	    if (!ticking) {
	      ticking = true;
	      window.requestAnimationFrame(update);
	    }
	  }, {
	    passive: true
	  });
	  update();
	}

	/**
	 * Video handling shared by the project hero, the film block and the reels.
	 *
	 * Loops (data-video="loop"): muted, looping, autoplaying; paused when scrolled offscreen; a visible pause
	 * control on any clip over 5 seconds (WCAG 2.2.2). In static mode (reduced motion / ?static) they show the poster.
	 *
	 * Films ([data-film]): click the play button to play in place, with sound and controls. A Vimeo ID
	 * (data-vimeo) loads the Vimeo player only after that click, so nothing third-party loads before interaction.
	 * The hero also has a Close button that restores the page copy.
	 */
	function initVideo() {
	  const loops = [...document.querySelectorAll('video[data-video="loop"]')];
	  const still = isStatic();
	  loops.forEach(video => {
	    const pause = video.parentElement.querySelector('[data-video-pause]');
	    let userPaused = false;
	    if (still) {
	      video.removeAttribute('autoplay');
	      video.pause();
	      return;
	    }
	    const sync = () => {
	      if (pause) {
	        pause.setAttribute('aria-pressed', String(userPaused));
	        pause.querySelector('.visually-hidden').textContent = userPaused ? 'Play video' : 'Pause video';
	      }
	    };
	    const reveal = () => {
	      if (pause && video.duration > 5) pause.hidden = false;
	    };
	    if (video.readyState >= 1) reveal();else video.addEventListener('loadedmetadata', reveal, {
	      once: true
	    });
	    pause?.addEventListener('click', () => {
	      userPaused = !userPaused;
	      if (userPaused) video.pause();else video.play().catch(() => {});
	      sync();
	    });
	    if ('IntersectionObserver' in window) {
	      new IntersectionObserver(entries => {
	        entries.forEach(entry => {
	          if (entry.isIntersecting && !userPaused) video.play().catch(() => {});else video.pause();
	        });
	      }, {
	        threshold: 0.15
	      }).observe(video);
	    }
	  });
	  document.querySelectorAll('[data-film]').forEach(root => {
	    const play = root.querySelector('[data-film-play]');
	    const close = root.querySelector('[data-film-close]');
	    const media = root.querySelector('[data-film-media]');
	    const video = media?.querySelector('video[data-video="film"]');
	    const vimeo = root.dataset.vimeo;
	    let frame = null;
	    play?.addEventListener('click', () => {
	      root.classList.add('is-playing');
	      if (vimeo) {
	        frame = document.createElement('iframe');
	        frame.src = `https://player.vimeo.com/video/${encodeURIComponent(vimeo)}?dnt=1&title=0&byline=0&portrait=0&autoplay=1`;
	        frame.allow = 'autoplay; fullscreen; picture-in-picture';
	        frame.allowFullscreen = true;
	        frame.title = root.getAttribute('aria-label') || 'Film';
	        media.appendChild(frame);
	      } else if (video) {
	        video.controls = true;
	        video.play().catch(() => {});
	      }
	      (close || video || frame)?.focus?.();
	    });
	    close?.addEventListener('click', () => {
	      video?.pause();
	      if (video) video.controls = false;
	      frame?.remove();
	      frame = null;
	      root.classList.remove('is-playing');
	      play?.focus();
	    });
	  });
	}

	/**
	 * Compare slider (idea to reality): pointer drag anywhere on the image, arrow keys (5% steps) on the grip.
	 * The first time it scrolls into view it sweeps once from 85% to 50% so people see it moves.
	 */
	function initCompare() {
	  document.querySelectorAll('[data-compare]').forEach(box => {
	    const grip = box.querySelector('[data-compare-grip]');
	    let pos = 50;
	    let dragging = false;
	    let touched = false;
	    const set = value => {
	      pos = Math.max(0, Math.min(100, value));
	      box.style.setProperty('--pos', `${pos}%`);
	      grip?.setAttribute('aria-valuenow', String(Math.round(pos)));
	    };
	    const fromPointer = event => {
	      const rect = box.getBoundingClientRect();
	      set((event.clientX - rect.left) / rect.width * 100);
	    };
	    box.addEventListener('pointerdown', event => {
	      dragging = touched = true;
	      box.setPointerCapture(event.pointerId);
	      fromPointer(event);
	    });
	    box.addEventListener('pointermove', event => dragging && fromPointer(event));
	    box.addEventListener('pointerup', () => dragging = false);
	    box.addEventListener('pointercancel', () => dragging = false);
	    grip?.addEventListener('keydown', event => {
	      if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
	        touched = true;
	        set(pos + (event.key === 'ArrowLeft' ? -5 : 5));
	        event.preventDefault();
	      }
	    });
	    if (isStatic() || !('IntersectionObserver' in window)) return;
	    const sweep = new IntersectionObserver(entries => {
	      if (!entries[0].isIntersecting) return;
	      sweep.disconnect();
	      const start = performance.now();
	      const step = now => {
	        if (touched) return;
	        const t = Math.min(1, (now - start) / 1400);
	        const eased = t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
	        set(85 - 35 * eased);
	        if (t < 1) window.requestAnimationFrame(step);
	      };
	      set(85);
	      window.requestAnimationFrame(step);
	    }, {
	      rootMargin: '0px 0px -30% 0px'
	    });
	    sweep.observe(box);
	  });
	}

	/**
	 * Concept sketchbook strip. On desktop (761px up) the strip is pinned with position: sticky and
	 * scrubbed sideways by the page scroll, with a "03 / 08" counter and progress track. Below that, or with
	 * reduced motion, the strip stays a native scroll-snap row. No scroll library: sticky + one rAF handler
	 * keeps it compatible with the Lenis smooth scroll.
	 */
	function initSketchbook() {
	  const tracks = [...document.querySelectorAll('[data-sketchbook]')];
	  if (!tracks.length) return;
	  const wide = window.matchMedia('(min-width: 761px)');
	  const setup = track => {
	    const pin = track.querySelector('.sketchbook__pin');
	    const scroller = track.querySelector('[data-sketchbook-scroller]');
	    const strip = track.querySelector('[data-sketchbook-strip]');
	    const bar = track.querySelector('[data-sketchbook-bar]');
	    const count = track.querySelector('[data-sketchbook-count]');
	    const total = strip.children.length;
	    let dist = 0;
	    let top = 0;
	    let ticking = false;
	    const measure = () => {
	      if (!track.classList.contains('is-pinned')) return;
	      dist = Math.max(0, strip.scrollWidth - scroller.clientWidth);
	      top = parseFloat(window.getComputedStyle(pin).top) || 0;
	      track.style.height = `${pin.offsetHeight + dist}px`;
	      update();
	    };
	    const update = () => {
	      ticking = false;
	      if (!track.classList.contains('is-pinned') || !dist) return;
	      const progress = Math.max(0, Math.min(1, (top - track.getBoundingClientRect().top) / dist));
	      strip.style.transform = `translate3d(${(-progress * dist).toFixed(1)}px, 0, 0)`;
	      if (bar) bar.style.width = `${(100 / total + progress * (100 - 100 / total)).toFixed(1)}%`;
	      if (count) count.textContent = `${String(Math.min(total, 1 + Math.round(progress * (total - 1)))).padStart(2, '0')} / ${String(total).padStart(2, '0')}`;
	    };
	    const onScroll = () => {
	      if (!ticking) {
	        ticking = true;
	        window.requestAnimationFrame(update);
	      }
	    };
	    const meta = track.querySelector('.sketchbook__meta');
	    const apply = () => {
	      // a strip that fits the page (or just one or two sketches) is laid out as a static row: no pin, no counter
	      const fits = track.classList.contains('sketchbook__track--few') || strip.scrollWidth <= scroller.clientWidth + 8;
	      track.classList.toggle('is-fit', fits);
	      if (meta) meta.hidden = fits;
	      const on = wide.matches && !isStatic() && !fits;
	      track.classList.toggle('is-pinned', on);
	      if (!on) {
	        track.style.height = '';
	        strip.style.transform = '';
	      }
	      measure();
	    };

	    // not pinned (phone, reduced motion): the counter and progress bar follow the strip's own scrolling
	    const onStripScroll = () => {
	      if (track.classList.contains('is-pinned')) return;
	      const max = scroller.scrollWidth - scroller.clientWidth;
	      if (max <= 0) return;
	      const progress = Math.max(0, Math.min(1, scroller.scrollLeft / max));
	      if (bar) bar.style.width = `${(100 / total + progress * (100 - 100 / total)).toFixed(1)}%`;
	      if (count) count.textContent = `${String(Math.min(total, 1 + Math.round(progress * (total - 1)))).padStart(2, '0')} / ${String(total).padStart(2, '0')}`;
	    };
	    scroller.addEventListener('scroll', onStripScroll, {
	      passive: true
	    });
	    window.addEventListener('scroll', onScroll, {
	      passive: true
	    });
	    window.addEventListener('resize', measure);
	    window.addEventListener('load', measure);
	    wide.addEventListener('change', apply);
	    apply();
	  };
	  tracks.forEach(setup);
	}

	/**
	 * Journey nav: marks the active stage, marks earlier ones done, and fills the progress bar across the whole
	 * journey (first stage's top to last stage's bottom).
	 */
	function initJourneyNav() {
	  const nav = document.querySelector('[data-journey-nav]');
	  const stages = [...document.querySelectorAll('[data-stage]')];
	  if (!nav || !stages.length) return;
	  const links = [...nav.querySelectorAll('[data-journey-link]')];
	  const bar = nav.querySelector('[data-journey-bar]');
	  let ticking = false;
	  const update = () => {
	    ticking = false;
	    const y = window.scrollY;
	    const line = window.innerHeight * 0.35;
	    let active = -1;
	    stages.forEach((stage, i) => {
	      if (stage.getBoundingClientRect().top <= line) active = i;
	    });
	    links.forEach((link, i) => {
	      link.classList.toggle('on', i === active);
	      link.classList.toggle('done', i < active);
	      if (i === active) link.setAttribute('aria-current', 'step');else link.removeAttribute('aria-current');
	    });
	    const first = stages[0].getBoundingClientRect().top + y;
	    const last = stages[stages.length - 1];
	    const end = last.getBoundingClientRect().bottom + y - window.innerHeight;
	    const progress = end > first ? Math.max(0, Math.min(1, (y - first + line) / (end - first + line))) : 0;
	    if (bar) bar.style.width = `${(progress * 100).toFixed(1)}%`;

	    // keep the active step in view in the horizontally scrolling mobile bar
	    const current = links[active];
	    if (current && current.offsetParent && nav.scrollWidth > nav.clientWidth) current.scrollIntoView({
	      inline: 'nearest',
	      block: 'nearest'
	    });
	  };
	  window.addEventListener('scroll', () => {
	    if (!ticking) {
	      ticking = true;
	      window.requestAnimationFrame(update);
	    }
	  }, {
	    passive: true
	  });
	  update();
	}

	/**
	 * Global lightbox. Every img[data-lb] on the page opens it; arrows, Escape and the left / right keys work,
	 * with a counter, the stage label and the alt text. Focus moves to Close on open and back to the image on close.
	 *
	 * Looping clips in the sliders and grids (video[data-lb-video]) join the same sequence. On phones, tapping
	 * one opens it in the lightbox with controls and sound, uncropped; on larger screens it keeps playing inline.
	 */
	function initLightbox() {
	  const box = document.getElementById('lb');
	  const items = [...document.querySelectorAll('img[data-lb], video[data-lb-video]')];
	  if (!box || !items.length) return;
	  const full = box.querySelector('[data-lb-img]');
	  const player = box.querySelector('[data-lb-video-el]');
	  const stage = box.querySelector('[data-lb-stage]');
	  const caption = box.querySelector('[data-lb-cap]');
	  const count = box.querySelector('[data-lb-count]');
	  const closeButton = box.querySelector('[data-lb-close]');
	  const phone = window.matchMedia('(max-width: 760px)');
	  let current = 0;
	  let opener = null;
	  const stopPlayer = () => {
	    player.pause();
	    player.removeAttribute('src');
	    player.removeAttribute('poster');
	    player.load();
	  };
	  const show = index => {
	    current = (index + items.length) % items.length;
	    const item = items[current];
	    const isVideo = item.tagName === 'VIDEO';
	    stopPlayer();
	    full.hidden = isVideo;
	    player.hidden = !isVideo;
	    if (isVideo) {
	      player.src = item.currentSrc || item.querySelector('source')?.src || '';
	      if (item.poster) player.poster = item.poster;
	      player.style.aspectRatio = item.style.getPropertyValue('--ar') || '';
	      player.play().catch(() => {});
	    } else {
	      full.src = item.currentSrc || item.src;
	      full.alt = item.alt;
	    }
	    stage.textContent = item.dataset.lbStage || '';
	    caption.textContent = isVideo ? item.getAttribute('aria-label') || '' : item.alt;
	    count.textContent = `${current + 1} / ${items.length}`;
	  };
	  const open = index => {
	    opener = items[index];
	    show(index);
	    box.hidden = false;
	    document.documentElement.classList.add('lb-open');
	    window.lenis?.stop();
	    closeButton.focus();
	  };
	  const close = () => {
	    stopPlayer();
	    box.hidden = true;
	    document.documentElement.classList.remove('lb-open');
	    window.lenis?.start();
	    opener?.focus();
	  };
	  items.forEach((item, index) => {
	    const inline = () => item.tagName === 'VIDEO' && !phone.matches; // clips keep playing inline on larger screens
	    item.tabIndex = 0;
	    item.setAttribute('role', 'button');
	    item.addEventListener('click', () => {
	      if (!inline()) open(index);
	    });
	    item.addEventListener('keydown', event => {
	      if ((event.key === 'Enter' || event.key === ' ') && !inline()) {
	        event.preventDefault();
	        open(index);
	      }
	    });
	  });
	  closeButton.addEventListener('click', close);
	  box.querySelector('[data-lb-prev]').addEventListener('click', () => show(current - 1));
	  box.querySelector('[data-lb-next]').addEventListener('click', () => show(current + 1));
	  box.addEventListener('click', event => {
	    if (event.target === box) close();
	  });
	  document.addEventListener('keydown', event => {
	    if (box.hidden) return;
	    if (event.key === 'Escape') close();
	    if (event.key === 'ArrowLeft') show(current - 1);
	    if (event.key === 'ArrowRight') show(current + 1);
	    if (event.key === 'Tab') {
	      // keep focus inside the dialog
	      const focusable = [...box.querySelectorAll('button, video[controls]:not([hidden])')];
	      const first = focusable[0];
	      const last = focusable[focusable.length - 1];
	      if (event.shiftKey && document.activeElement === first) {
	        event.preventDefault();
	        last.focus();
	      } else if (!event.shiftKey && document.activeElement === last) {
	        event.preventDefault();
	        first.focus();
	      }
	    }
	  });
	}

	document.addEventListener('DOMContentLoaded', () => {
	  // tells the failsafe in header.php that the script ran; if it never does, content is revealed anyway
	  document.documentElement.classList.add('js-ready');
	  initNavToggle();
	  initNavDropdowns();
	  initDialogs();
	  initHeroReel();
	  initPortfolioFilter();
	  initReveal();
	  initContactForm();
	  initHeaderScroll();
	  initLenis();
	  initSiteSearch();
	  initProjectGate();
	  initProjectMotion();
	  initVideo();
	  initCompare();
	  initSketchbook();
	  initJourneyNav();
	  initLightbox();
	});

})();
//# sourceMappingURL=theme.js.map
