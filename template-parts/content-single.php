<?php
/**
 * Single post content.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'sw-reveal' ); ?>>
	<header class="entry-header">
		<?php snailworld_category_badge(); ?>
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php snailworld_entry_meta(); ?>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="entry-thumbnail sw-organic-frame">
			<?php the_post_thumbnail( 'snailworld-featured', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
		</div>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'snailworld' ),
			'after'  => '</div>',
		) );
		?>
	</div>

	<footer class="entry-footer">
		<?php
		$tags = get_the_tags();
		if ( $tags ) :
			foreach ( $tags as $tag ) :
				?>
				<a class="sw-tag" href="<?php echo esc_url( get_tag_link( $tag ) ); ?>">#<?php echo esc_html( $tag->name ); ?></a>
				<?php
			endforeach;
		endif;
		?>
	</footer>

	<?php snailworld_author_box(); ?>
	<?php snailworld_post_navigation(); ?>
</article>
