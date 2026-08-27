<?php
/**
 * Breadcrumbs. Visual output only — the matching BreadcrumbList JSON-LD
 * is emitted by inc/schema-json-ld.php so both stay in sync.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build the ordered list of {label, url} breadcrumb items for the
 * current request. Shared by the visual breadcrumbs and the
 * BreadcrumbList schema.
 */
function snailworld_get_breadcrumb_items() {
	if ( is_front_page() ) {
		return array();
	}

	$items = array(
		array(
			'label' => __( 'Home', 'snailworld' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( is_singular( 'post' ) ) {
		$cat = snailworld_get_primary_category( get_the_ID() );
		if ( $cat ) {
			$items[] = array( 'label' => $cat->name, 'url' => get_category_link( $cat->term_id ) );
		}
		$items[] = array( 'label' => get_the_title(), 'url' => get_permalink() );
	} elseif ( is_category() ) {
		$items[] = array( 'label' => single_cat_title( '', false ), 'url' => '' );
	} elseif ( is_tag() ) {
		$items[] = array( 'label' => single_tag_title( '', false ), 'url' => '' );
	} elseif ( is_search() ) {
		/* translators: %s: search query. */
		$items[] = array( 'label' => sprintf( __( 'Search results for "%s"', 'snailworld' ), get_search_query() ), 'url' => '' );
	} elseif ( is_404() ) {
		$items[] = array( 'label' => __( 'Page not found', 'snailworld' ), 'url' => '' );
	} elseif ( is_page() ) {
		$items[] = array( 'label' => get_the_title(), 'url' => get_permalink() );
	} elseif ( is_author() ) {
		$items[] = array( 'label' => get_the_author(), 'url' => '' );
	} elseif ( is_archive() ) {
		$items[] = array( 'label' => get_the_archive_title(), 'url' => '' );
	}

	return $items;
}

function snailworld_breadcrumbs() {
	$items = snailworld_get_breadcrumb_items();
	if ( count( $items ) < 2 ) {
		return;
	}

	echo '<nav class="sw-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'snailworld' ) . '">';
	$total = count( $items );
	foreach ( $items as $i => $item ) {
		$is_last = ( $i === $total - 1 );
		if ( $i > 0 ) {
			echo '<span class="sep" aria-hidden="true">/</span>';
		}
		if ( $is_last || empty( $item['url'] ) ) {
			echo '<span aria-current="page">' . esc_html( $item['label'] ) . '</span>';
		} else {
			echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
		}
	}
	echo '</nav>';
}
