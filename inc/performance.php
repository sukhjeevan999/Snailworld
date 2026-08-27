<?php
/**
 * Core Web Vitals / performance hygiene: lazy-loading, lean head output,
 * async/defer helpers, disabling unused core cruft that costs requests.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Trim default <head> output that adds weight without value on a
 * production/AdSense site (emoji script, oEmbed discovery links, etc).
 */
function snailworld_trim_head() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'rest_output_link_wp_head' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'snailworld_trim_head' );

/**
 * Ensure native lazy-loading stays on for content/theme images.
 */
add_filter( 'wp_lazy_loading_enabled', '__return_true' );

/**
 * Preconnect to Google Fonts host when a font pair is enqueued, so the
 * TLS handshake overlaps with CSS parsing instead of blocking it.
 */
function snailworld_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
		$urls[] = 'https://fonts.googleapis.com';
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'snailworld_resource_hints', 10, 2 );

/**
 * Async decoding hint on post thumbnails/content images for less
 * main-thread contention while decoding.
 */
function snailworld_img_attributes( $attr ) {
	$attr['decoding'] = 'async';
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'snailworld_img_attributes' );

/**
 * Mark the first (likely above-the-fold) post thumbnail as eager/high
 * priority instead of lazy, avoiding an LCP penalty.
 */
function snailworld_maybe_eager_load_lcp( $attr, $attachment, $size ) {
	static $count = 0;
	if ( in_array( $size, array( 'snailworld-hero', 'snailworld-featured' ), true ) && 0 === $count ) {
		$attr['loading']       = 'eager';
		$attr['fetchpriority'] = 'high';
		$count++;
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'snailworld_maybe_eager_load_lcp', 10, 3 );

/**
 * Skip the block-library duotone inline SVG filters payload on the
 * front end unless a page actually needs it.
 */
function snailworld_dequeue_unused_assets() {
	if ( ! is_admin() ) {
		wp_dequeue_style( 'wp-block-library-theme' );
	}
}
add_action( 'wp_enqueue_scripts', 'snailworld_dequeue_unused_assets', 20 );
