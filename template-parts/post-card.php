<?php
/**
 * Post card: used in garden-grid / post-grid loops (home, archive,
 * category, search).
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'sw-card sw-reveal' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="sw-card-media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'snailworld-card', array( 'loading' => 'lazy', 'alt' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
		</a>
	<?php endif; ?>

	<div class="sw-card-body">
		<?php snailworld_category_badge(); ?>

		<h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

		<div class="sw-card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></div>

		<div class="sw-card-meta">
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			<span aria-hidden="true">·</span>
			<span>
				<?php
				/* translators: %d: reading time in minutes. */
				echo esc_html( sprintf( _n( '%d min read', '%d min read', snailworld_reading_time( get_the_ID() ), 'snailworld' ), snailworld_reading_time( get_the_ID() ) ) );
				?>
			</span>
		</div>
	</div>
</article>
