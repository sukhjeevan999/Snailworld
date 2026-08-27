<?php
/**
 * Homepage template: hero, category grid, latest posts in a garden grid.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main">

	<?php get_template_part( 'template-parts/hero' ); ?>

	<?php if ( get_theme_mod( 'sw_category_grid_enable', true ) ) : ?>
		<section class="sw-section sw-container">
			<div class="sw-section-head">
				<div>
					<span class="sw-section-kicker"><?php esc_html_e( 'Explore', 'snailworld' ); ?></span>
					<h2><?php esc_html_e( 'Browse by Topic', 'snailworld' ); ?></h2>
				</div>
			</div>
			<?php snailworld_category_grid(); ?>
		</section>
		<div class="sw-container"><div class="sw-divider" aria-hidden="true"><?php snailworld_icon( 'leaf-vine' ); ?></div></div>
	<?php endif; ?>

	<section id="latest" class="sw-section sw-container">
		<div class="sw-section-head">
			<div>
				<span class="sw-section-kicker"><?php esc_html_e( 'Fresh from the garden', 'snailworld' ); ?></span>
				<h2><?php esc_html_e( 'Latest Articles', 'snailworld' ); ?></h2>
			</div>
			<?php
			$blog_page_id = (int) get_option( 'page_for_posts' );
			$archive_link = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/' );
			?>
			<a class="sw-btn sw-btn-outline" href="<?php echo esc_url( $archive_link ); ?>">
				<?php esc_html_e( 'View All', 'snailworld' ); ?>
			</a>
		</div>

		<?php
		$count  = absint( get_theme_mod( 'sw_featured_count', 6 ) );
		$latest = new WP_Query( array(
			'post_type'           => 'post',
			'posts_per_page'      => $count ? $count : 6,
			'ignore_sticky_posts' => false,
			'no_found_rows'       => true,
		) );
		?>

		<?php if ( $latest->have_posts() ) : ?>
			<div class="sw-garden-grid">
				<?php
				while ( $latest->have_posts() ) :
					$latest->the_post();
					get_template_part( 'template-parts/post-card' );
				endwhile;
				?>
			</div>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
	</section>

	<?php snailworld_ad_zone( 'in_content_1' ); ?>

</main>

<?php
get_footer();
