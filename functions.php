<?php
/**
 * SnailWorld theme bootstrap.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SNAILWORLD_VERSION', '1.1.0' );
define( 'SNAILWORLD_DIR', get_template_directory() );
define( 'SNAILWORLD_URI', get_template_directory_uri() );

/**
 * Theme setup: supports, menus, image sizes.
 */
function snailworld_setup() {
	load_theme_textdomain( 'snailworld', SNAILWORLD_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
		'navigation-widgets',
	) );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'custom-logo', array(
		'height'      => 90,
		'width'       => 220,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	// Elementor / page-builder compatibility.
	add_theme_support( 'elementor' );
	add_theme_support( 'elementor-pro' );

	add_image_size( 'snailworld-card', 640, 420, true );
	add_image_size( 'snailworld-hero', 1200, 900, true );
	add_image_size( 'snailworld-featured', 960, 600, true );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'snailworld' ),
		'footer'  => __( 'Footer Menu', 'snailworld' ),
	) );
}
add_action( 'after_setup_theme', 'snailworld_setup' );

/**
 * Content width for embeds/oEmbed.
 */
function snailworld_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'snailworld_content_width', 900 );
}
add_action( 'after_setup_theme', 'snailworld_content_width', 0 );

/**
 * Register widget areas.
 */
function snailworld_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'snailworld' ),
		'id'            => 'sidebar-primary',
		'description'   => __( 'Appears next to posts and archive listings.', 'snailworld' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	for ( $i = 1; $i <= 4; $i++ ) {
		register_sidebar( array(
			/* translators: %d: footer column number. */
			'name'          => sprintf( __( 'Footer Column %d', 'snailworld' ), $i ),
			'id'            => 'footer-' . $i,
			'description'   => __( 'Footer widget column.', 'snailworld' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}
}
add_action( 'widgets_init', 'snailworld_widgets_init' );

/**
 * Enqueue styles & scripts.
 */
function snailworld_scripts() {
	$font_url = snailworld_get_font_pair_url();
	if ( $font_url ) {
		wp_enqueue_style( 'snailworld-fonts', $font_url, array(), null );
	}

	wp_enqueue_style( 'snailworld-style', get_stylesheet_uri(), array(), SNAILWORLD_VERSION );

	wp_enqueue_script( 'snailworld-main', SNAILWORLD_URI . '/assets/js/main.js', array(), SNAILWORLD_VERSION, true );
	wp_script_add_data( 'snailworld-main', 'strategy', 'defer' );

	if ( get_theme_mod( 'sw_enable_live_search', true ) ) {
		wp_enqueue_script( 'snailworld-search', SNAILWORLD_URI . '/assets/js/live-search.js', array(), SNAILWORLD_VERSION, true );
		wp_script_add_data( 'snailworld-search', 'strategy', 'defer' );

		$icon_keys  = array_merge( array_keys( snailworld_category_icon_choices() ), array( 'leaf' ) );
		$icon_paths = array();
		foreach ( $icon_keys as $key ) {
			$icon_paths[ $key ] = snailworld_icon_paths( $key );
		}

		wp_localize_script( 'snailworld-search', 'snailworldSearch', array(
			'restUrl'   => esc_url_raw( rest_url( 'snailworld/v1/search' ) ),
			'homeUrl'   => esc_url_raw( home_url( '/' ) ),
			'noResult'  => __( 'No results found. Try a different search term.', 'snailworld' ),
			'searching' => __( 'Searching…', 'snailworld' ),
			'icons'     => $icon_paths,
		) );
	}

	if ( is_singular() && get_theme_mod( 'sw_enable_toc', true ) ) {
		wp_enqueue_script( 'snailworld-toc', SNAILWORLD_URI . '/assets/js/toc.js', array(), SNAILWORLD_VERSION, true );
		wp_script_add_data( 'snailworld-toc', 'strategy', 'defer' );
	}

	if ( is_singular() ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'snailworld_scripts' );

/**
 * Add a `js`/`no-js` class to <html> as early as possible.
 */
function snailworld_no_js_script() {
	echo "<script>document.documentElement.classList.remove('no-js');document.documentElement.classList.add('js');</script>\n";
}
add_action( 'wp_head', 'snailworld_no_js_script', 1 );

/**
 * Defer non-critical scripts.
 */
function snailworld_defer_scripts( $tag, $handle, $src ) {
	if ( 'defer' === wp_scripts()->get_data( $handle, 'strategy' ) ) {
		return str_replace( ' src', ' defer src', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'snailworld_defer_scripts', 10, 3 );

/** Required includes. */
require SNAILWORLD_DIR . '/inc/icons.php';
require SNAILWORLD_DIR . '/inc/customizer-controls.php';
require SNAILWORLD_DIR . '/inc/customizer.php';
require SNAILWORLD_DIR . '/inc/customizer-output.php';
require SNAILWORLD_DIR . '/inc/template-tags.php';
require SNAILWORLD_DIR . '/inc/category-meta.php';
require SNAILWORLD_DIR . '/inc/ad-zones.php';
require SNAILWORLD_DIR . '/inc/ajax-search.php';
require SNAILWORLD_DIR . '/inc/toc.php';
require SNAILWORLD_DIR . '/inc/breadcrumbs.php';
require SNAILWORLD_DIR . '/inc/schema-json-ld.php';
require SNAILWORLD_DIR . '/inc/ads-txt.php';
require SNAILWORLD_DIR . '/inc/performance.php';

/**
 * Body classes: header layout, sticky header, footer columns, texture.
 */
function snailworld_body_classes( $classes ) {
	$classes[] = 'header-layout-' . get_theme_mod( 'sw_header_logo_position', 'left' );
	$classes[] = get_theme_mod( 'sw_header_sticky', true ) ? 'header-is-sticky' : 'header-not-sticky';
	$classes[] = 'footer-cols-' . absint( get_theme_mod( 'sw_footer_columns', 4 ) );

	if ( get_theme_mod( 'sw_enable_texture', false ) ) {
		$classes[] = 'has-texture';
	}

	if ( ! is_singular() || ! get_theme_mod( 'sw_enable_toc', true ) ) {
		$classes[] = 'no-toc';
	}

	return $classes;
}
add_filter( 'body_class', 'snailworld_body_classes' );

/**
 * Excerpt tuning.
 */
function snailworld_excerpt_length( $length ) {
	return 26;
}
add_filter( 'excerpt_length', 'snailworld_excerpt_length' );

function snailworld_excerpt_more( $more ) {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'snailworld_excerpt_more' );

/**
 * Register block editor / Customizer-aware color palette for Gutenberg.
 */
function snailworld_editor_settings() {
	add_theme_support( 'editor-color-palette', array(
		array( 'name' => __( 'Garden Base', 'snailworld' ), 'slug' => 'base', 'color' => get_theme_mod( 'sw_color_base', '#F7F3E7' ) ),
		array( 'name' => __( 'Moss Green', 'snailworld' ), 'slug' => 'primary', 'color' => get_theme_mod( 'sw_color_primary', '#52734D' ) ),
		array( 'name' => __( 'Soil Brown', 'snailworld' ), 'slug' => 'secondary', 'color' => get_theme_mod( 'sw_color_secondary', '#8A5E3C' ) ),
		array( 'name' => __( 'Coral Accent', 'snailworld' ), 'slug' => 'accent', 'color' => get_theme_mod( 'sw_color_accent', '#E08966' ) ),
		array( 'name' => __( 'Ink', 'snailworld' ), 'slug' => 'text', 'color' => get_theme_mod( 'sw_color_text', '#2A2823' ) ),
		array( 'name' => __( 'Sand Gold', 'snailworld' ), 'slug' => 'highlight', 'color' => get_theme_mod( 'sw_color_highlight', '#D9AE5D' ) ),
	) );
}
add_action( 'after_setup_theme', 'snailworld_editor_settings' );

function snailworld_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'snailworld_pingback_header' );
