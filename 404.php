<?php
/**
 * 404 template.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main">
	<div class="sw-container">
		<div class="sw-404 sw-reveal">
			<?php snailworld_icon( 'snail' ); ?>
			<h1><?php esc_html_e( "404 — This path hasn't been paved yet", 'snailworld' ); ?></h1>
			<p><?php esc_html_e( "The page you were looking for wandered off into the undergrowth. Let's get you back to the garden path.", 'snailworld' ); ?></p>
			<div class="sw-hero-actions" style="justify-content:center;margin-top:1.5rem;">
				<a class="sw-btn sw-btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'snailworld' ); ?></a>
			</div>
			<div style="max-width:420px;margin:2rem auto 0;"><?php get_search_form(); ?></div>
		</div>
	</div>
</main>

<?php
get_footer();
