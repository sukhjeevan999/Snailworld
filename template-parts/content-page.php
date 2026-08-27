<?php
/**
 * Static page content.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'sw-reveal' ); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="entry-thumbnail sw-organic-frame">
			<?php the_post_thumbnail( 'snailworld-featured', array( 'loading' => 'eager' ) ); ?>
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
</article>
