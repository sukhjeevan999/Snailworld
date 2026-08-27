/**
 * Customizer live-preview bindings (postMessage) — updates CSS custom
 * properties directly in the preview iframe without a full page reload
 * for colors and base font size; text settings bind normally.
 */
( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.customize ) {
		return;
	}

	wp.customize( 'blogname', function ( value ) {
		value.bind( function ( to ) {
			document.querySelectorAll( '.site-title a' ).forEach( function ( el ) {
				el.textContent = to;
			} );
		} );
	} );

	wp.customize( 'blogdescription', function ( value ) {
		value.bind( function ( to ) {
			var el = document.querySelector( '.site-description' );
			if ( el ) {
				el.textContent = to;
			}
		} );
	} );

	var colorVarMap = {
		sw_color_base: '--sw-base',
		sw_color_primary: '--sw-primary',
		sw_color_secondary: '--sw-secondary',
		sw_color_accent: '--sw-accent',
		sw_color_text: '--sw-text',
		sw_color_highlight: '--sw-highlight',
	};

	Object.keys( colorVarMap ).forEach( function ( setting ) {
		wp.customize( setting, function ( value ) {
			value.bind( function ( to ) {
				document.documentElement.style.setProperty( colorVarMap[ setting ], to );
			} );
		} );
	} );

	wp.customize( 'sw_font_size_base', function ( value ) {
		value.bind( function ( to ) {
			document.documentElement.style.setProperty( '--sw-font-size-base', to / 100 + 'rem' );
		} );
	} );
} )( window.wp );
