<?php
/**
 * Header template: site branding, primary nav, header actions, mobile nav.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'snailworld' ); ?></a>

<?php snailworld_ad_zone( 'header' ); ?>

<?php if ( is_singular() ) : ?>
	<div class="sw-progress-bar" data-sw-progress>
		<div class="sw-progress-track"></div>
		<div class="sw-progress-fill" data-sw-progress-fill></div>
		<div class="sw-progress-snail" data-sw-progress-snail>
			<?php snailworld_icon( 'snail', array( 'class' => 'sw-icon' ) ); ?>
		</div>
	</div>
<?php endif; ?>

<header id="masthead" class="site-header <?php echo get_theme_mod( 'sw_header_sticky', true ) ? 'is-sticky' : ''; ?>">
	<div class="sw-container site-header-inner">

		<div class="site-branding">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			}
			if ( is_front_page() && is_home() ) :
				?>
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php
			else :
				?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php
			endif;
			$description = get_bloginfo( 'description', 'display' );
			if ( $description ) :
				?>
				<p class="site-description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>

		<nav id="primary-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'snailworld' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'primary-menu',
				'fallback_cb'    => false,
			) );
			?>
		</nav>

		<div class="header-actions">
			<?php if ( get_theme_mod( 'sw_enable_live_search', true ) ) : ?>
				<button type="button" class="sw-theme-toggle sw-search-toggle" aria-label="<?php esc_attr_e( 'Open search', 'snailworld' ); ?>" data-sw-search-open>
					<?php snailworld_icon( 'search' ); ?>
				</button>
			<?php endif; ?>

			<?php if ( get_theme_mod( 'sw_enable_dark_toggle', true ) ) : ?>
				<button type="button" class="sw-theme-toggle" aria-label="<?php esc_attr_e( 'Toggle dark mode', 'snailworld' ); ?>" data-sw-theme-toggle>
					<?php snailworld_icon( 'sun', array( 'class' => 'sw-icon sw-icon-sun' ) ); ?>
					<?php snailworld_icon( 'moon', array( 'class' => 'sw-icon sw-icon-moon' ) ); ?>
				</button>
			<?php endif; ?>

			<?php if ( get_theme_mod( 'sw_header_cta_enable', true ) && get_theme_mod( 'sw_header_cta_text' ) ) : ?>
				<a class="sw-btn sw-btn-primary" href="<?php echo esc_url( get_theme_mod( 'sw_header_cta_url', '#' ) ); ?>">
					<?php echo esc_html( get_theme_mod( 'sw_header_cta_text' ) ); ?>
				</a>
			<?php endif; ?>

			<button type="button" class="sw-menu-toggle" aria-label="<?php esc_attr_e( 'Toggle menu', 'snailworld' ); ?>" aria-expanded="false" aria-controls="mobile-navigation" data-sw-menu-toggle>
				<span></span><span></span><span></span>
			</button>
		</div>
	</div>

	<nav id="mobile-navigation" class="mobile-navigation" aria-label="<?php esc_attr_e( 'Mobile menu', 'snailworld' ); ?>">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'mobile-menu',
			'fallback_cb'    => false,
		) );
		?>
		<?php if ( get_theme_mod( 'sw_header_cta_enable', true ) && get_theme_mod( 'sw_header_cta_text' ) ) : ?>
			<p style="margin-top:1.5rem;">
				<a class="sw-btn sw-btn-primary" href="<?php echo esc_url( get_theme_mod( 'sw_header_cta_url', '#' ) ); ?>">
					<?php echo esc_html( get_theme_mod( 'sw_header_cta_text' ) ); ?>
				</a>
			</p>
		<?php endif; ?>
	</nav>
</header>

<?php if ( get_theme_mod( 'sw_enable_live_search', true ) ) : ?>
	<div class="sw-search-panel" data-sw-search-panel>
		<div class="sw-search-box">
			<button type="button" class="sw-btn sw-btn-ghost sw-search-close" aria-label="<?php esc_attr_e( 'Close search', 'snailworld' ); ?>" data-sw-search-close>
				<?php snailworld_icon( 'close' ); ?>
			</button>
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-sw-search-form>
				<?php snailworld_icon( 'search', array( 'class' => 'sw-icon' ) ); ?>
				<input type="search" name="s" placeholder="<?php esc_attr_e( 'Search garden guides, care tips…', 'snailworld' ); ?>" autocomplete="off" data-sw-search-input>
				<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'snailworld' ); ?>"><?php snailworld_icon( 'arrow-right', array( 'class' => 'sw-icon' ) ); ?></button>
			</form>
			<div class="sw-search-results" data-sw-search-results aria-live="polite"></div>
		</div>
	</div>
<?php endif; ?>

<button type="button" class="sw-back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'snailworld' ); ?>" data-sw-scroll-top>
	<?php snailworld_icon( 'arrow-right', array( 'class' => 'sw-icon sw-icon-rotate-up' ) ); ?>
</button>

<div id="page" class="site">
