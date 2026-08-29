<?php
/**
 * Footer template: ad zone, widget columns, social icons, footer menu.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	<?php snailworld_ad_zone( 'footer' ); ?>

	<footer id="colophon" class="site-footer">
		<div class="sw-container">
			<div class="sw-divider" aria-hidden="true">
				<?php snailworld_icon( 'leaf-vine' ); ?>
			</div>

			<?php $footer_pages = get_theme_mod( 'sw_footer_links_enable', true ) ? get_pages( array( 'sort_column' => 'menu_order,post_title' ) ) : array(); ?>

			<!-- Order: Newsletter, Social, Quick Links, About — 4 columns on
			     desktop, stacked one-per-row on mobile. -->
			<div class="footer-widgets">
				<?php snailworld_newsletter_block(); ?>

				<?php if ( snailworld_has_social_links() ) : ?>
					<div class="widget sw-footer-social">
						<h2 class="widget-title"><?php esc_html_e( 'Follow Us', 'snailworld' ); ?></h2>
						<?php snailworld_social_icons(); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $footer_pages ) ) : ?>
					<div class="widget sw-footer-links">
						<h2 class="widget-title"><?php esc_html_e( 'Quick Links', 'snailworld' ); ?></h2>
						<ul>
							<?php foreach ( $footer_pages as $page ) : ?>
								<li><a href="<?php echo esc_url( get_permalink( $page ) ); ?>"><?php echo esc_html( get_the_title( $page ) ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<div class="widget sw-footer-about">
					<h2 class="widget-title"><?php echo esc_html( get_theme_mod( 'sw_footer_about_heading', __( 'About', 'snailworld' ) ) ); ?></h2>
					<p><?php echo esc_html( get_theme_mod( 'sw_footer_about_text' ) ); ?></p>
				</div>

				<?php
				// Any widgets the site owner drags into Appearance → Widgets'
				// Footer Column areas stack in as extra columns.
				for ( $i = 1; $i <= 4; $i++ ) :
					if ( is_active_sidebar( 'footer-' . $i ) ) :
						dynamic_sidebar( 'footer-' . $i );
					endif;
				endfor;
				?>
			</div>
		</div>

		<div class="sw-container footer-bottom">
			<p class="footer-copyright"><?php snailworld_copyright_text(); ?></p>
		</div>
	</footer>
</div><!-- .site -->

<?php wp_footer(); ?>
</body>
</html>
