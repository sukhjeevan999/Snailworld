<?php
/**
 * Virtual /ads.txt served from the AdSense Publisher ID Customizer field,
 * so no manual server file upload is required for AdSense verification.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function snailworld_ads_txt_rewrite() {
	add_rewrite_rule( '^ads\.txt$', 'index.php?sw_ads_txt=1', 'top' );
}
add_action( 'init', 'snailworld_ads_txt_rewrite' );

function snailworld_ads_txt_query_var( $vars ) {
	$vars[] = 'sw_ads_txt';
	return $vars;
}
add_filter( 'query_vars', 'snailworld_ads_txt_query_var' );

function snailworld_ads_txt_render() {
	if ( ! get_query_var( 'sw_ads_txt' ) ) {
		return;
	}

	$publisher_id = trim( get_theme_mod( 'sw_adsense_publisher_id', '' ) );

	header( 'Content-Type: text/plain; charset=utf-8' );

	if ( $publisher_id ) {
		if ( 0 !== strpos( $publisher_id, 'pub-' ) ) {
			$publisher_id = 'pub-' . preg_replace( '/[^0-9]/', '', $publisher_id );
		}
		echo "google.com, {$publisher_id}, DIRECT, f08c47fec0942fa0\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
		// Reminder placeholder: set the Publisher ID in
		// Customize → AdSense Zones → Ads.txt / Publisher to auto-populate this file.
		echo "# Add your AdSense Publisher ID in Customize > AdSense Zones > Ads.txt / Publisher\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	exit;
}
add_action( 'template_redirect', 'snailworld_ads_txt_render' );

/**
 * Flush rewrite rules once when the theme is activated so /ads.txt works
 * immediately without a manual "Save Permalinks" click.
 */
function snailworld_flush_rewrites_on_switch() {
	snailworld_ads_txt_rewrite();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'snailworld_flush_rewrites_on_switch' );
