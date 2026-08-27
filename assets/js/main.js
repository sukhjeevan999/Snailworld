/**
 * SnailWorld — core theme interactions.
 * Vanilla JS, no dependencies. Mobile nav, dark-mode toggle, scroll-reveal
 * animations, snail reading-progress bar, sticky header state, back-to-top.
 *
 * Note: the .sw-reveal scroll-in effect this file drives is an enhancement,
 * not a requirement — style.css gives every .sw-reveal element a pure-CSS
 * fallback that reveals it on its own after ~1.2s, so content never stays
 * invisible if this script is delayed or blocked (some hosting/cache
 * optimizers defer all JS until user interaction).
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		initMobileNav();
		initThemeToggle();
		initScrollReveal();
		initHeaderScrollState();
		initReadingProgress();
		initBackToTop();
		initSearchPanel();
	} );

	/* ---------------------------------------------------------
	 * Mobile navigation
	 * --------------------------------------------------------- */
	function initMobileNav() {
		var toggle = document.querySelector( '[data-sw-menu-toggle]' );
		var nav = document.getElementById( 'mobile-navigation' );
		if ( ! toggle || ! nav ) {
			return;
		}
		// Body scroll is intentionally left unlocked while the menu is
		// open: the menu itself has no inner scrollbox (max-height only,
		// overflow visible), so if it ever has more items than fit the
		// screen, the page's own natural scroll is what reveals the rest.
		toggle.addEventListener( 'click', function () {
			var isOpen = nav.classList.toggle( 'is-open' );
			toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		} );

		nav.addEventListener( 'click', function ( e ) {
			if ( e.target.tagName === 'A' ) {
				nav.classList.remove( 'is-open' );
				toggle.setAttribute( 'aria-expanded', 'false' );
			}
		} );

		// Toggle submenus on tap (mobile has no hover).
		nav.querySelectorAll( '.menu-item-has-children > a' ).forEach( function ( link ) {
			link.addEventListener( 'click', function ( e ) {
				var submenu = link.parentNode.querySelector( '.sub-menu' );
				if ( submenu ) {
					e.preventDefault();
					submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
				}
			} );
		} );
	}

	/* ---------------------------------------------------------
	 * Dark mode toggle — persisted via a first-party cookie so it
	 * survives navigation across pages.
	 * --------------------------------------------------------- */
	function setThemeCookie( value ) {
		var maxAge = 60 * 60 * 24 * 365;
		document.cookie = 'sw_theme=' + value + ';path=/;max-age=' + maxAge + ';SameSite=Lax';
	}

	function initThemeToggle() {
		var btn = document.querySelector( '[data-sw-theme-toggle]' );
		if ( ! btn ) {
			return;
		}
		btn.addEventListener( 'click', function () {
			var root = document.documentElement;
			var isDark = root.getAttribute( 'data-theme' ) === 'dark';
			if ( isDark ) {
				root.removeAttribute( 'data-theme' );
				setThemeCookie( 'light' );
			} else {
				root.setAttribute( 'data-theme', 'dark' );
				setThemeCookie( 'dark' );
			}
		} );
	}

	/* ---------------------------------------------------------
	 * Scroll-reveal (CSS-driven, IntersectionObserver only toggles
	 * a class — no animation logic runs in JS). See the file header
	 * note: this is a nicety on top of the guaranteed CSS fallback.
	 * --------------------------------------------------------- */
	function initScrollReveal() {
		var els = document.querySelectorAll( '.sw-reveal' );
		if ( ! els.length ) {
			return;
		}
		if ( ! ( 'IntersectionObserver' in window ) ) {
			els.forEach( function ( el ) {
				el.classList.add( 'is-visible' );
			} );
			return;
		}
		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-visible' );
						observer.unobserve( entry.target );
					}
				} );
			},
			{ rootMargin: '0px 0px -8% 0px', threshold: 0.05 }
		);
		els.forEach( function ( el ) {
			observer.observe( el );
		} );
	}

	/* ---------------------------------------------------------
	 * Sticky header shadow state once the page has scrolled.
	 * --------------------------------------------------------- */
	function initHeaderScrollState() {
		var header = document.getElementById( 'masthead' );
		if ( ! header || ! header.classList.contains( 'is-sticky' ) ) {
			return;
		}
		var ticking = false;
		function update() {
			header.classList.toggle( 'header-scrolled', window.scrollY > 12 );
			ticking = false;
		}
		window.addEventListener(
			'scroll',
			function () {
				if ( ! ticking ) {
					window.requestAnimationFrame( update );
					ticking = true;
				}
			},
			{ passive: true }
		);
		update();
	}

	/* ---------------------------------------------------------
	 * Snail reading-progress bar — a slim bar fixed to the top of
	 * singular posts, with a snail icon riding the leading edge of
	 * the fill as the reader scrolls through the article.
	 * --------------------------------------------------------- */
	function initReadingProgress() {
		var bar = document.querySelector( '[data-sw-progress]' );
		if ( ! bar ) {
			return;
		}
		var fill = bar.querySelector( '[data-sw-progress-fill]' );
		var snail = bar.querySelector( '[data-sw-progress-snail]' );

		var ticking = false;
		function update() {
			var scrollTop = window.scrollY || document.documentElement.scrollTop;
			var docHeight = document.documentElement.scrollHeight - window.innerHeight;
			var progress = docHeight > 0 ? Math.min( 1, Math.max( 0, scrollTop / docHeight ) ) : 0;
			var pct = progress * 100;

			if ( fill ) {
				fill.style.width = pct + '%';
			}
			if ( snail ) {
				snail.style.left = pct + '%';
			}
			bar.classList.toggle( 'is-visible', scrollTop > 80 );
			ticking = false;
		}

		window.addEventListener(
			'scroll',
			function () {
				if ( ! ticking ) {
					window.requestAnimationFrame( update );
					ticking = true;
				}
			},
			{ passive: true }
		);
		update();
	}

	/* ---------------------------------------------------------
	 * Back-to-top button, shown once the page has scrolled down.
	 * --------------------------------------------------------- */
	function initBackToTop() {
		var btn = document.querySelector( '[data-sw-scroll-top]' );
		if ( ! btn ) {
			return;
		}
		var ticking = false;
		function update() {
			btn.classList.toggle( 'is-visible', window.scrollY > 320 );
			ticking = false;
		}
		window.addEventListener(
			'scroll',
			function () {
				if ( ! ticking ) {
					window.requestAnimationFrame( update );
					ticking = true;
				}
			},
			{ passive: true }
		);
		update();

		btn.addEventListener( 'click', function () {
			window.scrollTo( { top: 0, behavior: 'smooth' } );
		} );
	}

	/* ---------------------------------------------------------
	 * Search overlay open/close (markup/results handled by
	 * live-search.js when enabled).
	 * --------------------------------------------------------- */
	function initSearchPanel() {
		var openBtn = document.querySelector( '[data-sw-search-open]' );
		var closeBtn = document.querySelector( '[data-sw-search-close]' );
		var panel = document.querySelector( '[data-sw-search-panel]' );
		if ( ! openBtn || ! panel ) {
			return;
		}
		var input = panel.querySelector( '[data-sw-search-input]' );

		function open() {
			panel.classList.add( 'is-open' );
			document.body.style.overflow = 'hidden';
			if ( input ) {
				window.setTimeout( function () {
					input.focus();
				}, 50 );
			}
		}
		function close() {
			panel.classList.remove( 'is-open' );
			document.body.style.overflow = '';
		}

		openBtn.addEventListener( 'click', open );
		if ( closeBtn ) {
			closeBtn.addEventListener( 'click', close );
		}
		panel.addEventListener( 'click', function ( e ) {
			if ( e.target === panel ) {
				close();
			}
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && panel.classList.contains( 'is-open' ) ) {
				close();
			}
		} );
	}
} )();
