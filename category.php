<?php
/**
 * Category archive: icon + accent color header from the category's
 * garden icon/color set on the Categories admin screen.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$term  = get_queried_object();
$icon  = $term ? snailworld_get_term_icon( $term->term_id ) : 'leaf';
$color = $term ? snailworld_get_term_color( $term->term_id ) : get_theme_mod( 'sw_color_primary', '#52734D' );
$bg    = $term ? snailworld_get_term_color_bg( $term->term_id, 0.15 ) : '';
?>

<main id="primary" class="site-main">
	<div class="sw-content-wrap sw-container">

		<?php snailworld_breadcrumbs(); ?>

		<header class="sw-archive-header sw-reveal" style="--sw-cat-color:<?php echo esc_attr( $color ); ?>;--sw-cat-color-bg:<?php echo esc_attr( $bg ); ?>">
			<span class="sw-cat-icon-wrap"><?php snailworld_icon( $icon ); ?></span>
			<h1><?php single_cat_title(); ?></h1>
			<?php
			$description = category_description();
			if ( $description ) {
				echo wp_kses_post( $description );
			}
			?>
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
