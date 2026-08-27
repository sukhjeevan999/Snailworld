<?php
/**
 * Per-category garden/snail icon + accent color, stored as term meta.
 * Shown in archive headers, post cards, and the homepage category grid.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add fields to the "Add New Category" screen.
 */
function snailworld_category_add_fields() {
	$icons = snailworld_category_icon_choices();
	?>
	<div class="form-field">
		<label for="sw-cat-icon"><?php esc_html_e( 'Garden Icon', 'snailworld' ); ?></label>
		<select name="sw_cat_icon" id="sw-cat-icon">
			<option value=""><?php esc_html_e( '— None —', 'snailworld' ); ?></option>
			<?php foreach ( $icons as $key => $label ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<p><?php esc_html_e( 'Shown on archive headers, post cards and the homepage category grid.', 'snailworld' ); ?></p>
	</div>
	<div class="form-field">
		<label for="sw-cat-color"><?php esc_html_e( 'Accent Color', 'snailworld' ); ?></label>
		<input type="text" name="sw_cat_color" id="sw-cat-color" class="sw-color-field" value="" data-default-color="#52734D" />
	</div>
	<?php
}
add_action( 'category_add_form_fields', 'snailworld_category_add_fields' );

/**
 * Add fields to the "Edit Category" screen.
 */
function snailworld_category_edit_fields( $term ) {
	$icons = snailworld_category_icon_choices();
	$icon  = get_term_meta( $term->term_id, 'sw_cat_icon', true );
	$color = get_term_meta( $term->term_id, 'sw_cat_color', true );
	?>
	<tr class="form-field">
		<th scope="row"><label for="sw-cat-icon"><?php esc_html_e( 'Garden Icon', 'snailworld' ); ?></label></th>
		<td>
			<select name="sw_cat_icon" id="sw-cat-icon">
				<option value=""><?php esc_html_e( '— None —', 'snailworld' ); ?></option>
				<?php foreach ( $icons as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $icon, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<p class="description"><?php esc_html_e( 'Shown on archive headers, post cards and the homepage category grid.', 'snailworld' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="sw-cat-color"><?php esc_html_e( 'Accent Color', 'snailworld' ); ?></label></th>
		<td><input type="text" name="sw_cat_color" id="sw-cat-color" class="sw-color-field" value="<?php echo esc_attr( $color ); ?>" data-default-color="#52734D" /></td>
	</tr>
	<?php
}
add_action( 'category_edit_form_fields', 'snailworld_category_edit_fields' );

/**
 * Save term meta.
 */
function snailworld_save_category_meta( $term_id ) {
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	if ( isset( $_POST['sw_cat_icon'] ) ) {
		$icons = array_keys( snailworld_category_icon_choices() );
		$icon  = sanitize_key( wp_unslash( $_POST['sw_cat_icon'] ) );
		if ( in_array( $icon, $icons, true ) ) {
			update_term_meta( $term_id, 'sw_cat_icon', $icon );
		} else {
			delete_term_meta( $term_id, 'sw_cat_icon' );
		}
	}
	if ( isset( $_POST['sw_cat_color'] ) ) {
		$color = sanitize_hex_color( wp_unslash( $_POST['sw_cat_color'] ) );
		if ( $color ) {
			update_term_meta( $term_id, 'sw_cat_color', $color );
		} else {
			delete_term_meta( $term_id, 'sw_cat_color' );
		}
	}
}
add_action( 'created_category', 'snailworld_save_category_meta' );
add_action( 'edited_category', 'snailworld_save_category_meta' );

/**
 * Color picker script on the category admin screens.
 */
function snailworld_category_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'edit-tags.php', 'term.php' ), true ) ) {
		return;
	}
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	wp_add_inline_script( 'wp-color-picker', 'jQuery(function($){ $(".sw-color-field").wpColorPicker(); });' );
}
add_action( 'admin_enqueue_scripts', 'snailworld_category_admin_assets' );

/**
 * Helpers: get icon key / color / label for a term (with graceful fallback).
 */
function snailworld_get_term_icon( $term_id ) {
	$icon = get_term_meta( $term_id, 'sw_cat_icon', true );
	return $icon ? $icon : 'leaf';
}

function snailworld_get_term_color( $term_id ) {
	$color = get_term_meta( $term_id, 'sw_cat_color', true );
	return $color ? $color : get_theme_mod( 'sw_color_primary', '#52734D' );
}

/**
 * Get the "primary" category for a post (first assigned category).
 */
function snailworld_get_primary_category( $post_id ) {
	$cats = get_the_category( $post_id );
	return ! empty( $cats ) ? $cats[0] : null;
}

/**
 * Render a small category tag/badge (icon + name) with its accent color.
 */
function snailworld_category_badge( $post_id = null, $echo = true ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$cat     = snailworld_get_primary_category( $post_id );
	if ( ! $cat ) {
		return '';
	}
	$icon  = snailworld_get_term_icon( $cat->term_id );
	$color = snailworld_get_term_color( $cat->term_id );

	$html = sprintf(
		'<a href="%1$s" class="sw-cat-tag" style="--sw-cat-color:%2$s">%3$s<span>%4$s</span></a>',
		esc_url( get_category_link( $cat->term_id ) ),
		esc_attr( $color ),
		snailworld_icon( $icon, array( 'echo' => false ) ),
		esc_html( $cat->name )
	);

	if ( $echo ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}
	return $html;
}
