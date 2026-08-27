<?php
/**
 * Inline garden/snail-themed icon set (Lucide-style line art, 24x24,
 * stroke-based) — used sparingly as accents throughout the theme.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the raw <path>/shape markup (no wrapping <svg>) for a given icon key.
 *
 * @param string $name Icon key.
 * @return string
 */
function snailworld_icon_paths( $name ) {
	$icons = array(
		// Full snail — shell spiral + body/antennae. Used for category
		// defaults, the reading-progress rider, and loading states.
		'snail'        => '<circle cx="15.5" cy="9.6" r="4"/><path d="M15.5 9.6a1.8 1.8 0 1 1-1.8-1.8"/><path d="M3 17.6c0-2.9 2.4-4.9 5.5-4.9h3.7c1.9 0 3.4 1.4 3.4 3.1"/><path d="M5 17.6c-1 0-1.7-.8-1.7-1.7"/><path d="M4.3 12.8 3.7 11"/><path d="M6.3 12.8 6.9 11"/><path d="M2 20.5h11"/>',
		// Shell spiral only — used for dividers / decorative accents.
		'snail-shell'  => '<circle cx="12" cy="12" r="8.5"/><path d="M12 12a4.3 4.3 0 1 1-4.3-4.3"/><path d="M7.7 7.7a1.9 1.9 0 1 1 1.9 1.9"/>',
		'leaf-vine'    => '<path d="M3 15c6-7 13-7 18-13"/><path d="M8.3 12.7c-1.4-1.7-1.2-4.3.6-5.8"/><path d="M14.5 6.5c1.7-1.4 4.3-1.2 5.8.6"/>',
		'leaf'         => '<path d="M5 20c9 0 14-5 14-14-9 0-14 5-14 14Z"/><path d="M5 20c2-6 5-9 9-11"/>',
		'dew-drop'     => '<path d="M12 3.2c4.2 5.1 6.3 8.6 6.3 11.3a6.3 6.3 0 0 1-12.6 0c0-2.7 2.1-6.2 6.3-11.3Z"/>',
		'trowel'       => '<path d="M4.5 19.5 8 16"/><path d="M8.3 15.7 15.8 8.2a2.7 2.7 0 0 0-3.9-3.9L4.4 12a2.7 2.7 0 0 0 3.9 3.9Z"/>',
		'watering-can' => '<path d="M3 14h8a3 3 0 0 0 3-3V9h3a2 2 0 1 1 0 4h-1"/><path d="M4 14v3a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1v-3"/><path d="M6.5 9V7a2 2 0 0 1 2-2h1"/><path d="M17.5 6 19.5 4M18.5 8.3l2.3-.7M17 4.5l1.3-1.7"/>',
		'butterfly'    => '<path d="M12 4.5v15"/><path d="M12 8.3c-.9-2.8-3.7-3.7-5.6-2.8-1.9.9-1.9 3.7 0 5.1 1.9 1.4 4.2.9 5.6-1"/><path d="M12 8.3c.9-2.8 3.7-3.7 5.6-2.8 1.9.9 1.9 3.7 0 5.1-1.9 1.4-4.2.9-5.6-1"/><path d="M12 13c-.7-1.9-2.8-2.4-4.2-1.7-1.4.7-1.4 2.8 0 3.7 1.4.9 3 .6 4.2-1"/><path d="M12 13c.7-1.9 2.8-2.4 4.2-1.7 1.4.7 1.4 2.8 0 3.7-1.4.9-3 .6-4.2-1"/>',
		'pot'          => '<path d="M6.5 9h11l-1.3 9.3a2 2 0 0 1-2 1.7H9.8a2 2 0 0 1-2-1.7L6.5 9Z"/><path d="M5 9h14"/><path d="M9.5 9c-.6-1.6.2-3.4 2.5-3.4S14.6 7.4 14 9"/>',
		// Utility icons.
		'search'       => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
		'close'        => '<path d="M18 6 6 18"/><path d="M6 6l12 12"/>',
		'sun'          => '<circle cx="12" cy="12" r="4.2"/><path d="M12 2.5v2.3M12 19.2v2.3M4.6 4.6l1.6 1.6M17.8 17.8l1.6 1.6M2.5 12h2.3M19.2 12h2.3M4.6 19.4l1.6-1.6M17.8 6.2l1.6-1.6"/>',
		'moon'         => '<path d="M20 14.5A8.5 8.5 0 1 1 9.5 4 6.8 6.8 0 0 0 20 14.5Z"/>',
		'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
		'chevron-right'=> '<path d="m9 6 6 6-6 6"/>',
		'clock'        => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
		'calendar'     => '<rect x="3.5" y="5" width="17" height="16" rx="2.2"/><path d="M8 3v4M16 3v4M3.5 10h17"/>',
		'user'         => '<circle cx="12" cy="8.2" r="3.6"/><path d="M4.5 20c1.4-3.7 4.4-5.6 7.5-5.6s6.1 1.9 7.5 5.6"/>',
		'tag'          => '<path d="M3.5 11.4 12 3h6a2 2 0 0 1 2 2v6l-8.5 8.5a2 2 0 0 1-2.8 0l-5.2-5.2a2 2 0 0 1 0-2.9Z"/><circle cx="15.2" cy="8.8" r="1.4"/>',
		'home'         => '<path d="m4 11 8-7 8 7"/><path d="M6 10v9a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-9"/>',
		'folder'       => '<path d="M3.5 6.5a1.5 1.5 0 0 1 1.5-1.5h4.4l1.8 2H19a1.5 1.5 0 0 1 1.5 1.5v9A1.5 1.5 0 0 1 19 19H5a1.5 1.5 0 0 1-1.5-1.5Z"/>',
		'arrow-right'  => '<path d="M4 12h16"/><path d="m14 6 6 6-6 6"/>',
		'arrow-left'   => '<path d="M20 12H4"/><path d="m10 6-6 6 6 6"/>',
	);

	return isset( $icons[ $name ] ) ? $icons[ $name ] : $icons['leaf'];
}

/**
 * Whitelist of garden/snail icons selectable for categories.
 */
function snailworld_category_icon_choices() {
	return array(
		'snail'        => __( 'Snail', 'snailworld' ),
		'snail-shell'  => __( 'Snail Shell', 'snailworld' ),
		'trowel'       => __( 'Garden Trowel (Care Tips)', 'snailworld' ),
		'watering-can' => __( 'Watering Can', 'snailworld' ),
		'dew-drop'     => __( 'Dew Drop', 'snailworld' ),
		'butterfly'    => __( 'Butterfly', 'snailworld' ),
		'pot'          => __( 'Garden Pot', 'snailworld' ),
		'leaf'         => __( 'Leaf', 'snailworld' ),
	);
}

/**
 * Echo/return an inline <svg> for an icon key.
 *
 * @param string $name  Icon key, see snailworld_icon_paths().
 * @param array  $args  { class, echo, title }.
 * @return string|void
 */
function snailworld_icon( $name, $args = array() ) {
	$args = wp_parse_args( $args, array(
		'class' => 'sw-icon',
		'echo'  => true,
		'title' => '',
	) );

	$title_markup = $args['title'] ? sprintf( '<title>%s</title>', esc_html( $args['title'] ) ) : '';
	$aria         = $args['title'] ? '' : ' aria-hidden="true" focusable="false"';

	$svg = sprintf(
		'<svg class="%1$s" viewBox="0 0 24 24"%2$s>%3$s%4$s</svg>',
		esc_attr( $args['class'] ),
		$aria,
		$title_markup,
		snailworld_icon_paths( $name ) // phpcs:ignore -- static, trusted inline path data.
	);

	if ( $args['echo'] ) {
		echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	return $svg;
}
