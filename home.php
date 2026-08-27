<?php
/**
 * Blog index (posts page). Used when a static front page is set and a
 * separate "Posts page" is assigned in Settings → Reading.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main">
	<div class="sw-content-wrap sw-container">

		<?php snailworld_breadcrumbs(); ?>

		<header class="sw-archive-header sw-reveal">
			<h1><?php single_post_title(); ?></h1>
		</header>

		<div class="sw-layout has-sidebar">
			<div class="sw-layout-main">
				<?php if ( have_posts() ) : ?>
					<div class="sw-post-grid">
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/post-card' );
						endwhile;
						?>
					</div>
					<?php snailworld_pagination(); ?>
				<?php else : ?>
					<?php get_template_part( 'template-parts/content', 'none' ); ?>
				<?php endif; ?>
			</div>
			<?php get_sidebar(); ?>
		</div>
	</div>
</main>

<?php
get_footer();
