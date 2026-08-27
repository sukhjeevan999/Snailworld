<?php
/**
 * Reusable template helper functions.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Entry meta: date, author, reading time.
 */
function snailworld_entry_meta() {
	$reading_time = snailworld_reading_time( get_the_ID() );
	?>
	<div class="entry-meta">
		<span class="meta-date">
			<?php snailworld_icon( 'calendar', array( 'class' => 'sw-icon' ) ); ?>
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</span>
		<span class="meta-author">
			<?php snailworld_icon( 'user', array( 'class' => 'sw-icon' ) ); ?>
			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
		</span>
		<span class="meta-reading-time">
			<?php snailworld_icon( 'clock', array( 'class' => 'sw-icon' ) ); ?>
			<?php
			/* translators: %d: reading time in minutes. */
			echo esc_html( sprintf( _n( '%d min read', '%d min read', $reading_time, 'snailworld' ), $reading_time ) );
			?>
		</span>
	</div>
	<?php
}

/**
 * Estimate reading time (words / 200 wpm).
 */
function snailworld_reading_time( $post_id ) {
	$content = get_post_field( 'post_content', $post_id );
	$words   = str_word_count( wp_strip_all_tags( $content ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Numeric pagination for archive/home loops.
 */
function snailworld_pagination() {
	$big   = 999999999;
	$links = paginate_links( array(
		'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'current'   => max( 1, get_query_var( 'paged' ) ),
		'total'     => $GLOBALS['wp_query']->max_num_pages,
		'mid_size'  => 1,
		'prev_text' => snailworld_icon( 'arrow-left', array( 'echo' => false ) ) . '<span class="screen-reader-text">' . esc_html__( 'Previous', 'snailworld' ) . '</span>',
		'next_text' => snailworld_icon( 'arrow-right', array( 'echo' => false ) ) . '<span class="screen-reader-text">' . esc_html__( 'Next', 'snailworld' ) . '</span>',
		'type'      => 'array',
	) );

	if ( empty( $links ) ) {
		return;
	}
	echo '<nav class="sw-pagination" aria-label="' . esc_attr__( 'Posts pagination', 'snailworld' ) . '">';
	foreach ( $links as $link ) {
		echo wp_kses_post( $link );
	}
	echo '</nav>';
}

/**
 * Prev/next single post navigation.
 */
function snailworld_post_navigation() {
	$prev = get_previous_post();
	$next = get_next_post();
	if ( ! $prev && ! $next ) {
		return;
	}
	?>
	<nav class="sw-post-nav" aria-label="<?php esc_attr_e( 'Post navigation', 'snailworld' ); ?>">
		<?php if ( $prev ) : ?>
			<a class="nav-prev" href="<?php echo esc_url( get_permalink( $prev ) ); ?>">
				<span class="nav-label"><?php esc_html_e( '← Previous', 'snailworld' ); ?></span>
				<span class="nav-title"><?php echo esc_html( get_the_title( $prev ) ); ?></span>
			</a>
		<?php else : ?>
			<span></span>
		<?php endif; ?>
		<?php if ( $next ) : ?>
			<a class="nav-next" href="<?php echo esc_url( get_permalink( $next ) ); ?>">
				<span class="nav-label"><?php esc_html_e( 'Next →', 'snailworld' ); ?></span>
				<span class="nav-title"><?php echo esc_html( get_the_title( $next ) ); ?></span>
			</a>
		<?php endif; ?>
	</nav>
	<?php
}

/**
 * Author box under single posts.
 */
function snailworld_author_box() {
	$user_id = get_the_author_meta( 'ID' );
	$bio     = get_the_author_meta( 'description' );
	?>
	<div class="sw-author-box">
		<?php echo get_avatar( $user_id, 64 ); ?>
		<div>
			<h3><?php the_author(); ?></h3>
			<?php if ( $bio ) : ?>
				<p><?php echo esc_html( $bio ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Homepage category icon grid.
 */
function snailworld_category_grid() {
	$cats = get_categories( array(
		'orderby'    => 'count',
		'order'      => 'DESC',
		'hide_empty' => true,
		'number'     => 8,
	) );
	if ( empty( $cats ) ) {
		return;
	}
	echo '<div class="sw-category-grid">';
	foreach ( $cats as $cat ) {
		$icon  = snailworld_get_term_icon( $cat->term_id );
		$color = snailworld_get_term_color( $cat->term_id );
		printf(
			'<a href="%1$s" class="sw-category-pill sw-reveal" style="--sw-cat-color:%2$s"><span class="sw-cat-icon-wrap">%3$s</span><span class="cat-name">%4$s</span><span class="cat-count">%5$s</span></a>',
			esc_url( get_category_link( $cat->term_id ) ),
			esc_attr( $color ),
			snailworld_icon( $icon, array( 'echo' => false ) ),
			esc_html( $cat->name ),
			/* translators: %d: number of posts. */
			esc_html( sprintf( _n( '%d post', '%d posts', $cat->count, 'snailworld' ), $cat->count ) )
		);
	}
	echo '</div>';
}

/**
 * Footer social icon links, only for the ones filled in.
 */
function snailworld_social_icons() {
	$socials = array( 'facebook', 'instagram', 'twitter', 'youtube', 'pinterest' );
	$labels  = array(
		'facebook'  => 'FB',
		'instagram' => 'IG',
		'twitter'   => 'X',
		'youtube'   => 'YT',
		'pinterest' => 'PT',
	);
	$output = '';
	foreach ( $socials as $key ) {
		$url = get_theme_mod( 'sw_social_' . $key );
		if ( ! $url ) {
			continue;
		}
		$output .= sprintf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s">%3$s</a>',
			esc_url( $url ),
			esc_attr( ucfirst( $key ) ),
			esc_html( $labels[ $key ] )
		);
	}
	if ( $output ) {
		echo '<div class="footer-social">' . $output . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Footer copyright text with [year] token replaced.
 */
function snailworld_copyright_text() {
	$text = get_theme_mod( 'sw_footer_copyright', sprintf( __( '© %s SnailWorld. All rights reserved.', 'snailworld' ), '[year]' ) );
	echo esc_html( str_replace( '[year]', gmdate( 'Y' ), $text ) );
}
