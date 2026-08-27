<?php
/**
 * Search form template.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$unique_id = wp_unique_id( 'search-form-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $unique_id ); ?>" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'snailworld' ); ?></label>
	<div style="display:flex;gap:.6rem;">
		<input type="search" id="<?php echo esc_attr( $unique_id ); ?>" class="search-field" placeholder="<?php esc_attr_e( 'Search…', 'snailworld' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
		<button type="submit" class="search-submit sw-btn sw-btn-outline"><?php snailworld_icon( 'search', array( 'class' => 'sw-icon' ) ); ?><span class="screen-reader-text"><?php esc_html_e( 'Search', 'snailworld' ); ?></span></button>
	</div>
</form>
