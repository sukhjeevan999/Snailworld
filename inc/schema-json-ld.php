<?php
/**
 * JSON-LD structured data: Organization (sitewide), BreadcrumbList, and
 * Article (single posts). Validate with Google's Rich Results Test after
 * launch — https://search.google.com/test/rich-results
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Organization schema — printed on every page so search engines can tie
 * the whole site back to one publisher entity.
 */
function snailworld_organization_schema() {
	$logo_id = get_theme_mod( 'custom_logo' );
	$schema  = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
	);

	if ( $logo_id ) {
		$logo_src = wp_get_attachment_image_src( $logo_id, 'full' );
		if ( $logo_src ) {
			$schema['logo'] = $logo_src[0];
		}
	}

	$description = get_bloginfo( 'description' );
	if ( $description ) {
		$schema['description'] = $description;
	}

	return $schema;
}

/**
 * BreadcrumbList schema, built from the same items used by the visual
 * breadcrumbs in inc/breadcrumbs.php.
 */
function snailworld_breadcrumb_schema() {
	$items = snailworld_get_breadcrumb_items();
	if ( count( $items ) < 2 ) {
		return null;
	}

	$list_items = array();
	foreach ( $items as $i => $item ) {
		$list_items[] = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $item['label'],
			'item'     => $item['url'] ? esc_url_raw( $item['url'] ) : esc_url_raw( get_permalink() ),
		);
	}

	return array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list_items,
	);
}

/**
 * Article schema for single posts.
 */
function snailworld_article_schema() {
	if ( ! is_singular( 'post' ) ) {
		return null;
	}

	$post_id = get_the_ID();
	$schema  = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'Article',
		'headline'         => get_the_title( $post_id ),
		'datePublished'    => get_the_date( 'c', $post_id ),
		'dateModified'     => get_the_modified_date( 'c', $post_id ),
		'mainEntityOfPage' => array(
			'@type' => 'WebPage',
			'@id'   => get_permalink( $post_id ),
		),
		'author'           => array(
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) ),
		),
		'publisher'        => snailworld_organization_schema(),
	);

	if ( has_post_thumbnail( $post_id ) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
		if ( $image ) {
			$schema['image'] = array( $image[0] );
		}
	}

	$excerpt = get_the_excerpt( $post_id );
	if ( $excerpt ) {
		$schema['description'] = wp_strip_all_tags( $excerpt );
	}

	return $schema;
}

/**
 * Print every applicable schema block in <head>.
 */
function snailworld_print_schema() {
	$blocks = array();

	$org = snailworld_organization_schema();
	if ( $org ) {
		$blocks[] = $org;
	}

	$breadcrumbs = snailworld_breadcrumb_schema();
	if ( $breadcrumbs ) {
		$blocks[] = $breadcrumbs;
	}

	$article = snailworld_article_schema();
	if ( $article ) {
		$blocks[] = $article;
	}

	foreach ( $blocks as $block ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $block ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'snailworld_print_schema', 30 );
