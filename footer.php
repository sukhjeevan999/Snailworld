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

			<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) || is_active_sidebar( 'footer-4' ) ) : ?>
				<div class="footer-widgets">
					<?php
					$cols = absint( get_theme_mod( 'sw_footer_columns', 4 ) );
					for ( $i = 1; $i <= $cols; $i++ ) :
						if ( is_active_sidebar( 'footer-' . $i ) ) :
							dynamic_sidebar( 'footer-' . $i );
						endif;
					endfor;
					?>
				</div>
			<?php endif; ?>
		</div>

		<div class="sw-container footer-bottom">
			<p class="footer-copyright"><?php snailworld_copyright_text(); ?></p>

			<nav class="footer-menu" aria-label="<?php esc_attr_e( 'Footer menu', 'snailworld' ); ?>">
				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<?php
					wp_nav_menu( array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-menu-list',
						'fallback_cb'    => false,
						'depth'          => 1,
					) );
					?>
				<?php else : ?>
					<ul class="footer-menu-list">
						<?php
						// No footer menu assigned yet (Appearance → Menus) — list every
						// published page automatically so the footer isn't empty.
						wp_list_pages( array(
							'title_li'    => '',
							'sort_column' => 'menu_order,post_title',
						) );
						?>
					</ul>
				<?php endif; ?>
			</nav>

			<?php snailworld_social_icons(); ?>
		</div>
	</footer>
</div><!-- .site -->

<?php wp_footer(); ?>
</body>
</html>
