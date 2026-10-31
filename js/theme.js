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
	 */
	function initNavToggle() {
	  document.querySelectorAll('.navbar-toggler[aria-controls]').forEach(toggler => {
	    const target = document.getElementById(toggler.getAttribute('aria-controls'));
	    if (!target) return;
	    toggler.addEventListener('click', () => {
	      const isOpen = target.classList.toggle('is-open');
	      toggler.setAttribute('aria-expanded', String(isOpen));
	    });

	    // Close after choosing a link — expected mobile nav behaviour.
	    target.querySelectorAll('a').forEach(link => {
	      link.addEventListener('click', () => {
	        target.classList.remove('is-open');
	        toggler.setAttribute('aria-expanded', 'false');
	      });
	    });
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
	 * Hero reel: crossfades .hero-reel__slide figures every 4.2s. Respects
	 * prefers-reduced-motion and offers a pause control (WCAG 2.2.2).
	 */
	function initHeroReel() {
	  document.querySelectorAll('.hero-reel').forEach(hero => {
	    const slides = hero.querySelectorAll('.hero-reel__slide');
	    if (slides.length < 2) return;
	    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
	    const button = hero.querySelector('[data-reel-pause]');
	    let index = 0;
	    let paused = reduced.matches;
	    let timer = null;
	    const show = next => {
	      slides[index].classList.remove('is-active');
	      index = next % slides.length;
	      slides[index].classList.add('is-active');
	    };
	    const start = () => {
	      stop();
	      timer = window.setInterval(() => show(index + 1), 4200);
	    };
	    const stop = () => window.clearInterval(timer);
	    const sync = () => {
	      if (button) button.setAttribute('aria-pressed', String(paused));
	      paused ? stop() : start();
	    };
	    button?.addEventListener('click', () => {
	      paused = !paused;
	      sync();
	    });
	    document.addEventListener('visibilitychange', () => document.hidden ? stop() : !paused && start());
	    sync();
	  });
	}

	/**
	 * Portfolio grid filtering. Each fieldset[data-filter-group] is a taxonomy;
	 * tiles carry data-{taxonomy}="slug slug". Filters combine (AND across groups).
	 * Every sixth visible tile is widened.
	 */
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
	          if (visible % 6 === 0) item.classList.add('is-wide');
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

	document.addEventListener('DOMContentLoaded', () => {
	  initNavToggle();
	  initNavDropdowns();
	  initDialogs();
	  initHeroReel();
	  initPortfolioFilter();
	  initReveal();
	});

})();
//# sourceMappingURL=theme.js.map
