<?php
/**
 * Comments template.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$count = get_comments_number();
			/* translators: %s: number of comments. */
			printf( esc_html( _n( '%s Comment', '%s Comments', $count, 'snailworld' ) ), esc_html( number_format_i18n( $count ) ) );
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 56,
			) );
			?>
		</ol>

		<?php
		the_comments_pagination( array(
			'prev_text' => esc_html__( '← Older', 'snailworld' ),
			'next_text' => esc_html__( 'Newer →', 'snailworld' ),
		) );
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'snailworld' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form( array(
		'class_submit' => 'sw-btn sw-btn-primary',
		'title_reply'  => __( 'Leave a Reply', 'snailworld' ),
	) );
	?>
</div>
