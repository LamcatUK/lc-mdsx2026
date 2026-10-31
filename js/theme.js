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
	      if (window.lenis) isOpen ? window.lenis.stop() : window.lenis.start();
	      // The submenu is always expanded in the open mobile menu; keep its ARIA state truthful.
	      target.querySelectorAll('.dropdown-toggle').forEach(t => t.setAttribute('aria-expanded', String(isOpen)));
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
	    const active = {};
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
	 * the chosen topic (the visible label, not the piped recipient addresses).
	 */
	function initContactForm() {
	  document.addEventListener('wpcf7mailsent', event => {
	    const form = event.target.querySelector('form') || event.target;
	    const select = form.querySelector('select[name="topic"]');
	    const topic = select ? select.options[select.selectedIndex]?.text || '' : '';
	    window.dataLayer = window.dataLayer || [];
	    window.dataLayer.push({
	      event: 'generate_lead',
	      form_id: 'contact',
	      enquiry_topic: topic
	    });
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

	document.addEventListener('DOMContentLoaded', () => {
	  initNavToggle();
	  initNavDropdowns();
	  initDialogs();
	  initHeroReel();
	  initPortfolioFilter();
	  initReveal();
	  initContactForm();
	  initHeaderScroll();
	  initLenis();
	});

})();
//# sourceMappingURL=theme.js.map
