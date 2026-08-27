<?php
/**
 * Homepage hero section.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! get_theme_mod( 'sw_hero_enable', true ) ) {
	return;
}

$image = get_theme_mod( 'sw_hero_image', '' );
?>
<section class="sw-hero">
	<div class="sw-container sw-hero-grid">
		<div class="sw-hero-copy sw-reveal">
			<?php if ( get_theme_mod( 'sw_hero_eyebrow' ) ) : ?>
				<span class="sw-hero-eyebrow">
					<?php snailworld_icon( 'snail-shell', array( 'class' => 'sw-icon' ) ); ?>
					<?php echo esc_html( get_theme_mod( 'sw_hero_eyebrow' ) ); ?>
				</span>
			<?php endif; ?>

			<h1><?php echo esc_html( get_theme_mod( 'sw_hero_heading', __( 'Take it slow. Let the garden lead.', 'snailworld' ) ) ); ?></h1>

			<?php if ( get_theme_mod( 'sw_hero_subheading' ) ) : ?>
				<p class="sw-hero-sub"><?php echo esc_html( get_theme_mod( 'sw_hero_subheading' ) ); ?></p>
			<?php endif; ?>

			<div class="sw-hero-actions">
				<?php if ( get_theme_mod( 'sw_hero_cta_text' ) ) : ?>
					<a class="sw-btn sw-btn-primary" href="<?php echo esc_url( get_theme_mod( 'sw_hero_cta_url', '#' ) ); ?>">
						<?php echo esc_html( get_theme_mod( 'sw_hero_cta_text' ) ); ?>
						<?php snailworld_icon( 'arrow-right', array( 'class' => 'sw-icon' ) ); ?>
					</a>
				<?php endif; ?>
				<a class="sw-btn sw-btn-outline" href="#latest">
					<?php esc_html_e( 'Browse Latest Posts', 'snailworld' ); ?>
				</a>
			</div>
		</div>

		<?php if ( $image ) : ?>
			<div class="sw-hero-media sw-reveal">
				<div class="sw-organic-frame">
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php esc_attr_e( 'Garden scene', 'snailworld' ); ?>" loading="eager" fetchpriority="high" />
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
