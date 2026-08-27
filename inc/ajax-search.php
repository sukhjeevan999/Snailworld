<?php
/**
 * Lightweight REST endpoint for the AJAX live search box, returning
 * garden/snail-icon-ready result cards (title, excerpt, url, category
 * icon + color) in one request instead of round-tripping per result.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function snailworld_register_search_route() {
	register_rest_route(
		'snailworld/v1',
		'/search',
		array(
			'methods'             => 'GET',
			'callback'            => 'snailworld_search_endpoint',
			'permission_callback' => '__return_true',
			'args'                => array(
				'q' => array(
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'snailworld_register_search_route' );

function snailworld_search_endpoint( WP_REST_Request $request ) {
	$term = trim( (string) $request->get_param( 'q' ) );

	if ( '' === $term || mb_strlen( $term ) < 2 ) {
		return new WP_REST_Response( array(), 200 );
	}

	$query = new WP_Query( array(
		's'                   => $term,
		'post_type'           => array( 'post', 'page' ),
		'post_status'         => 'publish',
		'posts_per_page'      => 8,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	) );

	$results = array();

	while ( $query->have_posts() ) {
		$query->the_post();
		$post_id = get_the_ID();
		$icon    = 'leaf';
		$color   = get_theme_mod( 'sw_color_primary', '#52734D' );

		if ( 'post' === get_post_type() ) {
			$cat = snailworld_get_primary_category( $post_id );
			if ( $cat ) {
				$icon  = snailworld_get_term_icon( $cat->term_id );
				$color = snailworld_get_term_color( $cat->term_id );
			}
		} else {
			$icon = 'pot';
		}

		$results[] = array(
			'id'      => $post_id,
			'title'   => get_the_title(),
			'url'     => get_permalink(),
			'excerpt' => wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 14 ),
			'icon'    => $icon,
			'color'   => $color,
			'type'    => get_post_type(),
		);
	}
	wp_reset_postdata();

	return new WP_REST_Response( $results, 200 );
}
