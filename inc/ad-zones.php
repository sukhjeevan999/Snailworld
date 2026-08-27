<?php
/**
 * AdSense-friendly ad zones: header, 2x in-content, sidebar, footer.
 * Each zone is a Customizer on/off toggle + a code textarea, so zero
 * code edits are needed to manage ads.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a given ad zone is enabled.
 */
function snailworld_ad_zone_enabled( $zone ) {
	return (bool) get_theme_mod( 'sw_ad_enable_' . $zone, false );
}

/**
 * Render an ad zone (label + code). Renders nothing if disabled or empty.
 */
function snailworld_ad_zone( $zone ) {
	if ( ! snailworld_ad_zone_enabled( $zone ) ) {
		return;
	}
	$code = get_theme_mod( 'sw_ad_code_' . $zone, '' );
	if ( ! trim( $code ) ) {
		return;
	}
	echo '<div class="sw-ad-zone" data-zone="' . esc_attr( $zone ) . '">';
	echo '<span class="sw-ad-label">' . esc_html__( 'Advertisement', 'snailworld' ) . '</span>';
	echo $code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted admin-entered AdSense markup, see snailworld_sanitize_ad_code().
	echo '</div>';
}

/**
 * Inject the two in-content ad zones after the Nth paragraph of post content.
 * Only runs on single posts, in the main loop, never on excerpts/feeds.
 */
function snailworld_insert_in_content_ads( $content ) {
	if ( is_admin() || ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$zones = array(
		'in_content_1' => 3,
		'in_content_2' => 8,
	);

	foreach ( $zones as $zone => $after_paragraph ) {
		if ( ! snailworld_ad_zone_enabled( $zone ) ) {
			continue;
		}
		$code = get_theme_mod( 'sw_ad_code_' . $zone, '' );
		if ( ! trim( $code ) ) {
			continue;
		}
		$markup  = '<div class="sw-ad-zone" data-zone="' . esc_attr( $zone ) . '"><span class="sw-ad-label">' . esc_html__( 'Advertisement', 'snailworld' ) . '</span>' . $code . '</div>';
		$content = snailworld_insert_after_paragraph( $markup, $after_paragraph, $content );
	}

	return $content;
}
add_filter( 'the_content', 'snailworld_insert_in_content_ads', 20 );

/**
 * Insert markup after the Nth closing </p> tag. Falls back to skipping
 * if the content has fewer paragraphs (avoids an ad landing right under
 * the title on short posts).
 */
function snailworld_insert_after_paragraph( $insertion, $paragraph_id, $content ) {
	$closing_p  = '</p>';
	$paragraphs = explode( $closing_p, $content );

	if ( count( $paragraphs ) < (int) $paragraph_id + 1 ) {
		return $content;
	}

	foreach ( $paragraphs as $index => $paragraph ) {
		if ( trim( $paragraph ) ) {
			$paragraphs[ $index ] .= $closing_p;
		}
		if ( (int) $paragraph_id === $index + 1 ) {
			$paragraphs[ $index ] .= $insertion;
		}
	}

	return implode( '', $paragraphs );
}
