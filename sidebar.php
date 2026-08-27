<?php
/**
 * Blog sidebar: ad zone + widgets.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_active_sidebar( 'sidebar-primary' ) && ! snailworld_ad_zone_enabled( 'sidebar' ) ) {
	return;
}
?>
<aside id="secondary" class="widget-area" aria-label="<?php esc_attr_e( 'Sidebar', 'snailworld' ); ?>">
	<?php snailworld_ad_zone( 'sidebar' ); ?>
	<?php dynamic_sidebar( 'sidebar-primary' ); ?>
</aside>
