<?php
/**
 * Turns Customizer settings into a small inline CSS block of custom
 * properties. Keeps style.css static/cacheable while staying 100%
 * editable from wp-admin.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Small hex-color helpers. Used so every derived color (surfaces,
 * borders, tinted backgrounds) is computed once in PHP and shipped as
 * a plain rgba()/hex value — no reliance anywhere in this theme on the
 * CSS color-mix() function, which is still unsupported in a meaningful
 * slice of real browsers/webviews and silently drops the whole
 * declaration (not just the color) where it's missing.
 */
function snailworld_hex_to_rgb( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return array( 0, 0, 0 );
	}
	return array_map( 'hexdec', str_split( $hex, 2 ) );
}

/**
 * @param string     $hex   Source color.
 * @param float|string $alpha 0-1 opacity.
 */
function snailworld_hex_to_rgba( $hex, $alpha ) {
	list( $r, $g, $b ) = snailworld_hex_to_rgb( $hex );
	return sprintf( 'rgba(%d,%d,%d,%s)', $r, $g, $b, $alpha );
}

/**
 * Blend $hex with $with_hex, keeping $weight (0-1) of $hex.
 */
function snailworld_blend_hex( $hex, $with_hex, $weight ) {
	list( $r1, $g1, $b1 ) = snailworld_hex_to_rgb( $hex );
	list( $r2, $g2, $b2 ) = snailworld_hex_to_rgb( $with_hex );
	$r = (int) round( $r1 * $weight + $r2 * ( 1 - $weight ) );
	$g = (int) round( $g1 * $weight + $g2 * ( 1 - $weight ) );
	$b = (int) round( $b1 * $weight + $b2 * ( 1 - $weight ) );
	return sprintf( '#%02x%02x%02x', $r, $g, $b );
}

function snailworld_customizer_css() {
	$pairs    = snailworld_font_pairs();
	$pair_key = get_theme_mod( 'sw_font_pair', 'garden' );
	$pair     = isset( $pairs[ $pair_key ] ) ? $pairs[ $pair_key ] : $pairs['garden'];
	$base_pct = absint( get_theme_mod( 'sw_font_size_base', 100 ) );
	$base_pct = $base_pct ? $base_pct : 100;

	$light = array();
	foreach ( snailworld_color_settings() as $id => $conf ) {
		$key           = str_replace( 'sw_color_', '', $id );
		$light[ $key ] = get_theme_mod( $id, $conf[0] );
	}

	$dark = array();
	foreach ( snailworld_color_settings_dark() as $id => $conf ) {
		$key          = str_replace( array( 'sw_color_', '_dark' ), '', $id );
		$dark[ $key ] = get_theme_mod( $id, $conf[0] );
	}

	$map = array(
		'base'      => '--sw-base',
		'primary'   => '--sw-primary',
		'secondary' => '--sw-secondary',
		'accent'    => '--sw-accent',
		'text'      => '--sw-text',
		'highlight' => '--sw-highlight',
	);

	$css = ':root{';
	foreach ( $map as $key => $var ) {
		$css .= $var . ':' . esc_html( $light[ $key ] ) . ';';
	}
	$css .= '--sw-surface:' . snailworld_blend_hex( $light['base'], '#ffffff', 0.88 ) . ';';
	$css .= '--sw-border:' . snailworld_hex_to_rgba( $light['text'], 0.12 ) . ';';
	$css .= '--sw-header-bg:' . snailworld_hex_to_rgba( $light['base'], 0.92 ) . ';';
	$css .= '--sw-hero-glow:' . snailworld_hex_to_rgba( $light['primary'], 0.12 ) . ';';
	$css .= '--sw-cat-color-bg-default:' . snailworld_hex_to_rgba( $light['primary'], 0.15 ) . ';';
	$css .= '--sw-font-heading:' . esc_html( $pair['heading_family'] ) . ';';
	$css .= '--sw-font-body:' . esc_html( $pair['body_family'] ) . ';';
	$css .= '--sw-font-size-base:' . ( $base_pct / 100 ) . 'rem;';
	$css .= '}';

	$css .= '[data-theme="dark"]{';
	foreach ( $map as $key => $var ) {
		$css .= $var . ':' . esc_html( $dark[ $key ] ) . ';';
	}
	$css .= '--sw-surface:' . snailworld_blend_hex( $dark['base'], '#000000', 0.85 ) . ';';
	$css .= '--sw-border:' . snailworld_hex_to_rgba( $dark['text'], 0.14 ) . ';';
	$css .= '--sw-header-bg:' . snailworld_hex_to_rgba( $dark['base'], 0.92 ) . ';';
	$css .= '--sw-hero-glow:' . snailworld_hex_to_rgba( $dark['primary'], 0.14 ) . ';';
	$css .= '--sw-cat-color-bg-default:' . snailworld_hex_to_rgba( $dark['primary'], 0.18 ) . ';';
	$css .= '}';

	if ( get_theme_mod( 'sw_enable_texture', false ) ) {
		// Lightweight inline SVG fibre/dew-grain pattern, no external request.
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120"><filter id="n"><feTurbulence type="fractalNoise" baseFrequency="0.85" numOctaves="2" stitchTiles="stitch"/><feColorMatrix type="saturate" values="0"/></filter><rect width="100%" height="100%" filter="url(%23n)" opacity="0.5"/></svg>';
		$css .= '.has-texture{--sw-texture:url(\'data:image/svg+xml;utf8,' . $svg . '\');}';
	}

	return $css;
}

function snailworld_print_customizer_css() {
	echo '<style id="snailworld-customizer-css">' . snailworld_customizer_css() . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'snailworld_print_customizer_css', 20 );

/**
 * Apply the visitor/site default color-scheme + data-theme attribute
 * on <html> before first paint, driven by a small early inline script
 * plus a fallback cookie so there is no flash of wrong theme.
 */
function snailworld_dark_mode_boot_script() {
	$default = get_theme_mod( 'sw_dark_mode_default', 'auto' );
	?>
	<script>
	(function(){
		try {
			var d = <?php echo wp_json_encode( $default ); ?>;
			var stored = document.cookie.replace(/(?:(?:^|.*;\s*)sw_theme\s*\=\s*([^;]*).*$)|^.*$/, '$1');
			var mode = stored || d;
			if ( mode === 'auto' ) {
				mode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
			}
			if ( mode === 'dark' ) {
				document.documentElement.setAttribute('data-theme','dark');
			}
		} catch(e){}
	})();
	</script>
	<?php
}
add_action( 'wp_head', 'snailworld_dark_mode_boot_script', 1 );
