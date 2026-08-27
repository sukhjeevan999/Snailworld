/**
 * Table of contents: scrollspy (highlights the active section link) and
 * a sane default open/collapsed state per breakpoint.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var toc = document.querySelector( '.sw-toc' );
		if ( ! toc ) {
			return;
		}

		// Collapsed by default on mobile, open on desktop.
		if ( window.matchMedia( '(max-width: 67.99em)' ).matches ) {
			toc.removeAttribute( 'open' );
		}

		var links = Array.prototype.slice.call( toc.querySelectorAll( 'a[data-toc-target]' ) );
		if ( ! links.length || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}

		var headings = links
			.map( function ( link ) {
				return document.getElementById( link.getAttribute( 'data-toc-target' ) );
			} )
			.filter( Boolean );

		if ( ! headings.length ) {
			return;
		}

		var activeId = null;

		function setActive( id ) {
			if ( id === activeId ) {
				return;
			}
			activeId = id;
			links.forEach( function ( link ) {
				link.classList.toggle( 'is-active', link.getAttribute( 'data-toc-target' ) === id );
			} );
		}

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						setActive( entry.target.id );
					}
				} );
			},
			{ rootMargin: '-15% 0px -70% 0px', threshold: 0 }
		);

		headings.forEach( function ( heading ) {
			observer.observe( heading );
		} );
	} );
} )();
