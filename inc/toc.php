<?php
/**
 * Auto table-of-contents generated from H2/H3 headings in post content.
 * Adds matching id="" anchors to headings in the rendered content, and
 * exposes snailworld_get_toc_items() for the sticky sidebar TOC.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build a unique, readable slug for a heading, tracking collisions.
 */
function snailworld_toc_slugify( $text, &$used ) {
	$slug = sanitize_title( wp_strip_all_tags( $text ) );
	if ( ! $slug ) {
		$slug = 'section';
	}
	$base = $slug;
	$i    = 2;
	while ( isset( $used[ $slug ] ) ) {
		$slug = $base . '-' . $i;
		$i++;
	}
	$used[ $slug ] = true;
	return $slug;
}

/**
 * Extract [{level, text, id}] headings from raw post content.
 */
function snailworld_get_toc_items( $post_id ) {
	if ( ! get_theme_mod( 'sw_enable_toc', true ) ) {
		return array();
	}
	$content = get_post_field( 'post_content', $post_id );
	if ( ! $content || ! preg_match_all( '/<h([23])[^>]*>(.*?)<\/h\1>/is', $content, $matches, PREG_SET_ORDER ) ) {
		return array();
	}

	$used  = array();
	$items = array();
	foreach ( $matches as $m ) {
		$text = trim( wp_strip_all_tags( $m[2] ) );
		if ( ! $text ) {
			continue;
		}
		$items[] = array(
			'level' => (int) $m[1],
			'text'  => $text,
			'id'    => snailworld_toc_slugify( $text, $used ),
		);
	}
	return $items;
}

/**
 * Inject id="" attributes into H2/H3 tags in the rendered content so they
 * match the slugs produced by snailworld_get_toc_items() (same order/logic).
 */
function snailworld_add_heading_ids( $content ) {
	if ( is_admin() || ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	if ( ! get_theme_mod( 'sw_enable_toc', true ) ) {
		return $content;
	}

	$used = array();
	return preg_replace_callback(
		'/<h([23])([^>]*)>(.*?)<\/h\1>/is',
		function ( $m ) use ( &$used ) {
			$level = $m[1];
			$attrs = $m[2];
			$inner = $m[3];
			$text  = trim( wp_strip_all_tags( $inner ) );
			if ( ! $text ) {
				return $m[0];
			}
			$id = snailworld_toc_slugify( $text, $used );
			if ( preg_match( '/\bid=/', $attrs ) ) {
				return $m[0]; // Respect an existing manual id.
			}
			return '<h' . $level . $attrs . ' id="' . esc_attr( $id ) . '">' . $inner . '</h' . $level . '>';
		},
		$content
	);
}
add_filter( 'the_content', 'snailworld_add_heading_ids', 15 );

/**
 * Render the TOC block (used in template-parts/toc.php).
 */
function snailworld_render_toc() {
	$items = snailworld_get_toc_items( get_the_ID() );
	if ( count( $items ) < 2 ) {
		return;
	}
	?>
	<details class="sw-toc sw-toc-sidebar sw-reveal" open>
		<summary class="sw-toc-title">
			<span><?php esc_html_e( 'Table of Contents', 'snailworld' ); ?></span>
			<?php snailworld_icon( 'chevron-down', array( 'class' => 'sw-icon sw-icon-chevron' ) ); ?>
		</summary>
		<ol>
			<?php foreach ( $items as $item ) : ?>
				<li class="<?php echo 3 === $item['level'] ? 'toc-sub' : ''; ?>">
					<a href="#<?php echo esc_attr( $item['id'] ); ?>" data-toc-target="<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['text'] ); ?></a>
				</li>
			<?php endforeach; ?>
		</ol>
	</details>
	<?php
}
