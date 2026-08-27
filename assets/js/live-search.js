/**
 * AJAX live search — debounced fetch against the theme's REST search
 * endpoint, rendered as garden/snail-icon result cards inside the search panel.
 */
( function () {
	'use strict';

	if ( typeof snailworldSearch === 'undefined' ) {
		return;
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		var form = document.querySelector( '[data-sw-search-form]' );
		var input = document.querySelector( '[data-sw-search-input]' );
		var resultsBox = document.querySelector( '[data-sw-search-results]' );
		if ( ! form || ! input || ! resultsBox ) {
			return;
		}

		var debounceTimer = null;
		var controller = null;

		input.addEventListener( 'input', function () {
			var query = input.value.trim();

			window.clearTimeout( debounceTimer );

			if ( query.length < 2 ) {
				resultsBox.innerHTML = '';
				return;
			}

			debounceTimer = window.setTimeout( function () {
				runSearch( query );
			}, 300 );
		} );

		form.addEventListener( 'submit', function ( e ) {
			// Let a genuinely empty query fall through to normal search,
			// otherwise keep the user inside the live-results panel.
			if ( input.value.trim().length >= 2 ) {
				e.preventDefault();
			}
		} );

		function runSearch( query ) {
			if ( controller ) {
				controller.abort();
			}
			controller = ( 'AbortController' in window ) ? new AbortController() : null;

			resultsBox.innerHTML = '<p class="sw-search-status">' + escapeHtml( snailworldSearch.searching ) + '</p>';

			fetch( snailworldSearch.restUrl + '?q=' + encodeURIComponent( query ), {
				signal: controller ? controller.signal : undefined,
				headers: { Accept: 'application/json' },
			} )
				.then( function ( response ) {
					if ( ! response.ok ) {
						throw new Error( 'Search request failed' );
					}
					return response.json();
				} )
				.then( function ( items ) {
					renderResults( items );
				} )
				.catch( function ( err ) {
					if ( err && err.name === 'AbortError' ) {
						return;
					}
					resultsBox.innerHTML = '';
				} );
		}

		function renderResults( items ) {
			if ( ! items || ! items.length ) {
				resultsBox.innerHTML = '<p class="sw-search-status">' + escapeHtml( snailworldSearch.noResult ) + '</p>';
				return;
			}

			var frag = document.createDocumentFragment();
			items.forEach( function ( item ) {
				var a = document.createElement( 'a' );
				a.className = 'sw-search-result';
				a.href = item.url;

				var iconWrap = document.createElement( 'span' );
				iconWrap.className = 'sw-cat-icon-wrap';
				iconWrap.style.setProperty( '--sw-cat-color', item.color );
				iconWrap.style.setProperty( '--sw-cat-color-bg', hexToRgba( item.color, 0.15 ) );
				iconWrap.innerHTML = buildIconSvg( item.icon );

				var textWrap = document.createElement( 'span' );
				var title = document.createElement( 'span' );
				title.className = 'sw-search-result-title';
				title.textContent = item.title;
				var excerpt = document.createElement( 'span' );
				excerpt.className = 'sw-search-result-excerpt';
				excerpt.textContent = item.excerpt;
				textWrap.appendChild( title );
				textWrap.appendChild( document.createElement( 'br' ) );
				textWrap.appendChild( excerpt );

				a.appendChild( iconWrap );
				a.appendChild( textWrap );
				frag.appendChild( a );
			} );

			resultsBox.innerHTML = '';
			resultsBox.appendChild( frag );
		}

		function buildIconSvg( key ) {
			var path = ( snailworldSearch.icons && snailworldSearch.icons[ key ] ) ? snailworldSearch.icons[ key ] : snailworldSearch.icons.leaf;
			return '<svg class="sw-icon" viewBox="0 0 24 24" aria-hidden="true">' + path + '</svg>';
		}

		// Precomputed rgba() background for a hex accent color — kept in
		// sync with the PHP-side snailworld_hex_to_rgba() helper so live
		// search results match server-rendered category tints exactly,
		// without depending on the CSS color-mix() function anywhere.
		function hexToRgba( hex, alpha ) {
			var clean = ( hex || '' ).replace( '#', '' );
			if ( clean.length === 3 ) {
				clean = clean.replace( /(.)/g, '$1$1' );
			}
			var match = /^([0-9a-f]{6})$/i.test( clean );
			var r = 0, g = 0, b = 0;
			if ( match ) {
				r = parseInt( clean.substring( 0, 2 ), 16 );
				g = parseInt( clean.substring( 2, 4 ), 16 );
				b = parseInt( clean.substring( 4, 6 ), 16 );
			}
			return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
		}

		function escapeHtml( str ) {
			var div = document.createElement( 'div' );
			div.textContent = str;
			return div.innerHTML;
		}
	} );
} )();
