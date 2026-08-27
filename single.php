<?php
/**
 * Single post template.
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

		<div class="sw-layout has-sidebar">
			<div class="sw-layout-main">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'single' );

					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
				endwhile;
				?>
			</div>

			<div class="sw-layout-aside">
				<div class="sw-toc-sidebar">
					<?php get_template_part( 'template-parts/toc' ); ?>
					<?php get_sidebar(); ?>
				</div>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
