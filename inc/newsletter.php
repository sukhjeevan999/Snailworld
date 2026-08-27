<?php
/**
 * Self-contained newsletter signup: no third-party service required.
 * Submissions are stored as a private "Newsletter Subscribers" post
 * type (visible in wp-admin) and the site admin gets an email for
 * each new signup. If the site owner later pastes a real embed code
 * from Mailchimp/Brevo/etc into Customize > Newsletter, that takes
 * over instead (see snailworld_newsletter_block() in template-tags.php).
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Private post type used purely as simple subscriber storage — shows
 * up in wp-admin as "Newsletter Subscribers" (email as the title, the
 * post date is the signup date).
 */
function snailworld_register_subscriber_cpt() {
	register_post_type( 'sw_subscriber', array(
		'label'           => __( 'Newsletter Subscribers', 'snailworld' ),
		'labels'          => array(
			'name'          => __( 'Newsletter Subscribers', 'snailworld' ),
			'singular_name' => __( 'Subscriber', 'snailworld' ),
			'all_items'     => __( 'Subscribers', 'snailworld' ),
		),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_icon'       => 'dashicons-email-alt',
		'menu_position'   => 26,
		'supports'        => array( 'title' ),
		'capability_type' => 'page',
		'map_meta_cap'    => true,
	) );
}
add_action( 'init', 'snailworld_register_subscriber_cpt' );

/**
 * Render the built-in signup form (used when no external embed code
 * has been pasted into the Customizer).
 */
function snailworld_render_builtin_newsletter_form() {
	$status = isset( $_GET['sw_newsletter'] ) ? sanitize_key( wp_unslash( $_GET['sw_newsletter'] ) ) : '';

	if ( 'success' === $status ) {
		echo '<p class="sw-newsletter-message is-success">' . esc_html__( "Thanks — you're on the list!", 'snailworld' ) . '</p>';
		return;
	}
	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="snailworld_newsletter_signup">
		<input type="hidden" name="redirect_to" value="<?php echo esc_url( home_url( add_query_arg( null, null ) ) ); ?>">
		<?php wp_nonce_field( 'snailworld_newsletter_signup', 'snailworld_newsletter_nonce' ); ?>
		<label class="screen-reader-text" for="sw-newsletter-email"><?php esc_html_e( 'Email address', 'snailworld' ); ?></label>
		<input type="email" id="sw-newsletter-email" name="sw_newsletter_email" placeholder="<?php esc_attr_e( 'you@example.com', 'snailworld' ); ?>" required>
		<button type="submit"><?php esc_html_e( 'Subscribe', 'snailworld' ); ?></button>
	</form>
	<?php if ( 'error' === $status ) : ?>
		<p class="sw-newsletter-message is-error"><?php esc_html_e( 'Please enter a valid email address.', 'snailworld' ); ?></p>
	<?php endif; ?>
	<?php
}

/**
 * Handle the signup POST (both logged-in and logged-out visitors).
 */
function snailworld_handle_newsletter_signup() {
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );

	$nonce_ok = isset( $_POST['snailworld_newsletter_nonce'] )
		&& wp_verify_nonce( sanitize_key( wp_unslash( $_POST['snailworld_newsletter_nonce'] ) ), 'snailworld_newsletter_signup' );

	$email = isset( $_POST['sw_newsletter_email'] ) ? sanitize_email( wp_unslash( $_POST['sw_newsletter_email'] ) ) : '';

	if ( ! $nonce_ok || ! is_email( $email ) ) {
		wp_safe_redirect( esc_url_raw( add_query_arg( 'sw_newsletter', 'error', $redirect ) ) );
		exit;
	}

	$existing = get_posts( array(
		'post_type'      => 'sw_subscriber',
		'post_status'    => 'publish',
		'meta_key'       => 'sw_subscriber_email',
		'meta_value'     => $email,
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	) );

	if ( empty( $existing ) ) {
		$post_id = wp_insert_post( array(
			'post_type'   => 'sw_subscriber',
			'post_title'  => $email,
			'post_status' => 'publish',
		) );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, 'sw_subscriber_email', $email );

			wp_mail(
				get_option( 'admin_email' ),
				sprintf(
					/* translators: %s: site name. */
					__( 'New newsletter signup on %s', 'snailworld' ),
					get_bloginfo( 'name' )
				),
				sprintf(
					/* translators: %s: subscriber email address. */
					__( "%s just subscribed to your newsletter.\n\nView all subscribers in wp-admin under \"Subscribers\".", 'snailworld' ),
					$email
				)
			);
		}
	}

	wp_safe_redirect( esc_url_raw( add_query_arg( 'sw_newsletter', 'success', $redirect ) ) );
	exit;
}
add_action( 'admin_post_snailworld_newsletter_signup', 'snailworld_handle_newsletter_signup' );
add_action( 'admin_post_nopriv_snailworld_newsletter_signup', 'snailworld_handle_newsletter_signup' );
